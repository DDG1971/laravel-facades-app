<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Этикетка коробки #{{ $box->box_number }}</title>
    <style>
        body {
            font-family: monospace;
            width: 80mm;
            margin: 0 auto;
            padding: 2mm;
        }
        .label {
            border: 1px dashed #000;
            padding: 3mm;
        }
        h3 { margin: 0 0 2mm; }
        hr { border-top: 1px dashed #999; margin: 3mm 0; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 1mm 0; }
    </style>
</head>
<body>
<div class="label">
    <h3>ЗАКАЗ №{{ $box->order->queue_number }}</h3>
    <strong>{{ $box->order->customer->company_name ?? $box->order->customer->name ?? '—' }}</strong><br>
    <small>Клиентский №: {{ $box->order->client_order_number ?? '—' }}</small>
    <hr>
    <table>
        <tr><td>Цвет:</td><td>RAL {{ $box->order->colorCode->code ?? '—' }} ({{ $box->order->colorCatalog->name ?? '' }})</td></tr>
        <tr><td>Покрытие:</td><td>{{ $box->order->coatingType->name ?? '—' }}</td></tr>
        <tr><td>Фрезеровка:</td><td>{{ $box->order->milling->name ?? '—' }}</td></tr>
    </table>
    <hr>
    <strong>КОРОБКА {{ $box->box_number }} / {{ $box->order->boxes->count() }}</strong>
    <hr>
    <strong>Содержимое:</strong><br>
    @foreach($box->items as $boxItem)
        {{ $boxItem->orderItem->facadeType->display_name ?? '—' }}
        {{ $boxItem->orderItem->height }}x{{ $boxItem->orderItem->width }}
        × {{ $boxItem->quantity }} шт
        ({{ $boxItem->orderItem->thickness->value ?? 19 }} мм)<br>
    @endforeach
    <hr>
    <strong>Всего: {{ $box->items->sum('quantity') }} шт</strong>
</div>
</body>
</html>
