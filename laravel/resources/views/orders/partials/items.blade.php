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
