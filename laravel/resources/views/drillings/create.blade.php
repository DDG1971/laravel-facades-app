<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold">Добавить новый тип сверления</h1>
    </x-slot>

    <div class="max-w-4xl mx-auto py-6 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Исправили route на drillings.store --}}
        <form action="{{ route('drillings.store') }}" method="POST" class="bg-white shadow-md rounded px-8 pt-6 pb-8 space-y-4">
            @csrf

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name_ru">
                    Название (RU)
                </label>
                <input type="text" id="name_ru" name="name_ru" required
                       value="{{ old('name_ru') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="name_en">
                    Название (EN) - для системы
                </label>
                <input type="text" id="name_en" name="name_en" required
                       value="{{ old('name_en') }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="price">
                    Цена (за ед.)
                </label>
                <input type="number" step="0.01" id="price" name="price"
                       value="{{ old('price', 0) }}"
                       class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            </div>

            <div class="flex items-center gap-4 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    ➕ Сохранить
                </button>
                <a href="{{ route('drillings.index') }}" class="text-gray-600 hover:underline text-sm">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</x-app-layout>
