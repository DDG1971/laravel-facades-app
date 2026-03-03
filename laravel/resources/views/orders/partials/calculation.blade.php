<div class="overflow-x-auto">
    @php
        $group = $priceGroup ?? 'retail';
    @endphp

    <table class="min-w-full border border-gray-300 text-sm table-fixed"> {{-- Добавил table-fixed для строгого контроля ширины --}}
        <thead class="bg-gray-100">
        <tr>
            <th class="border px-2 py-1 w-[20%] text-left">Фасад</th> {{-- Увеличили --}}
            <th class="border px-2 py-1 w-[8%]">Выс.</th>
            <th class="border px-2 py-1 w-[8%]">Шир.</th>
            <th class="border px-2 py-1 w-[6%]">Кол.</th>
            <th class="border px-2 py-1 w-[10%]">Площадь</th>
            <th class="border px-2 py-1 w-[10%]">Толщ.</th>
            <th class="border px-2 py-1 w-[8%] text-xs">Окрас 2с.</th> {{-- Уменьшили --}}
            <th class="border px-2 py-1 w-[12%]">Сверловка</th>
            <th class="border px-2 py-1 w-[10%]">Ставка</th> {{-- Уменьшили --}}
            <th class="border px-2 py-1 w-[10%]">Цена</th>
        </tr>
        </thead>
        <tbody>
        @foreach($order->items as $item)
            @php
                $area = ($item->height * $item->width / 1_000_000) * $item->quantity;
                $price = $item->calculatePrice($group);
                $rate = $item->getRate($group);
                $drillingCount = $item->getDrillingCount();
            @endphp
            <tr class="hover:bg-gray-50">
                <td class="border px-2 py-1 break-words">{{ $item->facadeType->name_ru ?? '—' }}</td> {{-- break-words поможет, если имя очень длинное --}}
                <td class="border px-2 py-1 text-center">{{ $item->height }}</td>
                <td class="border px-2 py-1 text-center">{{ $item->width }}</td>
                <td class="border px-2 py-1 text-center">{{ $item->quantity }}</td>
                <td class="border px-2 py-1 text-center">{{ number_format($area, 2, ',', ' ') }}</td>
                <td class="border px-2 py-1 text-center text-xs">{{ $item->thickness?->label ?? $item->thickness?->value ?? '—' }}</td>
                <td class="border px-2 py-1 text-center text-[10px] leading-tight">
                    @switch($item->coating_mode)
                        @case(1) 2-стор @break
                        @case(2) Част @break
                        @default —
                    @endswitch
                </td>
                <td class="border px-2 py-1 text-center">
                    @if($item->drilling)
                        <div class="text-[10px] leading-none">
                            <span class="font-bold">{{ $item->drilling->name_ru }}</span><br>
                            <span class="text-blue-600 italic">{{ $drillingCount }} шт/ф.</span>
                        </div>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </td>
                <td class="border px-2 py-1 text-right text-xs">{{ number_format($rate, 1, ',', ' ') }}</td> {{-- Сократил до 1 знака после запятой для экономии места --}}
                <td class="border px-2 py-1 text-right font-bold">{{ number_format($price, 2, ',', ' ') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="font-bold bg-gray-50 text-base">
            <td colspan="9" class="text-right px-4 py-2">ИТОГО К ОПЛАТЕ:</td>
            <td class="border px-2 py-2 text-right text-blue-700">
                {{ number_format($order->calculateTotal($group), 2, ',', ' ') }}
            </td>
        </tr>
        </tfoot>
    </table>
</div>




