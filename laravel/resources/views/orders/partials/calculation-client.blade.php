<div class="overflow-x-auto">
    @php
        // 🔹 priceGroup приходит из контроллера (например 'retail', 'dealer', 'private')
        $group = $priceGroup ?? 'retail';
    @endphp

        <!-- 🔹 Шапка заказа -->
    <div style="font-size: 14px; margin-bottom: 20px;">
        <h2>Расчёт заказа №{{ $order->queue_number }}</h2>
        <p><strong>Дата заказа:</strong> {{ $orderDate instanceof \Carbon\Carbon ? $orderDate->format('d.m.Y') : $orderDate }}</p>
        <p><strong>№ заказа клиента:</strong> {{ $clientNumber ?? '—' }}</p>
        <p><strong>Материал:</strong> {{ is_object($material) ? $material->label : $material }}</p>
        <p><strong>Цвет:</strong> {{ $order->color?->name_ru ?? '—' }}</p>
        <p><strong>Покрытие:</strong> {{ $order->coatingType?->label ?? '—' }}</p>
    </div>

    <table class="min-w-full border border-gray-300 text-sm">
        <thead class="bg-gray-100">
        <tr>
            <th class="border px-2 py-1">Фасад</th>
            <th class="border px-2 py-1">Высота</th>
            <th class="border px-2 py-1">Ширина</th>
            <th class="border px-2 py-1">Кол-во</th>
            <th class="border px-2 py-1">Площадь, м²</th>
            <th class="border px-2 py-1">Толщина</th>
            <th class="border px-2 py-1">2-стор. окрас</th>
            <th class="border px-2 py-1">Ставка (за м²)</th>
            <th class="border px-2 py-1">Цена</th>
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
                <td class="border px-2 py-1">{{ $item->facadeType->name_ru ?? '—' }}</td>
                <td class="border px-2 py-1 text-center">{{ $item->height }}</td>
                <td class="border px-2 py-1 text-center">{{ $item->width }}</td>
                <td class="border px-2 py-1 text-center">{{ $item->quantity }}</td>
                <td class="border px-2 py-1 text-center">{{ number_format($area, 2, ',', ' ') }}</td>
                <td class="border px-2 py-1 text-center">{{ $item->thickness?->label ?? $item->thickness?->value ?? '—' }}</td>
                <td class="border px-2 py-1 text-center">{{ $item->isDoubleSided() ? 'Да' : 'Нет' }}</td>
                <td class="border px-2 py-1 text-right">{{ number_format($rate, 2, ',', ' ') }}</td>
                <td class="border px-2 py-1 text-right">{{ number_format($price, 2, ',', ' ') }}</td>
            </tr>
        @endforeach
        </tbody>
        <tfoot>
        <tr class="font-bold bg-gray-50">
            <td colspan="8" class="text-right px-2 py-1">Итого:</td>
            <td class="border px-2 py-1 text-right">
                {{ number_format($order->calculateTotal($group), 2, ',', ' ') }}
            </td>
        </tr>
        <tr>
            <td colspan="9" class="px-2 py-1 text-xs text-gray-600">
                <strong>Пояснение:</strong>
                Ставка = базовая цена за м² + наценка за толщину + наценка за покрытие.
                Доплата за двухсторонний окрас рассчитывается отдельно и добавляется к итоговой цене.
            </td>
        </tr>
        </tfoot>
    </table>
</div>



