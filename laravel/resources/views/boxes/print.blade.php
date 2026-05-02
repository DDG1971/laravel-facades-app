<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Этикетка коробки #{{ $box->box_number }}</title>
    <style>
        /* Сброс отступов */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        /* Размер этикетки: 150x100 мм */
        @page {
            size: 150mm 100mm;
            margin: 0;
        }

        body {
            width: 96mm;
            height: 146mm;
            margin: 2mm;
        }

        .label {
            border: 1px dashed #000;
            padding: 4mm;
            height: 100%;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3mm;
        }

        .order-number {
            font-size: 28px;
            font-weight: bold;
        }

        .company {
            font-size: 22px;
            font-weight: bold;
            margin: 2mm 0;
        }

        .client-number {
            font-size: 18px;
            color: #555;
            margin-bottom: 3mm;
        }

        .box-number {
            font-size: 26px;
            font-weight: bold;
            text-align: center;
            margin: 4mm 0;
            background-color: #f0f0f0;
            padding: 2mm;
            border-radius: 2mm;
        }

        hr {
            border: none;
            border-top: 1px dashed #999;
            margin: 3mm 0;
        }

        table {
            width: 100%;
            font-size: 14px;
            border-collapse: collapse;
            margin-bottom: 3mm;
        }

        td {
            padding: 1.5mm 2mm;
        }

        td:first-child {
            color: #555;
            width: 35%;
        }

        td:last-child {
            font-weight: bold;
        }

        .items-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 2mm;
        }

        .item-row {
            font-size: 14px;
            padding: 1mm 0;
            border-bottom: 1px dotted #ccc;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
            margin-top: 3mm;
        }
    </style>
</head>
<body>
<div class="label">
    <div class="header">
        <div class="order-number">ЗАКАЗ №{{ $box->order->queue_number }}</div>
    </div>

    <div class="company">
        {{ $box->order->customer->company_name ?? $box->order->customer->name ?? '—' }}
    </div>
    <div class="client-number">
        Клиентский №: {{ $box->order->client_order_number ?? '—' }}
    </div>

    <hr>

    <table>
        <tr>
            <td>Цвет:</td>
            <td>RAL {{ $box->order->colorCode->code ?? '—' }} ({{ $box->order->colorCatalog->name ?? '' }})</td>
        </tr>
        <tr>
            <td>Покрытие:</td>
            <td>{{ $box->order->coatingType->name ?? '—' }}</td>
        </tr>
        <tr>
            <td>Фрезеровка:</td>
            <td>{{ $box->order->milling->name ?? '—' }}</td>
        </tr>
    </table>

    <hr>

    <div class="box-number">
        КОРОБКА {{ $box->box_number }} / {{ $box->order->boxes->count() }}
    </div>

    <hr>

    <div class="items-title">Содержимое:</div>
    @foreach($box->items as $boxItem)
        <div class="item-row">
            {{ $boxItem->orderItem->facadeType->display_name ?? '—' }}
            {{ $boxItem->orderItem->height }}x{{ $boxItem->orderItem->width }}
            × {{ $boxItem->quantity }} шт
            ({{ $boxItem->orderItem->thickness->value ?? 19 }} мм)
        </div>
    @endforeach

    <hr>

    <div class="total">
        Всего: {{ $box->items->sum('quantity') }} шт
    </div>
</div>
</body>
</html>
