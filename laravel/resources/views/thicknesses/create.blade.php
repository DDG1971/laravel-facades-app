<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-lg">Добавить толщину</h2>
    </x-slot>

    <div class="p-4">
        <form action="{{ route('thicknesses.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label for="value" class="block text-sm font-medium">Значение (мм)</label>
                <input type="number" name="value" id="value" class="border rounded w-full px-2 py-1"
                       value="{{ old('value') }}" required>
                @error('value') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="label" class="block text-sm font-medium">Метка</label>
                <input type="text" name="label" id="label" class="border rounded w-full px-2 py-1"
                       value="{{ old('label') }}">
                @error('label') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium">Цена</label>
                <input type="number" step="0.01" name="price" id="price" class="border rounded w-full px-2 py-1"
                       value="{{ old('price') }}">
                @error('price') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    💾 Сохранить
                </button>
                <a href="{{ route('thicknesses.index') }}" class="ml-2 text-gray-600">Отмена</a>
            </div>
        </form>
    </div>
</x-app-layout>
