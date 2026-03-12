<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Список заказов — {{ $customer->company_name }}
        </h2>
        <form method="GET" action="{{ route('orders.index') }}" class="flex flex-wrap gap-2">
            <!-- Поиск по номеру -->
            <input type="text" name="client_order_number" value="{{ request('client_order_number') }}"
                   placeholder="№заказа клиента"
                   class="text-sm border-gray-300 rounded-md shadow-sm w-40">

            <!-- Выбор цвета -->
            <select name="color_code_id" class="text-sm border-gray-300 rounded-md shadow-sm">
                <option value="">Все цвета</option>
                @foreach($colorCodes as $color)
                    <option value="{{ $color->id }}" @selected(request('color_code_id') == $color->id)>
                        {{ $color->code }}
                    </option>
                @endforeach
            </select>
            <!-- Выбор фрезеровки -->
            <select name="milling_id"
                    class="text-sm border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                <option value="">Все фрезеровки</option>
                @foreach($millings as $milling)
                    <option value="{{ $milling->id }}" @selected(request('milling_id') == $milling->id)>
                        {{ $milling->name }}
                    </option>
                @endforeach
            </select>

            <!-- НОВЫЙ ВЫБОР СТАТУСА -->
            <select name="status_id" class="text-sm border-gray-300 rounded-md shadow-sm">
                <option value="">Все статусы</option>
                @foreach($statuses as $status)
                    <option value="{{ $status->id }}" @selected(request('status_id') == $status->id)>
                        {{ $status->label }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">
                Найти
            </button>

            {{-- Обновленная кнопка сброса (добавил status_id) --}}
            @if(request()->anyFilled(['color_code_id', 'client_order_number','milling_id', 'status_id']))
                <a href="{{ route('orders.index') }}"
                   class="bg-red-500 text-white px-4 py-2 rounded-md text-sm hover:bg-red-600">
                    Сбросить
                </a>
            @endif
        </form>

    </x-slot>

    <div class="py-6">
        <div class="w-full px-6 lg:px-8">
            <!-- фиксированный хедер -->
            <div class="overflow-x-auto">
                <table class="w-full table-fixed border-separate text-sm">
                    <colgroup>
                        <col class="w-[60px]"/>   <!-- Очер. -->
                        <col class="w-[120px]"/>  <!-- № клиента -->
                        <col class="w-[120px]"/>  <!-- Дата -->
                        <col class="w-[96px]"/>   <!-- Мат-лы -->
                        <col class="w-[96px]"/>   <!-- Каталог -->
                        <col class="w-[100px]"/>  <!-- № цвета -->
                        <col class="w-[120px]"/>  <!-- Покрыт. -->
                        <col class="w-[120px]"/>  <!-- Фрезеровка -->
                        <col class="w-[80px]"/>   <!-- м² -->
                        <col class="w-[120px]"/>  <!-- Статус -->
                        <col class="w-[120px]"/>  <!-- Дата статуса -->
                        <col class="w-[120px]"/>  <!-- Действ -->
                    </colgroup>
                    <thead class="bg-gray-100 shadow-sm">
                    <tr>
                        <th class="border px-2 py-1">Очер.</th>
                        <th class="border px-2 py-1">№ клиента</th>
                        <th class="border px-2 py-1">Дата</th>
                        <th class="border px-2 py-1">Мат-лы</th>
                        <th class="border px-2 py-1">Каталог</th>
                        <th class="border px-2 py-1">№ цвета</th>
                        <th class="border px-2 py-1">Покрыт.</th>
                        <th class="border px-2 py-1">Фрезеровка</th>
                        <th class="border px-2 py-1">м²</th>
                        <th class="border px-2 py-1">
                            <div class="flex items-center justify-center gap-1">
                                <span>Статус</span>
                                <span class="text-lg text-blue-600 font-bold leading-none cursor-help"
                                      title="Статус производства и оплаты">
                                                💳
                                </span>
                            </div>
                        </th>
                        <th class="border px-2 py-1">Дата статуса</th>
                        <th class="border px-2 py-1">Действ</th>
                    </tr>
                    </thead>
                </table>
            </div>

            <!-- тело -->
            <div class="overflow-x-auto overflow-y-auto h-[70vh]">
                <table class="w-full table-fixed border-separate text-sm">
                    <colgroup>
                        <col class="w-[60px]"/>
                        <col class="w-[120px]"/>
                        <col class="w-[120px]"/>
                        <col class="w-[96px]"/>
                        <col class="w-[96px]"/>
                        <col class="w-[100px]"/>
                        <col class="w-[120px]"/>
                        <col class="w-[120px]"/>
                        <col class="w-[80px]"/>
                        <col class="w-[120px]"/>
                        <col class="w-[120px]"/>
                        <col class="w-[120px]"/>
                    </colgroup>
                    <tbody>
                    @forelse($orders as $order)
                        <tr data-order-id="{{ $order->id }}"
                            class="transition-colors duration-300 hover:bg-opacity-80
        @switch($order->status->name)
            @case('new') bg-gray-300 text-gray-900 @break
            @case('received') bg-yellow-500 text-black @break
            @case('in_progress') bg-blue-500 text-white @break
            @case('ready') bg-green-500 text-white @break
            @case('shipped') bg-green-300 text-gray-800 @break
            @case('completed') bg-purple-600 text-white @break
            @case('cancelled') bg-red-500 text-white @break
            @default bg-white text-gray-900
        @endswitch">
                            <td class="border px-2 py-1 text-center">{{ $order->queue_number }}</td>
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
                            <td class="border px-2 py-1">
                                <div class="flex items-center justify-between px-1">
                                    {{-- Название статуса --}}
                                    <span id="status-label-{{ $order->id }}">{{
                                               $order->status->label }}
                                     </span>

                                    {{-- Иконка оплаты (💰, 💸 или ❌) --}}
                                    <div class="text-lg" title="{{
                                               $order->payment_status === 'paid' ? 'Оплачено полностью' :
                                               ($order->payment_status === 'partial' ? 'Частичная оплата. Остаток: ' . number_format($order->debt_amount, 0, ',', ' ') . ' р.' : 'Ожидает оплаты')
                                     }}">
                                        @switch($order->payment_status)
                                            @case('paid')
                                                <span class="cursor-help">💰</span>
                                                @break
                                            @case('partial')
                                                <span class="cursor-help">💸</span>
                                                @break
                                            @default
                                                <span class="cursor-help opacity-40">❌</span>
                                        @endswitch
                                    </div>
                                </div>
                            </td>
                            <td id="date-status-{{ $order->id }}" class="border px-2 py-1 text-center">
                                {{ $order->date_status ? \Carbon\Carbon::parse($order->date_status)->format('d.m.Y') : '—' }}
                            </td>
                            <td class="border px-2 py-1 space-x-2 text-center">
                                <a href="{{ route('orders.preview', $order) }}" class="text-blue-600">🖨️</a>
                                @if($order->status->name === 'new')
                                    {{-- Активная кнопка только для новых заказов --}}
                                    <a href="{{ route('orders.edit', $order) }}" class="text-green-600 hover:underline"
                                       title="Редактировать">
                                        ✏️
                                    </a>
                                @else
                                    {{-- Заблокированная иконка для всех остальных статусов --}}
                                    <span class="text-gray-400 cursor-not-allowed"
                                          title="Редактирование недоступно (заказ в работе)">
                                        ✏️
                                     </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="12" class="border px-3 py-4 text-center text-gray-500">
                                Заказов пока нет
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>

    </div>
</x-app-layout>

