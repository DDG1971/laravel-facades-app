<x-app-layout>
    <x-slot name="head">
        <x-assets />
        <style>
            @media print {
                th:nth-child(4), td:nth-child(4) { width: 3rem; }
                th:nth-child(5), td:nth-child(5) { width: 3rem; }
                thead th {
                    background-color: #e5e7eb; /* светло‑серый */
                    color: #000;               /* контрастный текст */
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }
            }
        </style>
    </x-slot>

    <div class="max-w-4xl mx-auto px-8 ml-8">
        <div class="flex flex-col items-start mb-4 ml-16">
            <h1 class="text-xl font-semibold">
                Задание на пилу — Очередь №{{ $order->queue_number }}
            </h1>
            <p class="text-sm"><strong>Клиент:</strong> {{ $order->customer->company_name ?? '—' }}</p>
            <p class="text-sm"><strong>Дата:</strong> {{ $order->created_at->format('d.m.Y') }}</p>
        </div>

        @php
            $totalQuantity = 0;
            $totalArea = 0;
            foreach ($items as $item) {
                $excluded = in_array($item->facadeType->name_en ?? '', config('facade.exclude_from_saw', []));
                if ($excluded) continue;

                $needs = $item->needsSawAddition();
                $h = $needs ? $item->height + 4 : $item->height;
                $w = $needs ? $item->width + 4 : $item->width;

                $totalQuantity += $item->quantity;
                $totalArea += ($h * $w / 1_000_000) * $item->quantity;
            }
        @endphp

        <div class="max-h-[600px] overflow-y-auto border border-gray-300">
            <table class="table-auto border-collapse border border-gray-400 text-sm">
                <thead>
                <tr class="bg-gray-200 text-center">
                    <th class="border border-gray-400 px-1 py-0.5 w-20">Фасад</th>
                    <th class="border border-gray-400 px-1 py-0.5 w-16">Высота</th>
                    <th class="border border-gray-400 px-1 py-0.5 w-16">Ширина</th>
                    <th class="border border-gray-400 px-1 py-0.5 w-14">Кол-во</th>
                    <th class="border border-gray-400 px-1 py-0.5 w-14">Толщ.</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    @php
                        $excluded = in_array($item->facadeType->name_en ?? '', config('facade.exclude_from_saw', []));
                        if ($excluded) continue;
                        $needs = $item->needsSawAddition();
                        $h = $needs ? $item->height + 4 : $item->height;
                        $w = $needs ? $item->width + 4 : $item->width;
                    @endphp
                    <tr class="text-center">
                        <td class="border border-gray-400 px-1 py-0.5 text-gray-500 text-xs truncate">
                            @if(!$needs)
                                {{ $item->facadeType->name_ru ?? '' }}
                            @endif
                        </td>
                        <td class="border border-gray-400 px-1 py-0.5">{{ $h }}</td>
                        <td class="border border-gray-400 px-1 py-0.5">{{ $w }}</td>
                        <td class="border border-gray-400 px-1 py-0.5">{{ $item->quantity }}</td>
                        <td class="border border-gray-400 px-1 py-0.5">
                            {{ $item->thickness && $item->thickness->value == 19 ? '' : ($item->thickness->label ?? $item->thickness->value ?? '—') }}
                        </td>

                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="bg-green-100 font-bold text-center sticky bottom-0 text-sm">
                    <td colspan="3" class="border border-gray-400 px-1 py-0.5 text-right">Итого:</td>
                    <td class="border border-gray-400 px-1 py-0.5">{{ $totalQuantity }} шт</td>
                    <td class="border border-gray-400 px-1 py-0.5">{{ number_format($totalArea, 2, ',', ' ') }} м²</td>
                </tr>
                </tfoot>
            </table>
            <div class="no-print mt-6 flex justify-start space-x-4">
                <!-- Кнопка "Назад" -->
                <a href="{{ url()->previous() }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                    Назад
                </a>

                <!-- Кнопка "На печать" -->
                <button onclick="window.print()"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    На печать
                </button>
            </div>
        </div>
    </div>
</x-app-layout>





