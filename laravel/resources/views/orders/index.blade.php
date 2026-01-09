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
        <div class="w-full px-6 lg:px-8">
            <!-- фиксированный хедер -->
            <div class="overflow-x-auto">
                <table class="w-full table-fixed border-separate text-sm">
                    <colgroup>
                        <col class="w-[60px]" />   <!-- Очер. -->
                        <col class="w-[140px]" />  <!-- Клиент (уменьшено ~15%) -->
                        <col class="w-[120px]" />  <!-- № клиента -->
                        <col class="w-[120px]" />  <!-- Дата -->
                        <col class="w-[96px]" />   <!-- Мат-лы (уменьшено ~20%) -->
                        <col class="w-[96px]" />   <!-- Каталог (уменьшено ~20%) -->
                        <col class="w-[100px]" />  <!-- № цвета -->
                        <col class="w-[120px]" />  <!-- Покрыт. -->
                        <col class="w-[120px]" />  <!-- Фрезеровка -->
                        <col class="w-[80px]" />   <!-- м² -->
                        <col class="w-[120px]" />  <!-- Статус -->
                        <col class="w-[120px]" />  <!-- Дата статуса -->
                        <col class="w-[80px]" />   <!-- Цех № -->
                        <col class="w-[120px]" />  <!-- Действ -->
                    </colgroup>
                    <thead class="bg-gray-100 shadow-sm">
                    <tr>
                        <th class="border px-2 py-1">Очер.</th>
                        <th class="border px-2 py-1">Клиент</th>
                        <th class="border px-2 py-1">№ клиента</th>
                        <th class="border px-2 py-1">Дата</th>
                        <th class="border px-2 py-1">Мат-лы</th>
                        <th class="border px-2 py-1">Каталог</th>
                        <th class="border px-2 py-1">№ цвета</th>
                        <th class="border px-2 py-1">Покрыт.</th>
                        <th class="border px-2 py-1">Фрезеровка</th>
                        <th class="border px-2 py-1">м²</th>
                        <th class="border px-2 py-1">Статус</th>
                        <th class="border px-2 py-1">Дата статуса</th>
                        <th class="border px-2 py-1">Цех №</th>
                        <th class="border px-2 py-1">Действ</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <!-- тело -->
            <div class="overflow-x-auto overflow-y-auto h-[70vh]">
                <table class="w-full table-fixed border-separate text-sm">
                    <colgroup>
                        <col class="w-[60px]" />
                        <col class="w-[145px]" />  <!-- Клиент -->
                        <col class="w-[120px]" />
                        <col class="w-[120px]" />
                        <col class="w-[100px]" />   <!-- Мат-лы -->
                        <col class="w-[96px]" />   <!-- Каталог -->
                        <col class="w-[100px]" />
                        <col class="w-[120px]" />
                        <col class="w-[120px]" />
                        <col class="w-[80px]" />
                        <col class="w-[120px]" />
                        <col class="w-[120px]" />
                        <col class="w-[80px]" />
                        <col class="w-[120px]" />
                    </colgroup>
                    <tbody>
                    @forelse($orders as $order)
                        <tr class="
        hover:bg-gray-50
        @switch($order->status->name)
            @case('new') bg-blue-100 text-blue-800 @break
            @case('received') bg-yellow-100 text-yellow-800 @break
            @case('in_progress') bg-indigo-100 text-indigo-800 @break
            @case('paint_shop') bg-purple-100 text-purple-800 @break
            @case('ready') bg-green-100 text-green-800 @break
            @case('shipped') bg-teal-100 text-teal-800 @break
            @case('completed') bg-gray-200 text-gray-800 @break
            @case('cancelled') bg-red-100 text-red-800 @break
        @endswitch
    ">
                            <td class="border px-2 py-1 text-center">{{ $order->queue_number }}</td>
                            <td class="border px-2 py-1 truncate overflow-hidden whitespace-nowrap">
                                {{ $order->customer->company_name ?? 'нет данных' }}
                            </td>
                            <td class="border px-2 py-1 text-center">{{ $order->client_order_number }}</td>
                            <td class="border px-2 py-1 text-center">
                                {{ $order->date_received ? \Carbon\Carbon::parse($order->date_received)->format('d.m.Y') : '—' }}
                            </td>
                            <td class="border px-2 py-1">{{ $order->material }}</td>
                            <td class="border px-2 py-1">{{ $order->colorCatalog->name_en ?? '—' }}</td>
                            <td class="border px-2 py-1 text-center">{{ $order->colorCode->code ?? '—' }}</td>
                            <td class="border px-2 py-1">{{ $order->coatingType->name ?? '—' }}</td>
                            <td class="border px-2 py-1">{{ $order->milling->name ?? '—' }}</td>
                            <td class="border px-2 py-1 text-center">{{ $order->square_meters }}</td>
                            <td class="border px-2 py-1">{{ $order->status->label ?? '—' }}</td>
                            <td class="border px-2 py-1 text-center">
                                {{ $order->date_status ? \Carbon\Carbon::parse($order->date_status)->format('d.m.Y') : '—' }}
                            </td>
                            <td class="border px-2 py-1 text-center">{{ $order->paint_shop_id }}</td>
                            <td class="border px-2 py-1 space-x-2 text-center">
                                <a href="{{ route('orders.preview', $order) }}" class="text-blue-600 hover:underline">Просмотр</a>
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



