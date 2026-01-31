<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold mb-6">Добавить новый код цвета</h1>
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

    <form action="{{ route('color_codes.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="code">Код</label>
            <input type="text" id="code" name="code"
                   value="{{ old('code') }}" class="border px-2 py-1 w-full">
        </div>

        <div>
            <label for="color_catalog_id">Каталог</label>
            <select id="color_catalog_id" name="color_catalog_id" class="border px-2 py-1 w-full">
                @foreach($catalogs as $catalog)
                    <option value="{{ $catalog->id }}"
                        {{ old('color_catalog_id') == $catalog->id ? 'selected' : '' }}>
                        {{ $catalog->name_en }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            ➕ Добавить
        </button>
    </form>
</x-app-layout>

