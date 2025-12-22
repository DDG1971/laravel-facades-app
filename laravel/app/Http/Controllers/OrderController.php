<?php

namespace App\Http\Controllers;

use App\Models\Milling;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FacadeType;
use App\Models\ColorCode;
use App\Models\ColorCatalog;
use App\Models\CoatingType;
use App\Models\Drilling;


class OrderController extends Controller
{
    /**
     * Список заказов (для клиента или менеджера).
     */
    public function index()
    {
        $orders = Order::with('customer', 'status')->get();
        return view('orders.index', compact('orders'));
    }

    /**
     * Форма создания нового заказа.
     */
    public function create()
    {
        $customers     = Customer::all();
        $statuses      = Status::all();
        $facadeTypes   = FacadeType::all();
        $colors        = ColorCode::all();
        $colorCatalogs = ColorCatalog::all();
        $coatingTypes  = CoatingType::all();
        $drillings     = Drilling::all();
        $millings      = Milling::all();   // твоя модель сверловки

        return view('orders.create', compact(
            'customers',
            'statuses',
            'facadeTypes',
            'colors',
            'colorCatalogs',
            'coatingTypes',
            'drillings',
            'millings'
        ));
    }

    /**
     * Сохранение нового заказа и его позиций.
     */
    public function store(Request $request)
    {
        // общий счётчик (очередь)
        $lastQueue = Order::max('queue_number') ?? 0;

        // создаём заказ
        $order = Order::create([
            'customer_id'        => $request->customer_id,          // клиент, для которого создаётся заказ
            'user_id'            => Auth::id(),                     // кто реально создал заказ (client/admin/manager)
            'status_id'          => Status::where('name', 'new')->first()->id,
            'date_received'      => now(),
            'queue_number'       => $lastQueue + 1,                 // внутренняя очередь
            'client_order_number'=> $request->client_order_number,  // № заказа клиента
            'material'           => $request->material,             // общий материал заказа


        ]);

        // вложение для заказа
        if ($request->hasFile('order_attachment')) {
            $path = $request->file('order_attachment')->store('orders/attachments', 'public');
            $order->attachment_path = $path;
            $order->save();
        }

        // добавляем позиции (без material!)
        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id'            => $order->id,
                'facade_type_id'      => $item['facade_type_id'],
                'milling_id'          => $item['milling_id'] ?? null,
                'coating_type_id'     => $item['coating_type_id'] ?? null,
                'color_catalog_id'    => $item['color_catalog_id'] ?? null,
                'color_code_id'       => $item['color_code_id'] ?? null,
                'thickness'           => $item['thickness'],
                'height'              => $item['height'],
                'width'               => $item['width'],
                'square_meters'       => $item['square_meters'] ?? null,
                'quantity'            => $item['quantity'],
                'double_sided_coating'=> $item['double_sided_coating'] ?? false,
                'drilling_id'         => $item['drilling_id'] ?? null,
                'notes'               => $item['notes'] ?? null,
                'unit_price'          => $item['unit_price'] ?? 0,
                'total_price'         => $item['total_price'] ?? 0,
                'status_id'           => $item['status_id'] ?? Status::where('name','new')->first()->id,
                'date_status'         => now(),
                'date_created'        => now(),
                'attachment_path'     => isset($item['attachment_path'])
                    ? $item['attachment_path']->store('order_items/attachments', 'public')
                    : null,
            ]);
        }
        // пересчёт итогов заказа
        $totalSquare = 0;
        $totalPrice  = 0;

        foreach ($order->items as $item) {
            // если square_meters не передан — считаем сами
            $square = $item->square_meters ?? (($item->height * $item->width / 1_000_000) * $item->quantity);
            $totalSquare += $square;

            $totalPrice  += $item->total_price ?? 0;
        }

        $order->update([
            'square_meters' => $totalSquare,
            'total_price'   => $totalPrice,
        ]);

        return redirect()->route('orders.show', $order);
    }


    /**
     * Просмотр заказа.
     */
    public function show(Order $order)
    {
        $order->load('items', 'customer', 'status');
        return view('orders.show', compact('order'));
    }

    /**
     * Редактирование заказа.
     */
    public function edit(Order $order)
    {
        $order->load('items');
        $customers = Customer::all();
        $statuses  = Status::all();
        return view('orders.edit', compact('order','customers','statuses'));
    }

    /**
     * Обновление заказа и его позиций.
     */
    public function update(Request $request, Order $order)
    {
        $order->update([
            'customer_id' => $request->customer_id,
            'status_id'   => $request->status_id,
        ]);

        // обновление позиций
        foreach ($request->items as $itemId => $itemData) {
            $item = OrderItem::find($itemId);
            if ($item) {
                $item->update($itemData);
            }
        }

        return redirect()->route('orders.show', $order);
    }

    /**
     * Удаление позиции из заказа.
     */
    public function destroyItem(OrderItem $item)
    {
        $item->delete();
        return back();
    }

    /**
     * Подтверждение заказа клиентом.
     */
    public function submit(Order $order)
    {
        $order->update([
            'status_id' => Status::where('name','received')->first()->id,
        ]);
        return redirect()->route('orders.show', $order);
    }
    public function preview(Order $order)
    {
        $items = $order->items;
        $totalQuantity = $items->sum('quantity');
        $totalSquare = $items->sum('square_meters');

        return view('orders.preview', compact('order', 'items', 'totalQuantity', 'totalSquare'));
    }

}
