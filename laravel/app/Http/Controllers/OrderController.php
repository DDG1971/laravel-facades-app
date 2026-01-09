<?php

namespace App\Http\Controllers;

use App\Models\Milling;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FacadeType;
use App\Models\ColorCode;
use App\Models\ColorCatalog;
use App\Models\CoatingType;
use App\Models\Drilling;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    /**
     * Список заказов (для клиента или менеджера).
     */
    public function index()
    {
        $orders = Order::with([
            'customer',
            'status',
            'colorCatalog',
            'colorCode',
            'coatingType',
            'milling'
        ])->get();
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
        // Валидация
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'client_order_number' => 'nullable|string|max:50',
            'date_received' => 'nullable|date',
            'material' => 'required|string',
            'color_catalog_id' => 'nullable|exists:color_catalogs,id',
            'color_code_id' => ['required', 'exists:color_codes,id',
                function ($attribute, $value, $fail) use ($request) {
                    $catalogId = $request->input('color_catalog_id');
                    if (!\App\Models\ColorCode::where('id', $value)
                        ->where('color_catalog_id', $catalogId)->exists()) {
                        $fail('Выбранный цвет не принадлежит указанному каталогу.');
                    }
                },
            ],
            'coating_type_id' => 'nullable|exists:coating_types,id',
            'milling_id' => 'nullable|exists:millings,id',

            'items.*.height' => 'nullable|integer|min:1',
            'items.*.width' => 'nullable|integer|min:1',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.thickness' => 'nullable|in:6,10,12,14,16,18,19,22,25,32,38,44',
            'items.*.facade_type_id' => 'nullable|exists:facade_types,id',
            'items.*.milling_id' => 'nullable|exists:millings,id',
            'items.*.coating_type_id' => 'nullable|exists:coating_types,id',
            'items.*.color_catalog_id' => 'nullable|exists:color_catalogs,id',
            'items.*.color_code_id' => 'nullable|exists:color_codes,id',
            'items.*.drilling_id' => 'nullable|exists:drillings,id',
            'items.*.notes' => 'nullable|string',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.total_price' => 'nullable|numeric|min:0',
            'order_attachment' => 'nullable|file|mimes:pdf,jpg,png,doc,docx',
            'items.*.attachment' => 'nullable|file|mimes:pdf,jpg,png,doc,docx',
        ]);

        // Статус по умолчанию
        $defaultStatusId = optional(Status::firstWhere('name', 'new'))->id
            ?? Status::min('id');

        $clientOrderNumber = trim($request->input('client_order_number') ?? '');
        $customerId = $request->input('customer_id');
        $today = now()->toDateString();

        // Если номер пустой → подставляем "б/н-<queue_number>" позже, после получения queue_number
        $clientOrderNumber = $clientOrderNumber === '' ? null : $clientOrderNumber;

        // Проверка на дубликат (только если номер указан)
        $duplicateFound = false;
        if ($clientOrderNumber) {
            $duplicateFound = Order::where('customer_id', $customerId)
                ->whereYear('date_received', Carbon::parse($today)->year)
                ->where('client_order_number', $clientOrderNumber)
                ->exists();
        }

        $material = $request->input('material');
        $colorCatalogId = $request->input('color_catalog_id');
        $colorCodeId = $request->input('color_code_id');
        $coatingTypeId = $request->input('coating_type_id');
        $millingId = $request->input('milling_id');

        $order = DB::transaction(function () use (
            $defaultStatusId,
            $clientOrderNumber,
            $today,
            $customerId,
            $material,
            $colorCatalogId,
            $colorCodeId,
            $coatingTypeId,
            $millingId
        ) {
            $lastQueue = Order::lockForUpdate()->max('queue_number') ?? 0;
            $queueNumber = $lastQueue + 1;

            return Order::create([
                'customer_id' => $customerId,
                'user_id' => Auth::id(),
                'status_id' => $defaultStatusId,
                'date_received' => $today,
                'queue_number' => $queueNumber,
                'client_order_number' => $clientOrderNumber ?? ('б/н-' . $queueNumber),
                'material' => $material,
                'color_catalog_id' => $colorCatalogId,
                'color_code_id' => $colorCodeId,
                'coating_type_id' => $coatingTypeId,
                'milling_id' => $millingId,
            ]);
        });

        // Если нашли дубликат → обновляем флаг
       // if ($duplicateFound) {
       //     $order->duplicate = true;
       //     $order->save();
       // }

        // Вложение для заказа
        if ($request->hasFile('order_attachment')) {
            $path = $request->file('order_attachment')->store('orders/attachments', 'public');
            $order->attachment_path = $path;
            $order->save();
        }

        // Позиции
        foreach ($request->input('items', []) as $index => $item) {
            if (empty($item['height']) && empty($item['width']) && empty($item['quantity'])) {
                continue;
            }

            $attachmentPath = null;
            if (!empty($item['attachment']) && $item['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                $attachmentPath = $item['attachment']->store('order_items/attachments', 'public');
            }

            OrderItem::create([
                'order_id' => $order->id,
                'facade_type_id' => $item['facade_type_id'] ?? null,
                'milling_id' => $item['milling_id'] ?? null,
                'coating_type_id' => $item['coating_type_id'] ?? null,
                'color_catalog_id' => $item['color_catalog_id'] ?? null,
                'color_code_id' => $item['color_code_id'] ?? null,
                'thickness' => $item['thickness'],
                'height' => $item['height'],
                'width' => $item['width'],
                'square_meters' => $item['square_meters'] ?? null,
                'quantity' => $item['quantity'],
                'double_sided_coating' => $item['double_sided_coating'] ?? false,
                'drilling_id' => $item['drilling_id'] ?? null,
                'notes' => $item['notes'] ?? null,
                'unit_price' => $item['unit_price'] ?? 0,
                'total_price' => $item['total_price'] ?? 0,
                'date_created' => now(),
                'attachment_path' => $attachmentPath,
            ]);
        }

        // Пересчёт итогов
        $order->load('items', 'colorCatalog', 'colorCode', 'coatingType', 'milling');

        $totalSquare = 0;
        $totalPrice = 0;

        foreach ($order->items as $item) {
            $square = $item->square_meters
                ?? (($item->height * $item->width / 1_000_000) * $item->quantity);

            $totalSquare += $square;
            $totalPrice += $item->total_price ?? 0;
        }

        $order->update([
            'square_meters' => $totalSquare,
            'total_price' => $totalPrice,
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('duplicate', $duplicateFound)
            ->with('success', 'Заказ создан!');
    }


    /**
     * Просмотр заказа.
     */
    public function show(Order $order)
    {
        $order->load(
            'items',
            'customer',
            'status',
            'colorCatalog',
            'colorCode',
            'coatingType',
            'milling'
        );

        return view('orders.show', compact('order'));
    }

    /**
     * Редактирование заказа.
     */
    public function edit(Order $order)
    {
        $order->load('items');

        $customers = Customer::all();
        $statuses = Status::all();
        $colorCatalogs = ColorCatalog::all();
        $colors = ColorCode::all();
        $coatingTypes = CoatingType::all();
        $millings = Milling::all();
        $facadeTypes = FacadeType::all();
        $drillings = Drilling::all();

        return view('orders.edit', compact(
            'order',
            'customers',
            'statuses',
            'colorCatalogs',
            'colors',
            'coatingTypes',
            'millings',
            'facadeTypes',
            'drillings'
        ));
    }

    /**
     * Обновление заказа и его позиций.
     */
    public function update(Request $request, Order $order)
    {
        // Валидация
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'client_order_number' => 'nullable|string|max:50',
            'date_received' => 'nullable|date',
            'material'    => 'required|string',
            'color_catalog_id' => 'nullable|exists:color_catalogs,id',
            'color_code_id' => ['required', 'exists:color_codes,id',
                function ($attribute, $value, $fail) use ($request) {
                    $catalogId = $request->input('color_catalog_id');
                    if (!\App\Models\ColorCode::where('id', $value)
                        ->where('color_catalog_id', $catalogId)->exists()) {
                        $fail('Выбранный цвет не принадлежит указанному каталогу.');
                    }
                },
            ],
            'coating_type_id' => 'nullable|exists:coating_types,id',
            'milling_id' => 'nullable|exists:millings,id',

            'items.*.height' => 'required|integer|min:1',
            'items.*.width' => 'required|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.thickness' => 'nullable|in:6,10,12,14,16,18,19,22,25,32,38,44',
            'items.*.facade_type_id' => 'nullable|exists:facade_types,id',
            'items.*.milling_id' => 'nullable|exists:millings,id',
            'items.*.coating_type_id' => 'nullable|exists:coating_types,id',
            'items.*.color_catalog_id' => 'nullable|exists:color_catalogs,id',
            'items.*.color_code_id' => 'nullable|exists:color_codes,id',
            'items.*.drilling_id' => 'nullable|exists:drillings,id',
            'items.*.notes' => 'nullable|string',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.total_price' => 'nullable|numeric|min:0',
            'order_attachment' => 'nullable|file|mimes:pdf,jpg,png,doc,docx',
            'items.*.attachment' => 'nullable|file|mimes:pdf,jpg,png,doc,docx',
        ]);

        // Данные «шапки» заказа (статус оставляем как есть)
        $orderData = [
            'customer_id'         => $validated['customer_id'],
            'status_id'           => $order->status_id,
            'client_order_number' => $request->input('client_order_number'),
            'color_catalog_id'    => $request->input('color_catalog_id'),
            'color_code_id'       => $request->input('color_code_id'),
            'coating_type_id'     => $request->input('coating_type_id'),
            'material'            => $request->input('material'),
            'milling_id'          => $request->input('milling_id'),
        ];

        if ($request->hasFile('order_attachment')) {
            $orderData['attachment_path'] = $request->file('order_attachment')
                ->store('orders/attachments', 'public');
        }

        // ВАЖНО: реально обновить заказ
        try {
            $order->update($orderData);
        } catch (\Throwable $e) {
            dd('ORDER UPDATE FAILED', $e->getMessage());
        }

        // Позиции
        $items = $request->input('items', []);
        unset($items['__INDEX__']);

        foreach ($items as $itemData) {
            // пропуск пустых
            if (empty($itemData['height']) && empty($itemData['width']) && empty($itemData['quantity'])) {
                continue;
            }

            // обновление существующей позиции
            if (!empty($itemData['id'])) {
                $item = OrderItem::find($itemData['id']);
                if ($item) {
                    $attachmentPath = $item->attachment_path;
                    if (isset($itemData['attachment']) && $itemData['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                        $attachmentPath = $itemData['attachment']->store('order_items/attachments', 'public');
                    }

                    $item->update([
                        'facade_type_id'       => $itemData['facade_type_id'] ?? null,
                        'milling_id'           => $itemData['milling_id'] ?? null,
                        'coating_type_id'      => $itemData['coating_type_id'] ?? null,
                        'color_catalog_id'     => $itemData['color_catalog_id'] ?? null,
                        'color_code_id'        => $itemData['color_code_id'] ?? null,
                        'thickness'            => $itemData['thickness'],
                        'height'               => $itemData['height'],
                        'width'                => $itemData['width'],
                        'square_meters'        => $itemData['square_meters'] ?? null,
                        'quantity'             => $itemData['quantity'],
                        'double_sided_coating' => $itemData['double_sided_coating'] ?? false,
                        'drilling_id'          => $itemData['drilling_id'] ?? null,
                        'notes'                => $itemData['notes'] ?? null,
                        'unit_price'           => $itemData['unit_price'] ?? 0,
                        'total_price'          => $itemData['total_price'] ?? 0,
                        'attachment_path'      => $attachmentPath,
                    ]);
                }
            }
            // создание новой позиции
            else {
                $attachmentPath = null;
                if (isset($itemData['attachment']) && $itemData['attachment'] instanceof \Illuminate\Http\UploadedFile) {
                    $attachmentPath = $itemData['attachment']->store('order_items/attachments', 'public');
                }

                $order->items()->create([
                    'facade_type_id'       => $itemData['facade_type_id'] ?? null,
                    'milling_id'           => $itemData['milling_id'] ?? null,
                    'coating_type_id'      => $itemData['coating_type_id'] ?? null,
                    'color_catalog_id'     => $itemData['color_catalog_id'] ?? null,
                    'color_code_id'        => $itemData['color_code_id'] ?? null,
                    'thickness'            => $itemData['thickness'],
                    'height'               => $itemData['height'],
                    'width'                => $itemData['width'],
                    'square_meters'        => $itemData['square_meters'] ?? null,
                    'quantity'             => $itemData['quantity'],
                    'double_sided_coating' => $itemData['double_sided_coating'] ?? false,
                    'drilling_id'          => $itemData['drilling_id'] ?? null,
                    'notes'                => $itemData['notes'] ?? null,
                    'unit_price'           => $itemData['unit_price'] ?? 0,
                    'total_price'          => $itemData['total_price'] ?? 0,
                    'attachment_path'      => $attachmentPath,
                ]);
            }
        }

        // Пересчёт итогов
        $order->load('items');
        $totalSquare = $order->items->sum(function($item) {
            return $item->square_meters
                ?? (($item->height * $item->width / 1_000_000) * $item->quantity);
        });
        $totalPrice = $order->items->sum('total_price');

        $order->update([
            'square_meters' => $totalSquare,
            'total_price'   => $totalPrice,
        ]);

        return redirect()->route('orders.show', $order->id)
            ->with('success', 'Заказ успешно обновлён');
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
            //'status_id' => Status::where('name','received')->first()->id,
            'date_status' => now(), // полезно фиксировать дату смены статуса
        ]);

        return redirect()->route('orders.index')
            ->with('success', 'Заказ отправлен в список!');
    }
    public function preview(Order $order)
    {
        $items = $order->items;
        $totalQuantity = $items->sum('quantity');
        $totalSquare = $items->sum('square_meters');

        return view('orders.preview', compact('order', 'items', 'totalQuantity', 'totalSquare'));
    }


}
