<x-app-layout>
    <x-slot name="head">
        <x-assets/>
        <style>
            @media print {
                @page { size: A4 portrait; margin: 20mm; }
                .no-print { display: none !important; }
                body { margin: 0; padding: 0; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #444; padding: 4px 6px; text-align: center; }
                tfoot td { font-weight: bold; text-align: center; }
                th { background-color: #e0e0e0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            }
            table th, table td { text-align: center; vertical-align: middle; }
            .attachment-img { max-width: 100%; height: auto; margin-top: 8px; border: 1px solid #ccc; }
        </style>
    </x-slot>

    <div class="print-page">
        <!-- Заголовок -->
        <div class="mb-4 text-sm">
            <!-- Очередь и дата: приглушаем стиль -->
            <div class="flex justify-center items-center gap-6 mb-2 text-gray-500">
                <span>Очередь №{{ $order->queue_number }}</span>
                <span>Дата: {{ $order->date_received }}</span>
            </div>

            <!-- Клиент: основной акцент -->
            <div class="flex justify-center items-center gap-4 mb-2 border-b pb-3">
                <h2 class="text-xl font-black text-slate-800 tracking-tight">
                    {{ $order->customer->company_name ?? '—' }}
                </h2>
                <!-- Номер клиента в виде бейджа -->
                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded-md border border-gray-200 font-bold text-xs">
            №{{ $order->client_order_number }}
        </span>
            </div>
        </div>
        {{--шапка--}}
        <div class="flex justify-center items-center gap-8 mb-2 border-b pb-2">
    <span>
        <span class="text-gray-500 font-medium">Цвет:</span>
        <strong class="text-gray-900 font-extrabold ml-1">
            {{ $order->colorCatalog->name_en ?? '' }}
            {{ $order->colorCode->code ?? '' }}
            {{ $order->coatingType->name ?? '' }}
        </strong>
    </span>

            <span>
        <span class="text-gray-500 font-medium">Фрезеровка:</span>
        <strong class="text-gray-900 font-extrabold ml-1">
            {{ $order->milling->name ?? '—' }}
        </strong>
    </span>
        </div>

        <!-- Общий файл заказа -->
        @if($order->attachment_path)
            <div class="mb-6 no-print">
                <h3 class="font-semibold text-sm">Предпросмотр вложения заказа</h3>
                @php $ext = pathinfo($order->attachment_path, PATHINFO_EXTENSION); @endphp

                @if(strtolower($ext) === 'pdf')
                    <iframe src="{{ asset('storage/'.$order->attachment_path) }}"
                            width="100%" height="600px" style="border:1px solid #ccc;">
                    </iframe>
                @elseif(in_array(strtolower($ext), ['jpg','jpeg','png','gif']))
                    <img src="{{ asset('storage/'.$order->attachment_path) }}" alt="Вложение заказа"/>
                @else
                    <a href="{{ asset('storage/'.$order->attachment_path) }}" target="_blank">
                        Скачать вложение заказа
                    </a>
                @endif
            </div>
        @endif

        {{-- Вложения по позициям --}}
        @foreach($order->items as $item)
            @if($item->attachment_path)
                <div class="mb-6 no-print">
                    <h3 class="font-semibold text-sm">
                        Вложение для позиции #{{ $loop->iteration }}
                    </h3>
                    @php $ext = pathinfo($item->attachment_path, PATHINFO_EXTENSION); @endphp

                    @if(strtolower($ext) === 'pdf')
                        <iframe src="{{ asset('storage/'.$item->attachment_path) }}"
                                width="100%" height="400px" style="border:1px solid #ccc;">
                        </iframe>
                    @elseif(in_array(strtolower($ext), ['jpg','jpeg','png','gif']))
                        <img src="{{ asset('storage/'.$item->attachment_path) }}" alt="Вложение позиции"/>
                    @else
                        <a href="{{ asset('storage/'.$item->attachment_path) }}" target="_blank">
                            Скачать вложение позиции
                        </a>
                    @endif
                </div>
            @endif
        @endforeach

        <!-- Таблица позиций -->
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
                <th style="width:30%">Примечания / Чертёж</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->facadeType->name_ru ?? '—' }}</td>
                    <td>{{ $item->height }}</td>
                    <td>{{ $item->width }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>
                        @switch($item->coating_mode)
                            @case(1) Да @break
                            @case(2) Частич @break
                            @default —
                        @endswitch
                    </td>
                    <td>{{ $item->thickness->label ?? $item->thickness->value ?? '—' }}</td>
                    <td>{{ $item->drilling->name_ru ?? '—' }}</td>
                    <td>
                        {{ $item->notes }}
                        @if($item->attachment)
                            @php $ext = pathinfo($item->attachment, PATHINFO_EXTENSION); @endphp
                            @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif']))
                                <img src="{{ Storage::url($item->attachment) }}" alt="Чертёж позиции" class="attachment-img max-w-xs" />
                            @else
                                <a href="{{ Storage::url($item->attachment) }}" target="_blank" class="text-blue-600 hover:underline">
                                    Скачать файл позиции
                                </a>
                            @endif
                        @endif
                    </td>
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

        <!-- Кнопки -->
        <div class="no-print mt-6 flex justify-center space-x-4">
            <!-- Печать доступна ВСЕМ -->
            <button onclick="window.print()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Печать
            </button>
            <!-- Упаковка только для админа и менеджера -->
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                <a href="{{ route('boxes.packing', $order->id) }}" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                    📦 Упаковка
                </a>
            @endif

            <!-- Логика кнопки НАЗАД для каждого типа пользователя -->
            @if(auth()->user()->hasRole('admin'))
                <a href="{{ route('admin.orders.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Назад к заказам
                </a>
            @elseif(auth()->user()->hasRole('manager'))
                <a href="{{ route('manager.orders.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Назад к заказам
                </a>
            @elseif(auth()->user()->hasRole('customer'))
                <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Назад к заказам
                </a>
            @endif

            <!-- Кнопка НА ПИЛУ только для админа и менеджера -->
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                <a href="{{ route('orders.saw', $order->id) }}" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                    На пилу
                </a>
            @endif
        </div>
    </div>
</x-app-layout>




