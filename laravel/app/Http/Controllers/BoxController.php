<?php

namespace App\Http\Controllers;

use App\Models\Box;
use App\Models\BoxItem;
use App\Models\Order;
use Illuminate\Http\Request;

class BoxController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Order $order)
    {
        $boxes = $order->boxes()->with('items.orderItem')->get();
        return view('boxes.index', compact('order', 'boxes'));
    }

    // Страница Drag-and-Drop упаковки
    public function packing(Order $order)
    {
        $order->load('items');
        $boxes = $order->boxes()->with('items')->get();

        // Получаем ID всех упакованных order_item_id
        $packedItemIds = $boxes->flatMap->items->pluck('order_item_id')->unique();

        // Оставляем только те позиции, которые НЕ упакованы полностью
        $remainingItems = $order->items->filter(function($item) use ($packedItemIds) {
            return !$packedItemIds->contains($item->id);
        });

        // Подготовим данные для JS
        $itemsData = $remainingItems->map(function($i) {
            return [
                'id' => $i->id,
                'type' => $i->facadeType->display_name ?? null,
                'height' => $i->height,
                'width' => $i->width,
                'quantity' => $i->quantity,
                'thickness' => $i->thickness->value ?? 19
            ];
        })->values();

        return view('boxes.packing', compact('order', 'boxes', 'itemsData', 'remainingItems'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Order $order)
    {
        // Считаем следующий номер коробки для этого заказа
        $lastBox = $order->boxes()->max('box_number') ?? 0;
        $nextNumber = $lastBox + 1;

        $box = $order->boxes()->create([
            'box_number' => $nextNumber,
            'status' => 'pending'
        ]);

        return response()->json([
            'status' => 'ok',
            'box' => $box
        ]);
    }
    // Добавить деталь в коробку
    public function addItem(Request $request, Order $order, Box $box)
    {
        $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'quantity' => 'required|integer|min:1'
        ]);

        // Проверяем, есть ли уже такая деталь в коробке
        $existing = $box->items()->where('order_item_id', $request->order_item_id)->first();

        if ($existing) {
            $existing->increment('quantity', $request->quantity);
        } else {
            $box->items()->create([
                'order_item_id' => $request->order_item_id,
                'quantity' => $request->quantity
            ]);
        }

        return response()->json(['status' => 'ok']);
    }


// Получить содержимое коробки
    public function getItems(Order $order, Box $box)
    {
        $items = $box->items()->with('orderItem.facadeType')->get()->map(function($item) {
            return [
                'id' => $item->id,
                'type' => $item->orderItem->facadeType->display_name ?? null,
                'height' => $item->orderItem->height,
                'width' => $item->orderItem->width,
                'quantity' => $item->quantity,
                'thickness' => $item->orderItem->thickness->value ?? 19
            ];
        });

        return response()->json(['items' => $items]);
    }
    public function print(Box $box)
    {
        // Пока заглушка
        return view('boxes.print', compact('box'));
    }
    public function removeItem(Order $order, Box $box, BoxItem $boxItem)
    {
        $boxItem->delete();

        return response()->json(['status' => 'ok']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Box $box)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Box $box)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Box $box)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order, Box $box)
    {
        // Удаляем только если коробка принадлежит этому заказу
        if ($box->order_id !== $order->id) {
            return response()->json(['status' => 'error', 'message' => 'Нет доступа'], 403);
        }

        $box->delete();

        return response()->json(['status' => 'ok']);
    }
    public function packingList(Order $order)
    {
        $order->load('items.facadeType', 'items.thickness');

        // Считаем, сколько строк помещается на этикетку (примерно 20-25 с шапкой)
        $perPage = 20;
        $totalItems = $order->items->count();
        $totalPages = ceil($totalItems / $perPage);

        return view('boxes.packing-list', compact('order', 'totalPages'));
    }
    public function packingListPrint(Request $request, Order $order)
    {
        $order->load('items.facadeType', 'items.thickness');

        $perPage = 20;
        $page = $request->get('page', 1);
        $items = $order->items->forPage($page, $perPage);
        $totalPages = ceil($order->items->count() / $perPage);

        return view('boxes.packing-list-print', compact('order', 'items', 'page', 'totalPages'));
    }
}
