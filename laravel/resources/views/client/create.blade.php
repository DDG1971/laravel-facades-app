<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg">Создание заказа</h2>
    </x-slot>

    <!-- 🔹 Форма -->
    <form method="POST" action="{{ route('orders.store') }}" enctype="multipart/form-data">
        @csrf

        <!-- 🔹 Шапка заказа -->
        <div class="px-4 mt-6">
            <div class="grid grid-cols-4 divide-x divide-y border border-gray-400 w-fit text-sm">
                <!-- Дата -->
                <div class="p-2">
                    <label class="block text-xs font-medium text-gray-700">Дата создания</label>
                    <input type="date" class="w-48 border rounded-md px-2 py-1 text-sm bg-gray-100"
                           value="{{ now()->toDateString() }}" readonly tabindex="-1">
                </div>

                <!-- Компания (только своя) -->
                <div class="p-2">
                    <label class="block text-xs font-medium text-gray-700">Компания</label>
                    <input type="text" class="w-64 border rounded-md px-2 py-1 text-sm bg-gray-100"
                           value="{{ $customer->company_name }}" readonly>
                </div>

                <!-- № заказа клиента -->
                <div class="p-2">
                    <label for="client_order_number" class="block text-xs font-medium text-gray-700">№ заказа клиента</label>
                    <input type="text" id="client_order_number" name="client_order_number"
                           class="w-64 border rounded-md px-2 py-1 text-sm"
                           value="{{ old('client_order_number', 'б/н') }}">
                </div>
                <!-- Материал -->
                <div class="p-2">
                    <label for="material" class="block text-xs font-medium text-gray-700">Материал</label>
                    <select id="material" name="material"
                            class="w-48 border rounded-md px-2 py-1 bg-white text-sm">
                        <option value="MDF">MDF</option>
                        <option value="Shpon">Шпон</option>
                    </select>
                </div>
                <!-- Файл вложения -->
                <div class="p-2 no-print">
                    <label for="order_attachment" class="block text-xs font-medium text-gray-700">Файл вложения</label>
                    <input type="file" name="order_attachment" id="order_attachment" class="hidden"
                           onchange="document.getElementById('file-name').textContent = this.files[0]?.name || 'Файл не выбран';"/>
                    <button type="button" onclick="document.getElementById('order_attachment').click()"
                            class="px-2 py-1 border rounded-md bg-gray-100 hover:bg-gray-200 text-sm">
                        Выберите файл
                    </button>
                    <span id="file-name" class="ml-2 text-xs text-gray-500">Файл не выбран</span>
                </div>

                <!-- Каталог цветов -->
                <div class="p-2">
                    <label for="color_catalog_id" class="block text-xs font-medium text-gray-700">Каталог цветов</label>
                    <select id="color_catalog_id" name="color_catalog_id"
                            class="w-48 border rounded-md px-2 py-1 bg-white text-sm">
                        @foreach($colorCatalogs as $catalog)
                            <option value="{{ $catalog->id }}">{{ $catalog->name_en }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Код цвета -->
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

                <!-- Тип покрытия -->
                <div class="p-2">
                    <label for="coating_type_id" class="block text-xs font-medium text-gray-700">Тип покрытия</label>
                    <select id="coating_type_id" name="coating_type_id"
                            class="w-64 border rounded-md px-2 py-1 bg-white text-sm">
                        @foreach($coatingTypes as $coating)
                            <option value="{{ $coating->id }}">{{ $coating->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div></div>
                <div></div>
                <div></div>

                <!-- Фрезеровка -->
                <div class="p-2">
                    <label for="milling_id" class="block text-xs font-medium text-gray-700">Фрезеровка</label>
                    <div class="flex items-center gap-2">
                        <select id="milling_id" name="milling_id"
                                class="w-64 border rounded-md px-2 py-1 bg-white text-sm"
                                onchange="toggleMillingPreview()">
                            <option value="">—</option>
                            @foreach($millings as $milling)
                                <option value="{{ $milling->id }}"
                                        data-svg="{{ $milling->svg_path ? asset('storage/' . $milling->svg_path) : '' }}">
                                    {{ $milling->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" id="milling-preview-btn"
                                class="text-gray-400 hover:text-blue-600 text-lg cursor-pointer hidden"
                                title="Посмотреть чертёж"
                                onclick="showMillingPreview()">
                            👁️
                        </button>
                        <button type="button"
                                class="text-sm text-blue-600 hover:underline"
                                onclick="showMillingCatalog()">
                            📋 Каталог
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🔹 Позиции заказа -->
        @include('orders.partials.items') <!--  блок с таблицей позиций -->

        <!-- 🔹 Футер -->
        <div class="fixed bottom-0 left-0 right-0 bg-gray-100 border-t border-gray-300 px-6 py-2 flex justify-between items-center">
            <div class="flex space-x-8 text-sm font-semibold">
                <div>Итого фасадов: <span id="total-quantity">0</span></div>
                <div>Общая площадь: <span id="total-square">0</span> м²</div>
            </div>

            <div class="flex space-x-4">
                <button type="button" onclick="window.print()"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Предварительный просмотр
                </button>

                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                    Отправить
                </button>
                {{-- Кнопку "Расчёт" убрали --}}
            </div>

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
    {{-- Модальное окно для чертежа выбранной фрезеровки --}}
    <div id="milling-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-4 max-w-lg max-h-[80vh] overflow-auto relative">
            <button onclick="closeMillingModal(event)" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">✕</button>
            <h3 class="text-lg font-semibold mb-3" id="milling-modal-title">Чертёж фрезеровки</h3>
            <img id="milling-modal-image" src="" alt="Чертёж" class="max-w-full">
            <p id="milling-modal-empty" class="text-gray-400 hidden">Для этой фрезеровки чертёж не загружен.</p>
        </div>
    </div>

    {{-- Каталог фрезеровок --}}
    <div id="milling-catalog-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-4 max-w-4xl max-h-[80vh] overflow-auto relative">
            <button onclick="closeMillingCatalog(event)" class="absolute top-2 right-2 text-gray-500 hover:text-black text-xl">✕</button>
            <h3 class="text-lg font-semibold mb-4">Каталог фрезеровок</h3>
            <div class="space-y-4">
                @foreach($millings as $milling)
                    <div class="border rounded p-3 flex items-start gap-4">
                        <div class="flex-1">
                            <h4 class="font-semibold">{{ $milling->name }}</h4>
                            @if($milling->svg_path)
                                <img src="{{ asset('storage/' . $milling->svg_path) }}"
                                     alt="{{ $milling->name }}"
                                     class="max-w-[400px] mt-2 border rounded">
                            @else
                                <p class="text-gray-400 text-sm mt-2">Чертёж не загружен</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
