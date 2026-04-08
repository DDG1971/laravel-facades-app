<x-app-layout>
    <x-slot name="head">
        <x-assets />
        <style>
            @media print {
                /* 1. Настройка колонок */
                th:nth-child(4), td:nth-child(4) { width: 3rem; }
                th:nth-child(5), td:nth-child(5) { width: 3rem; }

                /* 2. Шапка таблицы: цвет и ПОВТОР на каждой странице */
                thead {
                    display: table-header-group; /* Это заставит браузер дублировать шапку */
                }
                thead th {
                    background-color: #e5e7eb !important;
                    color: #000 !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                /* 3. Исправляем обрезку контента */
                .max-h-\[600px\], .overflow-y-auto {
                    max-height: none !important;
                    overflow: visible !important;
                    display: block !important; /* Убираем ограничения контейнера */
                }

                /* 4. Чтобы строки не разрывались пополам между страницами */
                tr {
                    page-break-inside: avoid;
                }

                /* 5. Скрываем лишнее */
                .no-print, .no-print * {
                    display: none !important;
                }

                /* 6. Убираем лишние отступы, чтобы влезло больше данных */
                body {
                    margin: 0;
                    padding: 0;
                }
                .max-w-4xl {
                    max-width: 100% !important;
                    width: 100% !important;
                    margin: 0 !important;
                    padding: 0 !important;
                }
            }
        </style>
    </x-slot>

    <div class="max-w-4xl mx-auto px-8 ml-8">
        <div class="flex flex-col items-start mb-4 ml-16">
            <h1 class="text-xl font-semibold">
                Задание на пилу — Очередь №{{ $order->queue_number }}
            </h1>
            <p class="text-sm"><strong>Клиент:</strong> {{ $order->customer->company_name ?? '—' }}</p>
            <p class="text-sm"><strong>Дата:</strong> {{ $order->created_at->format('d.m.Y') }}</p>
        </div>

        @php
            // 1. Подготавливаем конфиг и коллекцию заранее
            $excludedNames = config('facade.exclude_from_saw', []);

            $filteredItems = $items->filter(function($item) use ($excludedNames) {
                return !in_array($item->facadeType->name_en ?? '', $excludedNames);
            })->map(function($item) {
                // Считаем размеры один раз
                $needs = $item->needsSawAddition();
                $item->print_h = $needs ? $item->height + 4 : $item->height;
                $item->print_w = $needs ? $item->width + 4 : $item->width;
                $item->show_name = !$needs; // Флаг для отображения имени
                return $item;
            });

            // 2. Считаем итоги по уже отфильтрованной коллекции
            $totalQuantity = $filteredItems->sum('quantity');
            $totalArea = $filteredItems->reduce(function ($carry, $item) {
                return $carry + ($item->print_h * $item->print_w / 1_000_000) * $item->quantity;
            }, 0);
        @endphp

        <div class="max-h-[600px] overflow-y-auto border border-gray-300">
            <table class="table-auto border-collapse border border-gray-400 text-sm">
                <thead>
                <tr class="bg-gray-200 text-center">
                    <th class="border border-gray-400 px-1 py-0.5 w-20">Фасад</th>
                    <th class="border border-gray-400 px-1 py-0.5 w-16">Высота</th>
                    <th class="border border-gray-400 px-1 py-0.5 w-16">Ширина</th>
                    <th class="border border-gray-400 px-1 py-0.5 w-14">Кол-во</th>
                    <th class="border border-gray-400 px-1 py-0.5 w-14">Толщ.</th>
                </tr>
                </thead>
                <tbody>
                @foreach($filteredItems as $item)
                    <tr class="text-center">
                        <td class="border border-gray-400 px-1 py-0.5 text-gray-500 text-xs truncate">
                            {{ $item->show_name ? ($item->facadeType->name_ru ?? '') : '' }}
                        </td>
                        <td class="border border-gray-400 px-1 py-0.5">{{ $item->print_h }}</td>
                        <td class="border border-gray-400 px-1 py-0.5">{{ $item->print_w }}</td>
                        <td class="border border-gray-400 px-1 py-0.5">{{ $item->quantity }}</td>
                        <td class="border border-gray-400 px-1 py-0.5">
                            {{ ($item->thickness && $item->thickness->value == 19)
                                ? ''
                                : ($item->thickness->label ?? $item->thickness->value ?? '—')
                            }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr class="bg-green-100 font-bold text-center sticky bottom-0 text-sm">
                    <td colspan="3" class="border border-gray-400 px-1 py-0.5 text-right">Итого:</td>
                    <td class="border border-gray-400 px-1 py-0.5">{{ $totalQuantity }} шт</td>
                    <td class="border border-gray-400 px-1 py-0.5">{{ number_format($totalArea, 2, ',', ' ') }} м²</td>
                </tr>
                </tfoot>
            </table>
        </div>

        <div class="no-print mt-6 flex justify-start space-x-4">
                <!-- Кнопка "Назад" -->
                <a href="{{ url()->previous() }}"
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded">
                    Назад
                </a>

                <!-- Кнопка "На печать" -->
                <button onclick="window.print()"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded">
                    На печать
                </button>
            </div>
        </div>
    </div>
</x-app-layout>





