<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { padding: 20px; border: 1px solid #eee; max-width: 600px; }
        .header { font-size: 18px; font-weight: bold; color: #2d3748; }
        .details { margin: 20px 0; padding: 15px; background: #f7fafc; }
        .footer { margin-top: 20px; font-size: 12px; color: #718096; }
    </style>
    <title></title>
</head>
<body>
<div class="container">
    <div class="header">Заказ №{{ $order->queue_number }} принят в работу</div>

    <p>Здравствуйте!</p>
    <p>Мы получили ваш заказ и приступили к его проверке. Наши менеджеры изучат прикрепленные чертежи и спецификации.</p>

    <div class="details">
        <strong>Информация о заказе:</strong><br>
        Дата: {{ $order->created_at->format('d.m.Y H:i') }}<br>
        Материал: {{ $order->material }}<br>
        Цвет: {{ $order->colorCode->code ?? '—' }} ({{ $order->colorCatalog->name_en ?? '—' }})
    </div>

    <p>Как только проверка будет завершена, мы отправим вам итоговый расчет стоимости на эту почту.</p>

    <div class="footer">
        Это автоматическое уведомление. Пожалуйста, не отвечайте на него.
    </div>
</div>
</body>
</html>
