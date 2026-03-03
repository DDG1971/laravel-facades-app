<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #333; }
        h2 { margin-bottom: 10px; text-align: center; }
        .header { margin-bottom: 15px; border-bottom: 1px solid #ccc; padding-bottom: 10px; }
        .header p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
        th, td { border: 1px solid #444; padding: 4px 2px; text-align: center; word-wrap: break-word; }
        th { background: #eee; font-size: 10px; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .footer { margin-top: 15px; font-size: 9px; color: #555; line-height: 1.4; }
    </style>
</head>
<body>
<h2>Расчёт заказа №{{ $order->queue_number }}</h2>

<div class="header">
    <p><strong>Дата создания:</strong> {{ $order->created_at->format('d.m.Y') }}</p>
    <p><strong>Клиент:</strong> {{ $order->customer->company_name ?? '—' }}</p>
    <p><strong>№ заказа клиента:</strong> {{ $order->client_order_number ?? '—' }}</p>
    <p><strong>Цвет:</strong> {{ $order->colorCatalog->name_en ?? '' }} {{ $order->colorCode->code ?? '' }} ({{ $order->coatingType->label ?? '—' }})</p>
    <p><strong>Фрезеровка:</strong> {{ $order->milling->name ?? '—' }} | <strong>Материал:</strong> {{ $order->material ?? '—' }}</p>
</div>

@php $group = $priceGroup ?? 'retail'; @endphp

<table>
    <thead>
    <tr>
        <th style="width: 18%;">Фасад</th>
        <th style="width: 7%;">Выс.</th>
        <th style="width: 7%;">Шир.</th>
        <th style="width: 5%;">Кол.</th>
        <th style="width: 8%;">М²</th>
        <th style="width: 10%;">Толщ.</th>
        <th style="width: 8%;">2-с. окр</th>
        <th style="width: 15%;">Сверловка</th> {{-- НОВАЯ КОЛОНКА --}}
        <th style="width: 10%;">Ставка</th>
        <th style="width: 12%;">Цена</th>
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
            <td style="text-align: left;">{{ $item->facadeType->name_ru ?? '—' }}</td>
            <td>{{ $item->height }}</td>
            <td>{{ $item->width }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($area, 2) }}</td>
            <td>{{ $item->thickness?->label ?? $item->thickness?->value ?? '—' }}</td>
            <td style="font-size: 9px;">
                {{ $item->coating_mode == 1 ? 'Да' : ($item->coating_mode == 2 ? 'Част' : '—') }}
            </td>
            <td style="font-size: 9px;">
                @if($item->drilling)
                    <strong>{{ $item->drilling->name_ru }}</strong><br>
                    ({{ $drillingCount }} шт/ф)
                @else
                    —
                @endif
            </td>
            <td class="text-right">{{ number_format($rate, 1, ',', ' ') }}</td>
            <td class="text-right font-bold">{{ number_format($price, 2, ',', ' ') }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr style="background: #f0f0f0;">
        <td colspan="9" class="text-right font-bold">ИТОГО К ОПЛАТЕ:</td>
        <td class="text-right font-bold" style="font-size: 13px;">{{ number_format($order->calculateTotal($group), 2, ',', ' ') }}</td>
    </tr>
    </tfoot>
</table>

<div class="footer">
    <strong>Пояснение:</strong> Ставка = базовая цена за м² + наценка за толщину + наценка за покрытие. <br>
    Доплата за двухсторонний окрас и услуги сверления рассчитываются отдельно и включены в итоговую цену позиции.
</div>
</body>
</html>


