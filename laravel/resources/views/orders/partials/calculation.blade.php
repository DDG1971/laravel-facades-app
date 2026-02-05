<div class="overflow-x-auto">
    @php
        // 🔹 группа цен приходит из контроллера
        $group = $priceGroup ?? 'retail';
    @endphp

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
                <td class="border px-2 py-1 text-center">
                    @switch($item->coating_mode)
                        @case(1)
                            Двухсторонний
                            @break
                        @case(2)
                            Частичная
                            @break
                        @default
                            —
                    @endswitch
                </td>
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

<!-- 🔹 Переключатель прайса -->
<div class="mt-4">
    <form method="GET" action="">
        <label for="price_group" class="mr-2 font-medium">Группа цен:</label>
        <select id="price_group" name="price_group"
                onchange="this.form.submit()"
                class="border rounded px-2 py-1 text-sm">
            <option value="retail"  {{ $group === 'retail'  ? 'selected' : '' }}>Розница</option>
            <option value="dealer"  {{ $group === 'dealer'  ? 'selected' : '' }}>Дилер</option>
            <option value="private" {{ $group === 'private' ? 'selected' : '' }}>Частный</option>
        </select>
    </form>
</div>



