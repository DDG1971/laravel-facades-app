<x-app-layout>
    <x-slot name="head">
        <meta name="order-id" content="{{ $order->id }}">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold mb-6">
            Упаковка заказа №{{ $order->queue_number }}
        </h1>
        <p class="text-gray-600">
            Клиент: {{ $order->customer->name }} |
            Фрезеровка: {{ $order->milling->name ?? '—' }}
        </p>
        <div class="no-print mt-4">
            <a href="{{ route('orders.preview', $order->id) }}" class="text-blue-600 hover:underline">
                ← Назад к просмотру заказа
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Левая колонка: Список деталей --}}
        <div class="md:col-span-1">
            <div class="bg-white shadow rounded p-4">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Детали заказа</h2>
                    <button id="splitItemsBtn" class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded text-sm">
                        Разделить на единицы
                    </button>
                </div>
                <div id="items-list" class="space-y-2">
                    @foreach($remainingItems as $item)
                        <div class="item-card border rounded p-2 cursor-move bg-gray-50"
                             data-item-id="{{ $item->id }}"
                             data-qty="{{ $item->quantity }}">
                            <strong>{{ $item->facadeType->display_name ?? '—' }} {{ $item->height }}x{{ $item->width }}</strong> × {{ $item->quantity }} шт
                            <br><small class="text-gray-500">{{ $item->thickness->value ?? 19 }} мм</small>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Правая колонка: Коробки --}}
        <div class="md:col-span-2">
            {{-- Заголовок и кнопка "Распечатать все" --}}
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold">Коробки заказа</h2>
                <button id="printAllBoxes" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 text-sm {{ $boxes->count() > 0 ? '' : 'hidden' }}">
                    🖨️ Распечатать все (<span id="boxesCount">{{ $boxes->count() }}</span>)
                </button>
            </div>

            {{-- Сетка коробок --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4" id="boxes-container">
                @forelse($boxes as $box)
                    <div class="bg-white shadow rounded p-4 box-dropzone" data-box-id="{{ $box->id }}">
                        <div class="flex justify-between items-center mb-3">
                            <h3 class="font-semibold">Коробка #{{ $box->box_number }}</h3>
                            <a href="{{ route('boxes.print', $box) }}"
                               class="text-blue-600 hover:underline text-sm"
                               target="_blank">
                                🖨️ Печать
                            </a>
                        </div>
                        <div class="box-items min-h-[150px] border border-dashed border-gray-300 rounded p-2 bg-gray-50">
                            {{-- JS наполнит --}}
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-8">
                        <button id="createFirstBox" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            ➕ Создать первую коробку
                        </button>
                    </div>
                @endforelse
            </div>

            {{-- Кнопка ВСЕГДА в DOM, но скрыта если нет коробок --}}
            <button id="addBoxBtn" class="mt-4 bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded text-sm {{ $boxes->count() > 0 ? '' : 'hidden' }}">
                ➕ Добавить коробку
            </button>
        </div>
    </div>


    {{-- Передаём items в JS --}}
    <script>
        document.getElementById('items-list').dataset.items = JSON.stringify({!! json_encode($itemsData) !!});
    </script>
</x-app-layout>
