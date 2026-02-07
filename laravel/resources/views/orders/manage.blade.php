<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Управление заказом #{{ $order->queue_number }}
        </h2>
    </x-slot>

    <div class="py-6 space-y-6">
        <!-- 🔹 Шапка заказа -->
        @include('orders.partials.header', [
            'order' => $order,
            'customers' => $customers,
            'colorCatalogs' => $colorCatalogs,
            'colors' => $colors,
            'coatingTypes' => $coatingTypes,
            'millings' => $millings
        ])

        <!-- 🔹 Расчёт заказа -->
        <div class="bg-white shadow rounded p-4">
            <h3 class="font-bold mb-2">Расчёт заказа</h3>
            @include('orders.partials.calculation', ['order' => $order])
        </div>
        <div class="mt-6">
            <a href="{{ route('orders.export.pdf', ['order' => $order, 'price_group' => $priceGroup]) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Скачать PDF для клиента
            </a>
        </div>

        <!-- 🔹 Отправка клиенту -->
        <form action="{{ route('orders.send.calculation', $order) }}" method="POST">
            @csrf
            <input type="hidden" name="price_group" value="{{ $priceGroup }}">
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                Отправить расчёт клиенту
            </button>
        </form>

        <!-- 🔹 Управление статусом -->
        {{--<div class="bg-white shadow rounded p-4">
            <h3 class="font-bold mb-2">Статус заказа</h3>
            @include('orders.partials.status-form', ['order' => $order, 'statuses' => $statuses])
        </div>--}}
    </div>
</x-app-layout>


