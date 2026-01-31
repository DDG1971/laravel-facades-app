    <x-app-layout>
        <x-slot name="head">

            <x-assets/>
            <style>
                @media print {
                    @page {
                        size: A4 portrait;
                        margin: 20mm;
                    }

                    .no-print {
                        display: none !important;
                    }

                    body {
                        margin: 0;
                        padding: 0;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }

                    th,
                    td {
                        border: 1px solid #444;
                        padding: 4px 6px;
                        text-align: center;
                    }

                    tfoot td {
                        font-weight: bold;
                        text-align: center;
                    }

                    th {
                        background-color: #e0e0e0 !important;
                        -webkit-print-color-adjust: exact;
                        print-color-adjust: exact;
                    }
                }

                table th,
                table td {
                    text-align: center;
                    vertical-align: middle;
                }
            </style>
        </x-slot>

        <div class="print-page">
            {{--<h2 class="text-center font-bold text-lg mb-4">Заказ №{{ $order->id }}</h2> --}}
            <div class="mb-4 text-sm">
                <!-- Первый ряд: заголовок + дата -->
                <div class="-ml-16">
                    <div class="flex justify-center items-center gap-6 mb-2">
                        <h2 class="font-bold text-lg">Очередь №{{ $order->queue_number }}</h2>
                        <span><strong>Дата :</strong> {{ $order->date_received }}</span>
                    </div>
                    <div class="flex justify-center items-center gap-6 mb-2 border-b pb-2">
                        <span><strong>Клиент:</strong> {{ $order->customer->company_name ?? '—' }}</span>
                        <span><strong>№кл-та:</strong> {{ $order->client_order_number }}</span>
                    </div>
                </div>

                <!-- 2 столбца -->
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div class="text-right">
                        <p><strong>Цвет:</strong> {{ $order->colorCatalog->name_en ?? '—' }}
                            {{ $order->colorCode->code ?? '' }}
                        </p> <p><strong>Материал:</strong> {{ $order->material }}</p>
                    </div>
                    <div class="text-left">
                        <p><strong></strong> {{ $order->coatingType->label ?? '—' }}</p>
                        <p><strong>Фрез.:</strong> {{ $order->milling->name ?? '—' }}</p>
                    </div>
                    <div></div>
                </div>

            </div>


            <table class="w-full border-collapse border border-gray-400 text-sm">
                <thead>
                <tr style="background-color: #e0e0e0;">
                    <th style="width:15%">Тип фасада</th>
                    <th style="width:10%">Высота</th>
                    <th style="width:10%">Ширина</th>
                    <th style="width:7%">Кол-во</th>
                    <th style="width:10%">2стр.окр.</th>
                    <th style="width:5%">Толщ.</th>
                    <th style="width:13%">Сверловка</th>
                    <th style="width:30%">Примечания</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->facadeType->name_ru ?? '—' }}</td>
                        <td>{{ $item->height }}</td>
                        <td>{{ $item->width }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->double_sided_coating ? 'Да' : '—' }}</td>
                        <td>{{ $item->thickness->label ?? $item->thickness->value ?? '—' }}</td>
                        <td>{{ $item->drilling->name_ru ?? '—' }}</td>
                        <td>{{ $item->notes }}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="3">Итого фасадов,шт:</td>
                    <td>{{ $totalQuantity }}</td>
                    <td colspan="4"></td>
                </tr>
                <tr>
                    <td colspan="3">Общая площадь,м2:</td>
                    <td>
                        {{ number_format($items->sum(fn($item) => ($item->height * $item->width / 1_000_000) * $item->quantity), 2) }}
                    </td>
                    <td colspan="4"></td>
                </tr>
                </tfoot>
            </table>

            <div class="no-print mt-6 flex justify-center space-x-4">
                <button onclick="window.print()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Печать
                </button>
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.orders.index') }}"
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Назад к заказам
                    </a>
                @elseif(auth()->user()->hasRole('customer'))
                    <a href="{{ route('orders.index') }}"
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Назад к заказам
                    </a>
                @endif
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                    <a href="{{ route('orders.saw', $order->id) }}"
                       class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                        На пилу
                    </a>
                @endif
            </div>
        </div>
    </x-app-layout>



