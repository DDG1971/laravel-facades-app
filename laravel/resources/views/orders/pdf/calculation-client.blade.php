<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h2 { margin-bottom: 10px; }
        .header { margin-bottom: 15px; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #444; padding: 4px; text-align: center; }
        th { background: #eee; }
        .footer { margin-top: 10px; font-size: 10px; color: #555; }
    </style>
    <title> </title>
</head>
<body>
<h2>Расчёт заказа №{{ $order->queue_number }}</h2>

<div class="header">
    <p><strong>Дата создания:</strong> {{ $order->created_at->format('d.m.Y') }}</p>
    <p><strong>Клиент:</strong> {{ $order->customer->company_name ?? '—' }}</p>
    <p><strong>№ заказа клиента:</strong> {{ $order->client_order_number ?? '—' }}</p>
    <p><strong>Каталог цветов:</strong> {{ $order->colorCatalog->name_en ?? '—' }}</p>
    <p><strong>Код цвета:</strong> {{ $order->colorCode->code ?? '—' }}</p>
    <p><strong>Тип покрытия:</strong> {{ $order->coatingType->label ?? '—' }}</p>
    <p><strong>Материал:</strong> {{ $order->material ?? '—' }}</p>
    <p><strong>Фрезеровка:</strong> {{ $order->milling->name ?? '—' }}</p>
</div>

@php $group = $priceGroup ?? 'retail'; @endphp

<table>
    <thead>
    <tr>
        <th>Фасад</th>
        <th>Высота</th>
        <th>Ширина</th>
        <th>Кол-во</th>
        <th>Площадь, м²</th>
        <th>Толщина</th>
        <th>2-стор. окрас</th>
        <th>Ставка (за м²)</th>
        <th>Цена</th>
    </tr>
    </thead>
    <tbody>
    @foreach($order->items as $item)
        @php
            $area = ($item->height * $item->width / 1_000_000) * $item->quantity;
            $price = $item->calculatePrice($group);
            $millingBase = $item->order->milling?->getBasePriceFor($group) ?? 0;
            $facadePricing = $item->facadeType?->resolvePricing($millingBase, 'm2')
                ?? ['base' => $millingBase, 'unit' => 'm2'];
            $rate = $facadePricing['base']
                    + ($item->thickness?->price ?? 0)
                    + ($item->order->coatingType?->price ?? 0);
        @endphp
        <tr>
            <td>{{ $item->facadeType->name_ru ?? '—' }}</td>
            <td>{{ $item->height }}</td>
            <td>{{ $item->width }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($area, 2, ',', ' ') }}</td>
            <td>{{ $item->thickness?->label ?? $item->thickness?->value ?? '—' }}</td>
            <td>{{ $item->isDoubleSided() ? 'Да' : 'Нет' }}</td>
            <td>{{ number_format($rate, 2, ',', ' ') }}</td>
            <td>{{ number_format($price, 2, ',', ' ') }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <td colspan="8" style="text-align:right"><strong>Итого:</strong></td>
        <td>{{ number_format($order->calculateTotal($group), 2, ',', ' ') }}</td>
    </tr>
    </tfoot>
</table>

<div class="footer">
    <strong>Пояснение:</strong> Ставка = базовая цена за м² + наценка за толщину + наценка за покрытие.
    Доплата за двухсторонний окрас рассчитывается отдельно и добавляется к итоговой цене.
</div>
</body>
</html>

