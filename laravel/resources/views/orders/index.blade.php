<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Список заказов
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <table class="min-w-full border-collapse border">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-left">Очер.</th>
                        <th class="border px-3 py-2 text-left">Клиент</th>
                        <th class="border px-3 py-2 text-left">№клиента</th>
                        <th class="border px-3 py-2 text-left">Дата</th>
                        <th class="border px-3 py-2 text-left">Мат-лы</th>
                        <th class="border px-3 py-2 text-left">Каталог</th>
                        <th class="border px-3 py-2 text-left">№_цвета</th>
                        <th class="border px-3 py-2 text-left">Покрыт.</th>
                        <th class="border px-3 py-2 text-left">Фрезеровка</th>
                        <th class="border px-3 py-2 text-left">м²</th>
                        <th class="border px-3 py-2 text-left">Статус</th>
                        <th class="border px-3 py-2 text-left">Дата </th>
                        <th class="border px-3 py-2 text-left">Цех №</th>
                        <th class="border px-3 py-2 text-left">Коммент.</th>
                        <th class="border px-3 py-2 text-left">Действ</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50">
                            <td class="border px-3 py-2">{{ $order->queue_number }}</td>
                            <td class="border px-3 py-2">{{ $order->customer->name ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $order->client_order_number }}</td>
                            <td class="border px-3 py-2">{{ $order->date_received?->format('d.m.Y') }}</td>
                            <td class="border px-3 py-2">{{ $order->material }}</td>
                            <td class="border px-3 py-2">{{ $order->colorCatalog->name ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $order->colorCode->code ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $order->coatingType->name ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $order->milling->name ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $order->square_meters }}</td>
                            <td class="border px-3 py-2">{{ $order->status->name ?? '—' }}</td>
                            <td class="border px-3 py-2">{{ $order->date_status?->format('d.m.Y') }}</td>
                            <td class="border px-3 py-2">{{ $order->paint_shop_id }}</td>
                            <td class="border px-3 py-2">{{ $order->notes }}</td>
                            <td class="border px-3 py-2 space-x-2">
                                <a href="{{ route('orders.show', $order) }}" class="text-blue-600 hover:underline">Просмотр</a>
                                <a href="{{ route('orders.edit', $order) }}" class="text-green-600 hover:underline">Редактировать</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="15" class="border px-3 py-4 text-center text-gray-500">
                                Заказов пока нет
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>


