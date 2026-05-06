<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Этикетка упаковки</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @page {
            size: 100mm 150mm;
            margin: 0;
        }

        body {
            font-family: 'Arial', sans-serif;
            width: 96mm;
            margin: 2mm auto;
            font-size: 10px;
        }

        .label {
            border: 1px dashed #000;
            padding: 3mm;
            position: relative;
            min-height: 140mm;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 3mm;
            position: relative;
        }
        .header-left {
            flex: 1;
        }

        .order-number {
            font-size: 14px;
            font-weight: bold;
            color: #555;
        }

        .company-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 2mm 0;
        }
        .company {
            font-size: 20px;
            font-weight: bold;
        }

        .client-number {
            font-size: 16px;
            color: #333;
            font-weight: bold;
        }

        .box-number {
            font-size: 16px;
            font-weight: bold;
            text-align: right;
            padding-right: 5mm;
            min-width: 35mm;
        }

        .page-info {
            font-size: 10px;
            color: #888;
            text-align: center;
            margin: 2mm 0;
        }

        hr {
            border: none;
            border-top: 1px dashed #999;
            margin: 2mm 0;
        }

        .info-row {
            font-size: 11px;
            margin-bottom: 1mm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2mm;
        }

        th {
            background: #e0e0e0;
            border: 1px solid #000;
            padding: 1mm;
            font-size: 9px;
            text-align: center;
        }

        td {
            border: 1px solid #000;
            padding: 1mm;
            text-align: center;
            font-size: 10px;
        }

        .check-cell {
            width: 10mm;
            min-width: 10mm;
        }

        .footer {
            margin-top: 3mm;
            font-size: 11px;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="label">
    <div class="header">
        <div class="header-left">
            <div class="order-number">ЗАКАЗ №{{ $order->queue_number }}</div>
            <div class="company-row">
                <div class="company">{{ $order->customer->company_name ?? $order->customer->name ?? '—' }}</div>
                <div class="box-number">Коробка№  ___</div>
            </div>
            <div class="client-number">Клиентский №: {{ $order->client_order_number ?? '—' }}</div>
        </div>
    </div>

    <hr>

    <div class="info-row">
        Цвет: RAL {{ $order->colorCode->code ?? '—' }} {{ $order->coatingType->name ?? '' }}
        &nbsp;|&nbsp;
        Фрезеровка: {{ $order->milling->name ?? '—' }}
    </div>

    <hr>

    <div class="page-info">
        Страница {{ $page }} / {{ $totalPages }}
    </div>

    <table>
        <thead>
        <tr>
            <th>Тип фасада</th>
            <th>Высота</th>
            <th>Ширина</th>
            <th>Кол-во</th>
            <th class="check-cell">✓</th>
            <th>Толщ.</th>
        </tr>
        </thead>
        <tbody>
        @foreach($items as $item)
            <tr>
                <td>{{ $item->facadeType->display_name ?? '—' }}</td>
                <td>{{ $item->height }}</td>
                <td>{{ $item->width }}</td>
                <td>{{ $item->quantity }}</td>
                <td class="check-cell"></td>
                <td>{{ $item->thickness->value ?? 19 }} мм</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="footer">
        Всего в заказе: {{ $order->items->sum('quantity') }} шт
    </div>
</div>
</body>
</html>
