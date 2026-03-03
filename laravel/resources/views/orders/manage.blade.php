<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Order Management #{{ $order->queue_number }}
            </h2>
            <a href="{{ route('admin.orders.index') }}"
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition text-sm font-medium">
                ← Back to List
            </a>
        </div>
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
        <div class="max-w-full mx-auto mb-6">
            <div class="bg-white shadow rounded-lg p-4 border-l-4 border-indigo-600 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="p-2 bg-indigo-50 rounded-full">
                        <svg xmlns="http://www.w3.org" class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l2-2 4 4m0-7l3 3m-3-3l-3 3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-700 uppercase tracking-wider">Ценовая категория</h3>
                        <p class="text-xs text-gray-500 italic">Текущий расчет: <span class="font-bold text-indigo-600 uppercase">{{ $priceGroup }}</span></p>
                    </div>
                </div>

                {{-- Форма переключения --}}
                <form action="{{ route('orders.manage', $order) }}" method="GET" class="flex items-center">
                    {{-- Если в URL есть другие важные параметры, они не потеряются --}}
                    @foreach(request()->except('price_group') as $key => $value)
                        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <select name="price_group"
                            onchange="this.form.submit()"
                            class="block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md font-bold text-gray-700 cursor-pointer shadow-sm bg-gray-50">
                        <option value="retail" {{ $priceGroup === 'retail' ? 'selected' : '' }}>🛒 Retail (Розница)</option>
                        <option value="private" {{ $priceGroup === 'private' ? 'selected' : '' }}>👤 Private (Частник)</option>
                        <option value="dealer" {{ $priceGroup === 'dealer' ? 'selected' : '' }}>🤝 Dealer (Дилер)</option>
                    </select>
                </form>
            </div>
        </div>


        {{--Финансы и оплата--}}
        <div class="bg-white shadow rounded p-4 border-l-4 {{ $order->payment_status === 'paid' ? 'border-green-500' : 'border-yellow-500' }}">
            <h3 class="font-bold mb-4 flex items-center gap-2 text-lg">
                <span class="text-xl">💳</span>
                <span>Финансовый баланс</span>
                @switch($order->payment_status)
                    @case('paid') <span class="text-xs bg-green-100 text-green-800 px-2 py-0.5 rounded-full uppercase tracking-wider">Paid</span> @break
                    @case('partial') <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded-full uppercase tracking-wider">Partial</span> @break
                    @default <span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full uppercase tracking-wider">Unpaid</span> @break
                @endswitch
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <p class="text-gray-500 text-sm italic">Total Order Amount:</p>
                    <p class="text-2xl font-black text-gray-800">
                        ${{ number_format($order->total_price ?: $order->calculateTotal($priceGroup), 2, '.', ',') }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm italic">Paid to date:</p>
                    <p class="text-2xl font-black text-green-600">
                        ${{ number_format($order->paid_amount, 2, '.', ',') }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-500 text-sm italic">Balance Due:</p>
                    <p class="text-2xl font-black {{ $order->debt_amount > 0 ? 'text-red-600' : 'text-gray-400' }}">
                        ${{ number_format($order->debt_amount, 2, '.', ',') }}
                    </p>
                </div>
            </div>

            <!-- Форма внесения оплаты -->
            <form action="{{ route('orders.updatePayment', $order) }}" method="POST" class="bg-gray-50 p-4 rounded-lg flex flex-wrap items-end gap-4 border border-gray-100">
                @csrf
                <input type="hidden" name="price_group" value="{{ $priceGroup }}">

                <div class="flex-1 min-w-[200px]">
                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-1 text-uppercase">Add Payment ($)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">$</span>
                        <input type="number" step="0.01" name="amount" id="amount"
                               class="w-full pl-7 border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="0.00" required>
                    </div>
                </div>

                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-bold rounded shadow hover:bg-blue-700 transition duration-150 uppercase text-sm tracking-wide">
                    Post Payment
                </button>

                @if($order->payment_status !== 'paid' && ($order->total_price ?: $order->calculateTotal($priceGroup)) > 0)
                    <button type="button"
                            onclick="document.getElementById('amount').value = '{{ $order->debt_amount }}'"
                            class="text-xs text-blue-600 font-bold hover:text-blue-800 flex items-center gap-1">
                        <span>⚡ Pay Full Balance</span>
                    </button>
                @endif
            </form>
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


