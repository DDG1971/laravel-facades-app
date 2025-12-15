<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl">Новый заказ</h2>
        <h2 class="font-semibold text-xl text-red-500">Новый заказ</h2>

    </x-slot>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- 🔹 Первый ряд: № заказа, дата, клиент, статус -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="order_number" class="block text-sm font-medium text-gray-700">№ заказа</label>
                    <input type="text" name="order_number" id="order_number"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring focus:ring-indigo-500">
                </div>
                <div>
                    <label for="date_received" class="block text-sm font-medium text-gray-700">Дата получения</label>
                    <input type="date" name="date_received" id="date_received" value="{{ now()->toDateString() }}"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring focus:ring-indigo-500">
                </div>
                <div>
                    <label for="customer_id" class="block text-sm font-medium text-gray-700">Клиент</label>
                    <select name="customer_id" id="customer_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:ring focus:ring-indigo-500">
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->company_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="status_id" class="block text-sm font-medium text-gray-700">Статус заказа</label>
                    <select name="status_id" id="status_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:ring focus:ring-indigo-500">
                        @foreach($statuses as $status)
                            <option value="{{ $status->id }}">{{ $status->label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 🔹 Второй ряд: Материал, Каталог цветов, Код цвета, Тип покрытия -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label for="material" class="block text-sm font-medium text-gray-700">Материал</label>
                    <input type="text" name="material" id="material" value="MDF"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring focus:ring-indigo-500">
                </div>
                <div>
                    <label for="color_catalog_id" class="block text-sm font-medium text-gray-700">Каталог цветов</label>
                    <select name="color_catalog_id" id="color_catalog_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:ring focus:ring-indigo-500">
                        @foreach($colorCatalogs as $catalog)
                            <option value="{{ $catalog->id }}" {{ $catalog->name_en == 'RAL' ? 'selected' : '' }}>
                                {{ $catalog->name_en }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="color_code_id" class="block text-sm font-medium text-gray-700">Код цвета</label>
                    <select name="color_code_id" id="color_code_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:ring focus:ring-indigo-500">
                        @foreach($colors as $color)
                            <option value="{{ $color->id }}" {{ $color->code == '9003' ? 'selected' : '' }}>
                                {{ $color->code }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="coating_type_id" class="block text-sm font-medium text-gray-700">Тип покрытия</label>
                    <select name="coating_type_id" id="coating_type_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:ring focus:ring-indigo-500">
                        @foreach($coatingTypes as $coating)
                            <option value="{{ $coating->id }}" {{ $coating->label == 'Матовый' ? 'selected' : '' }}>
                                {{ $coating->label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 🔹 Третий ряд: Фрезеровка и Файл вложения -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="milling_id" class="block text-sm font-medium text-gray-700">Фрезеровка</label>
                    <select name="milling_id" id="milling_id"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:ring focus:ring-indigo-500">
                        <option value="">—</option>
                        @foreach($millings as $milling)
                            <option value="{{ $milling->id }}">{{ $milling->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="attachment_path" class="block text-sm font-medium text-gray-700">Файл вложения</label>
                    <input type="file" name="attachment_path" id="attachment_path"
                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring focus:ring-indigo-500">
                </div>
            </div>
        </form>
    </div>
            <!-- 🔹 Позиции заказа -->
            <h4 class="text-lg font-semibold mb-2">Позиции заказа</h4>

            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300 rounded-lg" id="order-items-table">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="px-3 py-2 border text-left">Тип фасада</th>
                        <th class="px-3 py-2 border text-left">Высота</th>
                        <th class="px-3 py-2 border text-left">Ширина</th>
                        <th class="px-3 py-2 border text-left">Количество</th>
                        <th class="px-3 py-2 border text-left">Двухстор.покрытие</th>
                        <th class="px-3 py-2 border text-left">Толщина</th>
                        <th class="px-3 py-2 border text-left">Сверловка</th>
                        <th class="px-3 py-2 border text-left">Примечания</th>
                        <th class="px-3 py-2 border"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="border px-2 py-1">
                            <select name="items[0][facade_type_id]"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">—</option>
                                @foreach($facadeTypes as $type)
                                    <option value="{{ $type->id }}">{{ $type->name_ru }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border px-2 py-1">
                            <input type="number" name="items[0][height]"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                        <td class="border px-2 py-1">
                            <input type="number" name="items[0][width]"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                        <td class="border px-2 py-1">
                            <input type="number" name="items[0][quantity]"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                        <td class="border px-2 py-1">
                            <select name="items[0][double_sided_coating]"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">—</option>
                                <option value="1">Да</option>
                                <option value="0">Нет</option>
                            </select>
                        </td>
                        <td class="border px-2 py-1">
                            <select name="items[0][thickness]"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="19" selected>19</option>
                                <option value="16">16</option>
                                <option value="22">22</option>
                            </select>
                        </td>
                        <td class="border px-2 py-1">
                            <select name="items[0][drilling_id]"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">—</option>
                                @foreach($drillings as $drilling)
                                    <option value="{{ $drilling->id }}">{{ $drilling->name_ru }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td class="border px-2 py-1">
                            <input type="text" name="items[0][notes]"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </td>
                        <td class="border px-2 py-1 text-center">
                            <button type="button"
                                    class="inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 remove-row">
                                Удалить
                            </button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <button type="button" id="add-row"
                    class="mt-3 inline-flex items-center px-3 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                Добавить позицию
            </button>

            <hr class="my-6">

            <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Сохранить заказ
            </button>
        </form>
    </div>

    <script>
        let rowIndex = 1;

        // Добавление новой строки
        document.getElementById('add-row').addEventListener('click', function() {
            const tableBody = document.querySelector('#order-items-table tbody');
            const newRow = tableBody.rows[0].cloneNode(true);

            // Обновляем имена полей и очищаем значения
            Array.from(newRow.querySelectorAll('input, select')).forEach(el => {
                const name = el.getAttribute('name');
                if (name) {
                    el.setAttribute('name', name.replace(/\d+/, rowIndex));
                    // Для select оставляем пустую опцию, иначе сбрасываем
                    if (el.tagName.toLowerCase() === 'select') {
                        el.selectedIndex = 0;
                    } else {
                        el.value = '';
                    }
                }
            });

            tableBody.appendChild(newRow);
            rowIndex++;
        });

        // Удаление строки
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-row')) {
                const row = e.target.closest('tr');
                const rows = document.querySelectorAll('#order-items-table tbody tr');
                if (rows.length > 1) {
                    row.remove();
                }
            }
        });
    </script>
</x-app-layout>

