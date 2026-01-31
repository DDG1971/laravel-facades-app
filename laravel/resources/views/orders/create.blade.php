<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg">Создание заказа</h2>
    </x-slot>

    <!-- 🔹 Форма -->
    <form method="POST" action="{{ route('admin.orders.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- 🔹 Шапка заказа -->
        <div class="mx-auto px-4 mt-6">
            <div class="grid grid-cols-4 divide-x divide-y border border-gray-400 w-fit text-sm">
                <!-- Первая строка -->
                <div class="p-2">
                    <label class="block text-xs font-medium text-gray-700">Дата создания</label>
                    <input
                        type="date"
                        class="w-48 border rounded-md px-2 py-1 text-sm bg-gray-100"
                        value="{{ now()->toDateString() }}"
                        readonly
                        tabindex="-1"
                    >
                    {{-- Не отправляю дату из формы; задаю её на сервере в контроллере --}}
                </div>
                <div class="p-2">
                    <label for="customer_id" class="block text-xs font-medium text-gray-700">Клиент</label>
                    <select id="customer_id" name="customer_id"
                            class="w-64 border rounded-md px-2 py-1 bg-white text-sm">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="p-2">
                    <label for="client_order_number" class="block text-xs font-medium text-gray-700">№ заказа клиента</label>
                    <input
                        type="text"
                        id="client_order_number"
                        name="client_order_number"
                        class="w-64 border rounded-md px-2 py-1 text-sm"
                        value="{{ old('client_order_number', 'б/н') }}"
                    >
                </div>

                <!-- Вторая строка -->
                <div class="p-2"></div>
                <div class="p-2">
                    <label for="color_catalog_id" class="block text-xs font-medium text-gray-700">Каталог цветов</label>
                    <select id="color_catalog_id" name="color_catalog_id"
                            class="w-48 border rounded-md px-2 py-1 bg-white text-sm">
                        @foreach($colorCatalogs as $catalog)
                            <option value="{{ $catalog->id }}">{{ $catalog->name_en }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="p-2">
                    <label for="color_code_id" class="block text-xs font-medium text-gray-700">Код цвета</label>
                    <select id="color_code_id" name="color_code_id"
                            class="w-64 border rounded-md px-2 py-1 bg-white text-sm">
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}"
                                {{ old('color_code_id') == $color->id || $color->code == 9003 ? 'selected' : '' }}>
                                {{ $color->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="p-2">
                    <label for="coating_type_id" class="block text-xs font-medium text-gray-700">Тип покрытия</label>
                    <select id="coating_type_id" name="coating_type_id"
                            class="w-64 border rounded-md px-2 py-1 bg-white text-sm">
                        @foreach($coatingTypes as $coating)
                            <option value="{{ $coating->id }}">{{ $coating->label }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Третья строка -->
                <div class="p-2"></div>
                <div class="p-2 no-print">
                    <label for="order_attachment" class="block text-xs font-medium text-gray-700">Файл вложения</label>
                    <input type="file" name="order_attachment" id="order_attachment" class="hidden"
                           onchange="document.getElementById('file-name').textContent = this.files[0]?.name || 'Файл не выбран';"/>
                    <button type="button"
                            onclick="document.getElementById('order_attachment').click()"
                            class="px-2 py-1 border rounded-md bg-gray-100 hover:bg-gray-200 text-sm">
                        Выберите файл
                    </button>
                    <span id="file-name" class="ml-2 text-xs text-gray-500">Файл не выбран</span>
                </div>
                <div class="p-2">
                    <label for="material" class="block text-xs font-medium text-gray-700">Материал</label>
                    <select id="material" name="material"
                            class="w-48 border rounded-md px-2 py-1 bg-white text-sm">
                        <option value="MDF">MDF</option>
                        <option value="Shpon">Шпон</option>
                    </select>
                </div>
                <div class="p-2">
                    <label for="milling_id" class="block text-xs font-medium text-gray-700">Фрезеровка</label>
                    <select id="milling_id" name="milling_id"
                            class="w-64 border rounded-md px-2 py-1 bg-white text-sm">
                        <option value="">—</option>
                        @foreach($millings as $milling)
                            <option value="{{ $milling->id }}">{{ $milling->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>


        <!-- 🔹 Позиции заказа -->
        <h4 class="text-lg font-semibold mb-2">Позиции заказа</h4>

        <div class="overflow-x-auto pb-32">
            <table class="min-w-full table-fixed border border-gray-300 border-collapse" id="order-items-table">
                <colgroup>
                    <col style="width:96px">   <!-- Тип фасада -->
                    <col style="width:80px">   <!-- Высота -->
                    <col style="width:80px">   <!-- Ширина -->
                    <col style="width:60px">   <!-- Кол-во -->
                    <col style="width:40px">   <!-- 2стр.окр. -->
                    <col style="width:40px">   <!-- Толщ. -->
                    <col style="width:96px">   <!-- Сверловка -->
                    <col style="width:220px"> <!-- Примечания -->
                    <col style="width:40px">   <!-- Файл -->
                    <col style="width:40px">   <!-- + -->
                    <col style="width:40px">   <!-- − -->
                </colgroup>

                <thead class="bg-gray-100 text-sm">
                <tr>
                    <th class="px-1 py-0.5 border text-left">Тип фасада</th>
                    <th class="px-1 py-0.5 border text-left">Высота</th>
                    <th class="px-1 py-0.5 border text-left">Ширина</th>
                    <th class="px-1 py-0.5 border text-left">Кол-во</th>
                    <th class="px-1 py-0.5 border text-left">2стр.окр.</th>
                    <th class="px-1 py-0.5 border text-left">Толщ.</th>
                    <th class="px-1 py-0.5 border text-left">Сверловка</th>
                    <th class="px-1 py-0.5 border text-left">Примечания</th>
                    <th class="px-1 py-0.5 border text-center no-print">Файл</th>
                    <th class="px-1 py-0.5 border text-center no-print">+</th>
                    <th class="px-1 py-0.5 border text-center no-print">−</th>
                </tr>
                </thead>

                <tbody id="order-items-body">
                <!-- 🔹 Шаблон строки (скрытый, для клонирования JS) -->
                <tr id="item-row-template" class="hidden text-sm">
                    <td class="border px-0.5 py-0">
                        <select name="items[__INDEX__][facade_type_id]"
                                class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate">
                            <option value="">—</option>
                            @foreach($facadeTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name_ru }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="border px-0.5 py-0">
                        <input type="number" name="items[__INDEX__][height]"
                               class="w-full min-w-0 border border-gray-400 px-0.5 py-0 text-center text-sm">
                    </td>
                    <td class="border px-0.5 py-0">
                        <input type="number" name="items[__INDEX__][width]"
                               class="w-full min-w-0 border border-gray-400 px-0.5 py-0 text-center text-sm">
                    </td>
                    <td class="border px-0.5 py-0">
                        <input type="number" name="items[__INDEX__][quantity]"
                               class="w-full min-w-0 border border-gray-400 px-0.5 py-0 text-center text-sm">
                    </td>
                    <td class="border px-0.5 py-0">
                        <select name="items[__INDEX__][double_sided_coating]"
                                class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm">
                            <option value="0">—</option>
                            <option value="1">Да</option>
                        </select>
                    </td>

                    <td class="border px-0.5 py-0">
                        <select name="items[__INDEX__][thickness_id]"
                                class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm">
                            @foreach($thicknesses as $thickness)
                                <option value="{{ $thickness->id }}">{{ $thickness->label ?? $thickness->value }}</option>
                            @endforeach
                        </select>
                    </td>

                    <td class="border px-0.5 py-0">
                        <select name="items[__INDEX__][drilling_id]"
                                class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate">
                            <option value="">—</option>
                            @foreach($drillings as $drilling)
                                <option value="{{ $drilling->id }}">{{ $drilling->name_ru }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="border px-0.5 py-0">
                        <input type="text" name="items[__INDEX__][notes]"
                               class="w-full min-w-0 border border-gray-400 px-0.5 py-0 text-center text-sm">
                    </td>
                    <td class="border px-0.5 py-0 text-center no-print">
                        <div class="flex justify-center">
                            <label
                                class="w-8 h-8 flex items-center justify-center bg-blue-500 text-white rounded cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 12.79V7a2 2 0 00-2-2h-5.79a2 2 0 00-1.42.59l-7.3 7.3a2 2 0 000 2.82l5.3 5.3a2 2 0 002.82 0l7.3-7.3a2 2 0 00.59-1.42z"/>
                                </svg>
                                <input type="file" name="items[__INDEX__][attachment]" class="hidden">
                            </label>
                        </div>
                    </td>
                    <td class="border px-0.5 py-0 text-center no-print">
                        <div class="flex justify-center">
                            <button type="button"
                                    class="w-8 h-8 flex items-center justify-center bg-green-500 text-white rounded add-row">+
                            </button>
                        </div>
                    </td>
                    <td class="border px-0.5 py-0 text-center no-print">
                        <button type="button"
                                class="w-5 h-5 bg-red-600 text-white hover:bg-red-700 remove-row text-xs">−
                        </button>
                    </td>
                </tr>

                <!-- 🔹 Первая реальная строка -->
                <tr class="text-sm">
                    <td class="border px-0.5 py-0">
                        <select name="items[0][facade_type_id]"
                                class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate">
                            <option value="">—</option>
                            @foreach($facadeTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name_ru }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="border px-0.5 py-0">
                        <input type="number" name="items[0][height]"
                               class="w-full min-w-0 border border-gray-400 px-0.5 py-0 text-center text-sm">
                    </td>
                    <td class="border px-0.5 py-0">
                        <input type="number" name="items[0][width]"
                               class="w-full min-w-0 border border-gray-400 px-0.5 py-0 text-center text-sm">
                    </td>
                    <td class="border px-0.5 py-0">
                        <input type="number" name="items[0][quantity]"
                               class="w-full min-w-0 border border-gray-400 px-0.5 py-0 text-center text-sm">
                    </td>
                    <td class="border px-0.5 py-0">
                        <select name="items[0][double_sided_coating]"
                                class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm">
                            <option value="0">—</option>
                            <option value="1">Да</option>
                        </select>
                    </td>

                    <td class="border px-0.5 py-0">
                        <select name="items[0][thickness_id]"
                                class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm">
                            @foreach($thicknesses as $thickness)
                                <option value="{{ $thickness->id }}" {{ $thickness->value == 19 ? 'selected' : '' }}>
                                    {{ $thickness->label ?? $thickness->value }}
                                </option>
                            @endforeach
                        </select>
                    </td>

                    <td class="border px-0.5 py-0">
                        <select name="items[0][drilling_id]"
                                class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate">
                            <option value="">—</option>
                            @foreach($drillings as $drilling)
                                <option value="{{ $drilling->id }}">{{ $drilling->name_ru }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="border px-0.5 py-0">
                        <input type="text" name="items[0][notes]"
                               class="w-full min-w-0 border border-gray-400 px-0.5 py-0 text-center text-sm">
                    </td>
                    <td class="border px-0.5 py-0 text-center no-print">
                        <div class="flex justify-center">
                            <label
                                class="w-8 h-8 flex items-center justify-center bg-blue-500 text-white rounded cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 12.79V7a2 2 0 00-2-2h-5.79a2 2 0 00-1.42.59l-7.3 7.3a2 2 0 000 2.82l5.3 5.3a2 2 0 002.82 0l7.3-7.3a2 2 0 00.59-1.42z"/>
                                </svg>
                                <input type="file" name="items[0][attachment]" class="hidden">
                            </label>
                        </div>
                    </td>

                    <td class="border px-0.5 py-0 text-center no-print">
                        <div class="flex justify-center">
                            <button type="button"
                                    class="w-8 h-8 flex items-center justify-center bg-green-500 text-white rounded add-row">
                                <!-- Heroicons Plus Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4v16m8-8H4"/>
                                </svg>
                            </button>
                        </div>
                    </td>

                    <td class="border px-0.5 py-0 text-center no-print">
                        <button type="button"
                                class="w-5 h-5 bg-red-600 text-white hover:bg-red-700 remove-row text-xs">−
                        </button>
                    </td>
                </tr>
                </tbody>



            </table>
        </div>
        <!-- Футер -->
        <div
            class="fixed bottom-0 left-0 right-0 bg-gray-100 border-t border-gray-300 px-6 py-2 flex justify-between items-center">
            <!-- Итоги -->

            <div class="flex space-x-8 text-sm font-semibold">
                <div>Итого фасадов: <span id="total-quantity">0</span></div>
                <div>Общая площадь: <span id="total-square">0</span> м²</div>
            </div>

            <!-- Кнопки -->
            <div class="flex space-x-4">
                <button type="button" onclick="window.print()"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Предварительный просмотр
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Отправить
                </button>

                <button type="button" id="calculate-btn"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Расчёт
                </button>
            </div>
            <!-- 🔹 Вывод ошибок -->
            @if ($errors->any())
                <div class="mt-4 text-red-600 text-sm">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

    </form>

</x-app-layout>

