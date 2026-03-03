<div class="overflow-x-auto">
    @php
        $group = $priceGroup ?? 'retail';
    @endphp

        <!-- 🔹 Шапка заказа (оставляем, как была, она нужна для PDF) -->
    <div style="font-size: 14px; margin-bottom: 20px;">
        <h2 style="margin-bottom: 5px;">Расчёт заказа №{{ $order->queue_number }}</h2>
        <p style="margin: 2px 0;"><strong>Дата заказа:</strong> {{ $order->created_at->format('d.m.Y') }}</p>
        <p style="margin: 2px 0;"><strong>№ заказа клиента:</strong> {{ $order->client_order_number ?? '—' }}</p>
        <p style="margin: 2px 0;"><strong>Цвет:</strong> {{ $order->colorCatalog->name_en ?? '' }} {{ $order->colorCode->code ?? '' }} {{ $order->coatingType->name ?? '' }}</p>
        <p style="margin: 2px 0;"><strong>Фрезеровка:</strong> {{ $order->milling->name ?? '—' }}</p>
    </div>

    <table style="width: 100%; border-collapse: collapse; font-size: 12px;" border="1">
        <thead style="background-color: #f3f4f6;">
        <tr>
            <th style="padding: 4px; width: 20%;">Фасад</th>
            <th style="padding: 4px; width: 8%;">Выс.</th>
            <th style="padding: 4px; width: 8%;">Шир.</th>
            <th style="padding: 4px; width: 6%;">Кол.</th>
            <th style="padding: 4px; width: 10%;">М²</th>
            <th style="padding: 4px; width: 10%;">Толщ.</th>
            <th style="padding: 4px; width: 8%;">Окрас 2с.</th>
            <th style="padding: 4px; width: 12%;">Сверловка</th>
            <th style="padding: 4px; width: 10%;">Ставка</th>
            <th style="padding: 4px; width: 10%;">Цена</th>
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
            <tr>
                <td style="padding: 4px;">{{ $item->facadeType->name_ru ?? '—' }}</td>
                <td style="padding: 4px; text-align: center;">{{ $item->height }}</td>
                <td style="padding: 4px; text-align: center;">{{ $item->width }}</td>
                <td style="padding: 4px; text-align: center;">{{ $item->quantity }}</td>
                <td style="padding: 4px; text-align: center;">{{ number_format($area, 2) }}</td>
                <td style="padding: 4px; text-align: center;">{{ $item->thickness?->label ?? '—' }}</td>
                <td style="padding: 4px; text-align: center; font-size: 10px;">
                    {{ $item->coating_mode == 1 ? '2-стор' : ($item->coating_mode == 2 ? 'Част' : '—') }}
                </td>
                <td style="padding: 4px; text-align: center; font-size: 10px;">
                    @if($item->drilling)
                        <strong>{{ $item->drilling->name_ru }}</strong><br>
                        {{ $drillingCount }} шт/ф.
                    @else
                        —
                    @endif
                </td>
                <td style="padding: 4px; text-align: right;">{{ number_format($rate, 1) }}</td>
                <td style="padding: 4px; text-align: right; font-weight: bold;">{{ number_format($price, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr style="background-color: #f9fafb; font-weight: bold;">
            <td colspan="9" style="padding: 8px; text-align: right;">ИТОГО К ОПЛАТЕ:</td>
            <td style="padding: 8px; text-align: right; color: #1d4ed8;">
                {{ number_format($order->calculateTotal($group), 2) }}
            </td>
        </tr>
        </tfoot>
    </table>
</div>


