<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Этикетка коробки #{{ $box->box_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: 100mm 150mm;
            margin: 0;
        }

        body {
            font-family: 'Arial', 'Helvetica', sans-serif;
            width: 96mm;
            height: 146mm;
            margin: 2mm;
            font-size: 12px;
            line-height: 1.3;
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
            margin-bottom: 2mm;
        }

        .order-number {
            font-size: 24px;
            font-weight: bold;
        }

        .company {
            font-size: 20px;
            font-weight: bold;
            margin: 2mm 0;
        }

        .client-number {
            font-size: 14px;
            color: #333;
        }

        .client-value {
            font-size: 18px;
            font-weight: bold;
        }

        .box-number {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
            margin: 3mm 0;
            background-color: #f0f0f0;
            padding: 2mm;
            border-radius: 2mm;
        }

        hr {
            border: none;
            border-top: 1px dashed #999;
            margin: 3mm 0;
        }

        .info-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
            margin-bottom: 3mm;
        }

        .info-table td {
            padding: 1mm 2mm;
        }

        .info-table td:first-child {
            color: #555;
            width: 25%;
        }

        .info-table td:last-child {
            font-weight: bold;
        }

        .items-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 2mm;
        }

        /* Таблица содержимого */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .items-table th {
            background-color: #e0e0e0;
            border: 1.5px solid #000;  /* было 1px solid #999 */
            padding: 1.5mm 1mm;
            text-align: center;
            font-size: 10px;
        }

        .items-table td {
            border: 1.5px solid #000;  /* было 1px solid #999 */
            padding: 1mm;
            text-align: center;
        }

        .total {
            font-size: 16px;
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
        Клиентский №: <span class="client-value">{{ $box->order->client_order_number ?? '—' }}</span>
    </div>

    <hr>

    <table class="info-table">
        <tr>
            <td>Цвет:</td>
            <td>RAL {{ $box->order->colorCode->code ?? '—' }} {{ $box->order->coatingType->name ?? '' }}</td>
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

    <table class="items-table">
        <thead>
        <tr>
            <th>Тип фасада</th>
            <th>Высота</th>
            <th>Ширина</th>
            <th>Кол-во</th>
            <th>Толщ.</th>
        </tr>
        </thead>
        <tbody>
        @foreach($box->items as $boxItem)
            <tr>
                <td>{{ $boxItem->orderItem->facadeType->display_name ?? '—' }}</td>
                <td>{{ $boxItem->orderItem->height }}</td>
                <td>{{ $boxItem->orderItem->width }}</td>
                <td>{{ $boxItem->quantity }}</td>
                <td>{{ $boxItem->orderItem->thickness->value ?? 19 }} мм</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <hr>

    <div class="total">
        Всего: {{ $box->items->sum('quantity') }} шт
    </div>
</div>
</body>
</html>
