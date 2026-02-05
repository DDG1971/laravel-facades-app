<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg">Редактирование заказа</h2>
    </x-slot>


    <!-- 🔹 Форма -->
    <form method="POST" action="{{ route('orders.update', $order->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- 🔹 Шапка заказа -->
        <div class="mx-auto px-4 mt-6">
            <div class="grid grid-cols-4 divide-x divide-y border border-gray-400 w-fit text-sm">
                <!-- Первая строка -->
                <div class="p-2">
                    <label class="block text-xs font-medium text-gray-700">Дата создания</label>
                    <input
                        type="date"
                        class="w-48 border rounded-md px-2 py-1 text-sm bg-gray-100"
                        value="{{ $order->created_at->toDateString() }}"
                        readonly
                        tabindex="-1"
                    >
                </div>
               {{-- <div class="p-2">
                    <label for="customer_id" class="block text-xs font-medium text-gray-700">Клиент</label>
                    <select id="customer_id" name="customer_id"
                            class="w-64 border rounded-md px-2 py-1 bg-white text-sm">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}"
                                {{ old('customer_id', $order->customer_id) == $customer->id ? 'selected' : '' }}>
                                {{ $customer->company_name }}
                            </option>
                        @endforeach
                    </select>
                </div>--}}
                <div class="p-2">
                    <label class="block text-xs font-medium text-gray-700">Компания</label>
                    <input type="text" class="w-64 border rounded-md px-2 py-1 text-sm bg-gray-100"
                           value="{{ $order->customer->company_name }}" readonly>
                    <input type="hidden" name="customer_id" value="{{ $order->customer_id }}">
                </div>
                <div class="p-2">
                    <label for="client_order_number" class="block text-xs font-medium text-gray-700">№ заказа
                        клиента</label>
                    <input
                        type="text"
                        id="client_order_number"
                        name="client_order_number"
                        class="w-64 border rounded-md px-2 py-1 text-sm"
                        value="{{ old('client_order_number', $order->client_order_number) }}"
                    >
                </div>

                <!-- Вторая строка -->
                <div class="p-2"></div>
                <div class="p-2">
                    <label for="color_catalog_id" class="block text-xs font-medium text-gray-700">Каталог цветов</label>
                    <select id="color_catalog_id" name="color_catalog_id"
                            class="w-48 border rounded-md px-2 py-1 bg-white text-sm">
                        @foreach($colorCatalogs as $catalog)
                            <option value="{{ $catalog->id }}"
                                {{ old('color_catalog_id', $order->color_catalog_id) == $catalog->id ? 'selected' : '' }}>
                                {{ $catalog->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="p-2">
                    <label for="color_code_id" class="block text-xs font-medium text-gray-700">Код цвета</label>
                    <select id="color_code_id" name="color_code_id"
                            class="w-64 border rounded-md px-2 py-1 bg-white text-sm">
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}"
                                {{ old('color_code_id', $order->color_code_id) == $color->id ? 'selected' : '' }}>
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
                            <option value="{{ $coating->id }}"
                                {{ old('coating_type_id', $order->coating_type_id) == $coating->id ? 'selected' : '' }}>
                                {{ $coating->label }}
                            </option>
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
                    <span id="file-name" class="ml-2 text-xs text-gray-500">
                        {{ $order->attachment_filename ?? 'Файл не выбран' }}
                    </span>
                </div>
                <div class="p-2">
                    <label for="material" class="block text-xs font-medium text-gray-700">Материал</label>
                    <select id="material" name="material"
                            class="w-48 border rounded-md px-2 py-1 bg-white text-sm">
                        <option value="MDF" {{ old('material', $order->material) == 'MDF' ? 'selected' : '' }}>MDF
                        </option>
                        <option value="Shpon" {{ old('material', $order->material) == 'Shpon' ? 'selected' : '' }}>
                            Шпон
                        </option>
                    </select>
                </div>
                <div class="p-2">
                    <label for="milling_id" class="block text-xs font-medium text-gray-700">Фрезеровка</label>
                    <select id="milling_id" name="milling_id"
                            class="w-64 border rounded-md px-2 py-1 bg-white text-sm">
                        <option value="">—</option>
                        @foreach($millings as $milling)
                            <option value="{{ $milling->id }}"
                                {{ old('milling_id', $order->milling_id) == $milling->id ? 'selected' : '' }}>
                                {{ $milling->name }}
                            </option>
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
                    <col style="width:220px">  <!-- Примечания -->
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
                {{-- существующие строки --}}
                @foreach($order->items as $index => $item)
                    <tr class="text-sm">
                        <td class="border px-0.5 py-0">
                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                            <select name="items[{{ $index }}][facade_type_id]"  class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate">
                                <option value="">—</option>
                                @foreach($facadeTypes as $type)
                                    <option value="{{ $type->id }}" {{ old("items.$index.facade_type_id", $item->facade_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name_ru }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border"><input type="number" name="items[{{ $index }}][height]" value="{{ old("items.$index.height", $item->height) }}"  class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate"></td>
                        <td class="border"><input type="number" name="items[{{ $index }}][width]" value="{{ old("items.$index.width", $item->width) }}"  class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate"></td>
                        <td class="border"><input type="number" name="items[{{ $index }}][quantity]" value="{{ old("items.$index.quantity", $item->quantity) }}"  class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate"></td>
                        <td class="border">
                            <select name="items[{{ $index }}][double_sided_coating]"  class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate">
                                <option value="0" {{ old("items.$index.double_sided_coating", $item->double_sided_coating) == 0 ? 'selected' : '' }}>—</option>
                                <option value="1" {{ old("items.$index.double_sided_coating", $item->double_sided_coating) == 1 ? 'selected' : '' }}>Да</option>
                            </select>
                        </td>
                        <td class="border">
                            <select name="items[{{ $index }}][thickness_id]"
                                    class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate">
                                @foreach($thicknesses as $thickness)
                                    <option value="{{ $thickness->id }}"
                                        {{ old("items.$index.thickness_id", $item->thickness_id) == $thickness->id ? 'selected' : '' }}>
                                        {{ $thickness->label ?? $thickness->value }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border">
                            <select name="items[{{ $index }}][drilling_id]"  class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate">
                                <option value="">—</option>
                                @foreach($drillings as $drilling)
                                    <option value="{{ $drilling->id }}" {{ old("items.$index.drilling_id", $item->drilling_id) == $drilling->id ? 'selected' : '' }}>
                                        {{ $drilling->name_ru }}
                                    </option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border"><input type="text" name="items[{{ $index }}][notes]" value="{{ old("items.$index.notes", $item->notes) }}"  class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-white text-center text-sm truncate"></td>
                        <td class="border text-center no-print">
                            <label class="w-8 h-8 flex items-center justify-center bg-blue-500 text-white rounded cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 12.79V7a2 2 0 00-2-2h-5.79a2 2 0 00-1.42.59l-7.3 7.3a2 2 0 000 2.82l5.3 5.3a2 2 0 002.82 0l7.3-7.3a2 2 0 00.59-1.42z"/>
                                </svg>
                                <input type="file" name="items[{{ $index }}][attachment]" class="hidden">
                            </label>
                        </td>
                        <td class="border text-center no-print">
                            <button type="button" class="add-row bg-green-500 text-white rounded w-8 h-8">+</button>
                        </td>
                        <td class="border text-center no-print">
                            <button type="button" class="remove-row bg-red-600 text-white rounded w-5 h-5">−</button>
                        </td>
                    </tr>
                @endforeach

                {{-- шаблон новой строки --}}
                <tr class="text-sm hidden" id="item-row-template">
                    <td class="border">
                        <input type="hidden" name="items[__INDEX__][id]" value="" disabled >
                        <select name="items[__INDEX__][facade_type_id]"   class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-blue-50 text-center text-sm truncate" disabled >
                            <option value="">—</option>
                            @foreach($facadeTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name_ru }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="border"><input type="number" name="items[__INDEX__][height]"   class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-blue-50 text-center text-sm truncate" disabled></td>
                    <td class="border"><input type="number" name="items[__INDEX__][width]"   class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-blue-50 text-center text-sm truncate" disabled></td>
                    <td class="border"><input type="number" name="items[__INDEX__][quantity]"   class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-blue-50 text-center text-sm truncate" disabled></td>
                    <td class="border">
                        <select name="items[__INDEX__][double_sided_coating]"   class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-blue-50 text-center text-sm truncate" disabled>
                            <option value="0">—</option>
                            <option value="1">Да</option>
                        </select>
                    </td>
                    <td class="border">
                        <select name="items[__INDEX__][thickness_id]"   class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-blue-50 text-center text-sm truncate" disabled>
                            @foreach($thicknesses as $thickness)
                                <option value="{{ $thickness->id }}">{{ $thickness->label ?? $thickness->value }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="border">
                        <select name="items[__INDEX__][drilling_id]"   class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-blue-50 text-center text-sm truncate" disabled>
                            <option value="">—</option>
                            @foreach($drillings as $drilling)
                                <option value="{{ $drilling->id }}">{{ $drilling->name_ru }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="border"><input type="text" name="items[__INDEX__][notes]"   class="w-full min-w-0 border border-gray-400 px-0.5 py-0 bg-blue-50 text-center text-sm truncate" disabled></td>
                    <td class="border text-center no-print">
                        <label class="w-8 h-8 flex items-center justify-center bg-blue-500 text-white rounded cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 12.79V7a2 2 0 00-2-2h-5.79a2 2 0 00-1.42.59l-7.3 7.3a2 2 0 000 2.82l5.3 5.3a2 2 0 002.82 0l7.3-7.3a2 2 0 00.59-1.42z"/>
                            </svg>
                            <input type="file" name="items[__INDEX__][attachment]" class="hidden" disabled>
                        </label>
                    </td>
                    <td class="border text-center no-print">
                        <button type="button" class="add-row bg-green-500 text-white rounded w-8 h-8">+</button>
                    </td>
                    <td class="border text-center no-print">
                        <button type="button" class="remove-row bg-red-600 text-white rounded w-5 h-5">−</button>
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
                    Сохранить изменения
                </button>

                {{--<a href="{{ route('orders.show', $order->id) }}"
                   class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                    Назад
                </a>--}}
            </div>
        </div>

    </form>
</x-app-layout>

