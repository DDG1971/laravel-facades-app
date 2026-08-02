<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold mb-6">Добавить новую фрезеровку</h1>
    </x-slot>

    @if ($errors->any())
        <div class="text-red-600 mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('millings.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf

        <div>
            <label for="name">Название</label>
            <input type="text" id="name" name="name"
                   value="{{ old('name') }}" class="border px-2 py-1 w-full">
        </div>

        <div>
            <label for="code">Код</label>
            <input type="text" id="code" name="code"
                   value="{{ old('code') }}" class="border px-2 py-1 w-full">
        </div>

        <div>
            <label for="name_en">Английское имя</label>
            <input type="text" id="name_en" name="name_en"
                   value="{{ old('name_en') }}" class="border px-2 py-1 w-full">
        </div>

        {{-- Новое поле: загрузка SVG --}}
        <div>
            <label for="svg_file" class="block text-sm font-medium text-gray-700">
                Чертёж фрезеровки (SVG или PNG)
            </label>
            <input type="file" id="svg_file" name="svg_file" accept=".svg,.png,.jpg,.jpeg"
                   class="border px-2 py-1 w-full">
            <p class="text-xs text-gray-500 mt-1">Рекомендуется SVG. Максимальный размер: 2 МБ.</p>
        </div>

        <div>
            <label for="price_retail">Розничная цена</label>
            <input type="text" id="price_retail" name="price_retail"
                   value="{{ old('price_retail') }}" class="border px-2 py-1 w-full">
        </div>

        <div>
            <label for="price_dealer">Цена для дилеров</label>
            <input type="text" id="price_dealer" name="price_dealer"
                   value="{{ old('price_dealer') }}" class="border px-2 py-1 w-full">
        </div>

        <div>
            <label for="price_private">Цена для физ. лиц</label>
            <input type="text" id="price_private" name="price_private"
                   value="{{ old('price_private') }}" class="border px-2 py-1 w-full">
        </div>

        <div>
            <label for="price_coloring">Цена сырых</label>
            <input type="text" id="price_coloring" name="price_coloring"
                   value="{{ old('price_coloring') }}" class="border px-2 py-1 w-full">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            ➕ Добавить
        </button>
    </form>

</x-app-layout>
