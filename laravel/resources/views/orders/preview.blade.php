<x-app-layout>
    <x-slot name="head">

        <x-assets/>
        <style>
            @media print {
                @page { size: A4 portrait; margin: 20mm; }
                .no-print { display: none !important; }
                body { margin: 0; padding: 0; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #444; padding: 4px 6px; text-align: center; }
                th { background: #f0f0f0; }
                tfoot td { font-weight: bold; text-align: right; }
            }
        </style>
    </x-slot>

    <!-- 🔹 Шапка заказа (без статуса и вложения) -->
    <div class="print-page">
        <h2 class="text-center font-bold text-lg mb-4">Заказ №{{ $order->id }}</h2>
        <div class="mb-4 text-sm">
            <p><strong>Дата получения:</strong> {{ $order->date_received }}</p>
            <p><strong>Клиент:</strong> {{ $order->customer->company_name ?? '—' }}</p>
            <p><strong>№ заказа клиента:</strong> {{ $order->client_order_number }}</p>
            <p><strong>Каталог цветов:</strong> {{ $order->colorCatalog->name_en ?? '—' }}</p>
            <p><strong>Код цвета:</strong> {{ $order->colorCode->code ?? '—' }}</p>
            <p><strong>Тип покрытия:</strong> {{ $order->coatingType->label ?? '—' }}</p>
            <p><strong>Материал:</strong> {{ $order->material }}</p>
            <p><strong>Фрезеровка:</strong> {{ $order->milling->name ?? '—' }}</p>
        </div>

        <!-- 🔹 Таблица позиций (без файла, + и -) -->
        <table class="w-full border-collapse border border-gray-400 text-sm">
            <thead>
            <tr>
                <th>Тип фасада</th>
                <th>Высота</th>
                <th>Ширина</th>
                <th>Кол-во</th>
                <th>2стр.окр.</th>
                <th>Толщ.</th>
                <th>Сверловка</th>
                <th>Примечания</th>
            </tr>
            </thead>
            <tbody>
            @foreach($items as $item)
                <tr>
                    <td>{{ $item->facadeType->name_ru ?? '—' }}</td>
                    <td>{{ $item->height }}</td>
                    <td>{{ $item->width }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->double_sided_coating ? 'Да' : '—' }}</td>
                    <td>{{ $item->thickness }}</td>
                    <td>{{ $item->drilling->name_ru ?? '—' }}</td>
                    <td>{{ $item->notes }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">Итого фасадов:</td>
                <td>{{ $totalQuantity }}</td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td colspan="3">Общая площадь:</td>
                <td>{{ $totalSquare }}</td>
                <td colspan="4"></td>
            </tr>
            </tfoot>
        </table>

        <!-- 🔹 Кнопки (скрываются при печати) -->
        <div class="no-print mt-6 flex justify-center space-x-4">
            <button onclick="window.print()" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                Печать
            </button>
            <a href="{{ route('orders.index') }}" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Назад к заказам
            </a>
        </div>
    </div>
</x-app-layout>



