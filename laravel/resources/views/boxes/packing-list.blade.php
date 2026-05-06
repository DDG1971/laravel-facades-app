<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold mb-6">
            Упаковка списком — Заказ №{{ $order->queue_number }}
        </h1>
        <p class="text-gray-600">
            Клиент: {{ $order->customer->company_name ?? $order->customer->name }} |
            Фрезеровка: {{ $order->milling->name ?? '—' }}
        </p>
    </x-slot>

    <div class="bg-white shadow rounded p-6">
        <h2 class="text-lg font-semibold mb-4">Этикетки для печати</h2>
        <p class="text-gray-500 mb-4">
            Всего деталей: {{ $order->items->count() }}.
            Этикеток: {{ $totalPages }}.
        </p>
        <p class="text-gray-500 mb-6">
            На каждой этикетке — список деталей и поле для отметки «Коробка № ___».
            Упаковщик вручную отмечает, какие детали положил в коробку.
        </p>

        <div class="flex flex-wrap gap-3">
            @for($page = 1; $page <= $totalPages; $page++)
                <a href="{{ route('boxes.packing-list-print', ['order' => $order->id, 'page' => $page]) }}"
                   class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600"
                   target="_blank">
                    🖨️ Этикетка {{ $page }} / {{ $totalPages }}
                </a>
            @endfor
        </div>

        <div class="mt-6">
            <a href="{{ route('orders.preview', $order->id) }}" class="text-blue-600 hover:underline">
                ← Назад к заказу
            </a>
        </div>
    </div>
</x-app-layout>
