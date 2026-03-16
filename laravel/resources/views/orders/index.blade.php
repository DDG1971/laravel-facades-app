<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Список заказов
            </h2>

            <!-- Форма фильтрации -->
            <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-2">
                <!-- Поиск по номеру -->
                <input type="text"
                       name="client_order_number"
                       value="{{ request('client_order_number') }}"
                       placeholder="№ заказа клиента"
                       class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 w-40">

                <!-- Выбор клиента -->
                <select name="customer_id" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">Все клиенты</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>
                            {{ $customer->company_name }}
                        </option>
                    @endforeach
                </select>

                <!-- Выбор цвета -->
                <select name="color_code_id" class="text-sm border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <option value="">Все цвета</option>
                    @foreach($colorCodes as $color)
                        <option value="{{ $color->id }}" @selected(request('color_code_id') == $color->id)>
                            {{ $color->code }}
                        </option>
                    @endforeach
                </select>
                <!-- Выбор фрезеровки -->
                <select name="milling_id" class="text-sm border-gray-300 rounded-md shadow-sm focus:ring focus:ring-indigo-200">
                    <option value="">Все фрезеровки</option>
                    @foreach($millings as $milling)
                        <option value="{{ $milling->id }}" @selected(request('milling_id') == $milling->id)>
                            {{ $milling->name }}
                        </option>
                    @endforeach
                </select>

                <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded-md text-sm hover:bg-gray-700">
                    Найти
                </button>

                @if(request()->anyFilled(['customer_id', 'color_code_id','milling_id','client_order_number']))
                    <a href="{{ route('admin.orders.index') }}"
                       class="bg-red-500 text-white px-4 py-2 rounded-md text-sm hover:bg-red-600">
                        Сбросить
                    </a>
                @endif
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-6 lg:px-8">
            <!-- фиксированный хедер -->
            <div class="overflow-x-auto">
                <table class="w-full table-fixed border-separate text-sm">
                    <!-- Группа колонок для жесткой фиксации ширины -->
                    <colgroup>
                        <col class="w-[60px]">  <!-- Очер. -->
                        <col class="w-[180px]"> <!-- Клиент -->
                        <col class="w-[120px]"> <!-- № клиента -->
                        <col class="w-[100px]"> <!-- Дата -->
                        <col class="w-[80px]"> <!-- Мат-лы -->
                        <col class="w-[100px]"> <!-- Каталог -->
                        <col class="w-[100px]"> <!-- № цвета -->
                        <col class="w-[120px]"> <!-- Покрыт. -->
                        <col class="w-[150px]"> <!-- Фрезеровка -->
                        <col class="w-[70px]">  <!-- м² -->
                        <col class="w-[150px]"> <!-- Статус -->
                        <col class="w-[110px]"> <!-- Дата статуса -->
                        <col class="w-[80px]">  <!-- Цех № -->
                        <col class="w-[100px]"> <!-- Действ -->
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
                        <th class="border px-2 py-1">Расчет</th>
                        <th class="border px-2 py-1">Действ</th>
                    </tr>
                    </thead>
                </table>
            </div>
            <!-- тело -->
            <div class="overflow-x-auto overflow-y-auto h-[70vh]">
                <table class="w-full table-fixed border-separate text-sm">
                    <colgroup>
                        <col class="w-[60px]">  <!-- Очер. -->
                        <col class="w-[180px]"> <!-- Клиент -->
                        <col class="w-[120px]"> <!-- № клиента -->
                        <col class="w-[100px]"> <!-- Дата -->
                        <col class="w-[80px]"> <!-- Мат-лы -->
                        <col class="w-[100px]"> <!-- Каталог -->
                        <col class="w-[100px]"> <!-- № цвета -->
                        <col class="w-[120px]"> <!-- Покрыт. -->
                        <col class="w-[150px]"> <!-- Фрезеровка -->
                        <col class="w-[70px]">  <!-- м² -->
                        <col class="w-[150px]"> <!-- Статус -->
                        <col class="w-[110px]"> <!-- Дата статуса -->
                        <col class="w-[80px]">  <!-- Расчет -->
                        <col class="w-[100px]"> <!-- Действ -->
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
                            <!-- Очередь -->
                            <td class="border px-2 py-1 text-center">{{ $order->queue_number }}</td>
                            <!-- Клиент -->
                            <td class="border px-2 py-1 truncate overflow-hidden whitespace-nowrap">
                                {{ $order->customer->company_name ?? 'нет данных' }}
                            </td>
                            <!-- № клиента -->
                            <td class="border px-2 py-1 text-center">
                                {{ $order->client_order_number }}
                            </td>
                            <!-- Дата получения -->
                            <td class="border px-2 py-1 text-center">
                                {{ $order->date_received ? \Carbon\Carbon::parse($order->date_received)->format('d.m.Y') : '—' }}
                            </td>
                            <!-- Материал -->
                            <td class="border px-2 py-1">{{ $order->material }}</td>
                            <!-- Каталог -->
                            <td class="border px-2 py-1 text-center">{{ $order->colorCatalog->name_en ?? '—' }}</td>
                            <!-- Код цвета -->
                            <td class="border px-2 py-1 text-center">{{ $order->colorCode->code ?? '—' }}</td>
                            <!-- Покрытие -->
                            <td class="border px-2 py-1 whitespace-normal break-words max-w-[150px]">
                                {{ $order->coatingType->name ?? '—' }}
                            </td>
                            <!-- Фрезеровка -->
                            <td class="border px-2 py-1">{{ $order->milling->name ?? '—' }}</td>
                            <!-- Площадь -->
                            <td class="border px-2 py-1 text-center">{{ $order->square_meters }}</td>
                            <!-- Статус -->
                            <td class="border px-2 py-1 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Твой текущий Select -->
                                    <select id="status-select-{{ $order->id }}"
                                            onchange="updateStatus({{ $order->id }}, this.value)"
                                            class="text-sm border rounded px-1 py-0.5 focus:outline-none focus:ring focus:ring-blue-300
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
                                        @foreach($statuses as $status)
                                            <option value="{{ $status->id }}" @selected($order->status_id == $status->id) class="bg-white text-black">
                                                {{ $status->label }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <!-- Индикатор оплаты (💰, 💸 или ❌) -->
                                    <div class="text-lg" title="{{
                                        $order->payment_status === 'paid' ? 'Fully Paid'
                                         : 'Amount Due: $' . number_format($order->debt_amount, 2, '.', ',')
                                    }}">
                                        @switch($order->payment_status)
                                            @case('paid') <span class="cursor-help">💰</span> @break
                                            @case('partial') <span class="cursor-help">💸</span> @break
                                            @default <span class="cursor-help opacity-40">❌</span> @break
                                        @endswitch
                                    </div>
                                </div>
                            </td>
                            <!-- Дата статуса -->
                            <td id="date-status-{{ $order->id }}" class="border px-2 py-1 text-center">
                                {{ $order->date_status ? \Carbon\Carbon::parse($order->date_status)->format('d.m.Y') : '—' }}
                            </td>
                            <!-- Цех -->
                            <td class="border px-2 py-1 text-center">
                                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                                    <a href="{{ route('orders.manage', $order->id) }}"
                                       class="text-lg hover:scale-110 transition-transform inline-block"
                                       title="Расчеты и PDF">
                                        🧮
                                    </a>
                                @endif
                            </td>
                            <!-- Действия -->
                            <td class="border px-2 py-1 space-x-2 text-center">
                                <a href="{{ route('orders.preview', $order) }}"
                                   class="text-blue-600 hover:underline"
                                   title="Печать">
                                    🖨️
                                </a>
                                <a href="{{ route('orders.edit', $order) }}"
                                   class="text-green-600 hover:underline" title="Редактировать">
                                    ✏️
                                </a>
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
            <div class="mt-4 px-4 pb-4">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

</x-app-layout>
