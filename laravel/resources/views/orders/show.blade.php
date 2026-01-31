<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg">Просмотр заказа</h2>
    </x-slot>
    @if(session('duplicate'))
        <div id="duplicate-alert"
             class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-2 rounded mb-3 flex justify-between
             items-center transition transform duration-500 ease-in-out">
        <span>
            ⚠️ Внимание: у клиента уже есть заказ с таким номером в этом году.
        </span>
            <button onclick="slideUpAlert()"
                    class="text-yellow-700 hover:text-yellow-900 font-bold ml-4">
                ✕
            </button>
        </div>

        <script>
            function slideUpAlert() {
                const alert = document.getElementById('duplicate-alert');
                alert.style.transform = 'translateY(-20px)';
                alert.style.opacity = '0';
                setTimeout(() => alert.style.display = 'none', 500); // ждём завершения анимации
            }
        </script>
    @endif


    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="p-4">
        {{-- Общие параметры заказа --}}
        <p><strong>№ заказа клиента:</strong> {{ $order->client_order_number }}</p>
        <p><strong>Каталог цвета:</strong> {{ $order->colorCatalog->name_en ?? '—' }}</p>
        <p><strong>Код цвета:</strong> {{ $order->colorCode->code ?? '—' }}</p>
        <p><strong>Покрытие:</strong> {{ $order->coatingType->name ?? '—' }}</p>
        <p><strong>Материал:</strong> {{ $order->material ?? '—' }}</p>
        <p><strong>Фрезеровка:</strong> {{ $order->milling->name ?? '—' }}</p>
        <p><strong>Количество деталей:</strong> {{ $order->items->sum('quantity') }}</p>
        <p><strong>Квадратура (общая):</strong> {{ $order->square_meters }}</p>
        <p><strong>Статус:</strong> {{ $order->status->label ?? '—' }}</p>

        {{-- Список позиций заказа --}}
        <h3 class="mt-6 font-semibold">Позиции заказа</h3>
        <table class="mt-2 w-full table-fixed border-collapse border border-gray-300 text-sm">
            <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1 w-24 truncate">Тип фасада</th>
                <th class="border px-2 py-1 w-16">Высота</th>
                <th class="border px-2 py-1 w-16">Ширина</th>
                <th class="border px-2 py-1 w-12">Кол-во</th>
                <th class="border px-2 py-1 w-12">Толщина</th>
                <th class="border px-2 py-1 w-20 truncate">Сверловка</th>
                <th class="border px-2 py-1 w-16">2‑х сторон. окрас</th>
                <th class="border px-2 py-1 w-20 text-center">Квадратура</th>
                <th class="border px-2 py-1 w-20 text-center">Ставка за м²</th>
                <th class="border px-2 py-1 w-20 text-center">Цена</th>
                <th class="border px-2 py-1 w-32 truncate">Примечания</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order->items as $item)
                @php
                    $area = ($item->height * $item->width / 1_000_000) * $item->quantity;
                    $millingBase = $order->milling?->getBasePriceFor('retail') ?? 0;
                    $resolved = $item->facadeType?->resolvePricing($millingBase, 'm2')
                        ?? ['base' => $millingBase, 'unit' => 'm2'];
                    $unitPrice = $resolved['base']
                        + ($item->thickness?->price ?? 0)
                        + ($order->coatingType?->price ?? 0);
                    $finalPrice = $item->calculatePrice('retail');
                @endphp
                <tr>
                    <td class="border px-2 py-1 truncate">{{ $item->facadeType->name_ru ?? '—' }}</td>
                    <td class="border px-2 py-1 text-center">{{ $item->height }}</td>
                    <td class="border px-2 py-1 text-center">{{ $item->width }}</td>
                    <td class="border px-2 py-1 text-center">{{ $item->quantity }}</td>
                    <td class="border px-2 py-1 text-center">
                        {{ $item->thickness->label ?? $item->thickness->value ?? '—' }}
                    </td>
                    <td class="border px-2 py-1 truncate">{{ $item->drilling->name_ru ?? '—' }}</td>
                    <td class="border px-2 py-1 text-center">
                        {{ $item->double_sided_coating ? 'Да' : '—' }}
                    </td>
                    <td class="border px-2 py-1 text-center">
                        {{ number_format($area, 2, ',', ' ') }}
                    </td>
                    <td class="border px-2 py-1 text-center">
                        {{ number_format($unitPrice, 1, ',', ' ') }}
                    </td>
                    <td class="border px-2 py-1 text-center">
                        {{ number_format($finalPrice, 1, ',', ' ') }}
                    </td>
                    <td class="border px-2 py-1 truncate">{{ $item->notes ?? '—' }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="7" class="border px-2 py-1 text-right font-bold">Итого:</td>
                <td class="border px-2 py-1 text-center">{{ number_format($order->total_square, 2, ',', ' ') }}</td>
                <td></td>
                <td class="border px-2 py-1 text-center font-bold">{{ number_format($order->calculateTotal('retail'), 1, ',', ' ') }}</td>
                <td></td>
            </tr>
            </tfoot>
        </table>

        {{-- Кнопки действий --}}
        <div class="mt-4 flex gap-4">
            {{-- Отправить заказ --}}
            <form action="{{ route('orders.submit', $order->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Отправить заказ
                </button>
            </form>

            {{-- Назад (возврат к редактированию текущего заказа) --}}
            <a href="{{ route('orders.edit', $order->id) }}"
               class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                Назад,отредактировать
            </a>
        </div>
    </div>
</x-app-layout>




