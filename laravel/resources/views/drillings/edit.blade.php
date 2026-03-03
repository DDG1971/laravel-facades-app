<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-lg font-semibold">Редактирование сверления: {{ $drilling->name_ru }}</h1>
    </x-slot>

    <div class="p-4">
        @if ($errors->any())
            <div class="text-red-600 mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('drillings.update', $drilling) }}" method="POST" class="space-y-4 max-w-md">
            @csrf
            @method('PUT')

            <div>
                <label class="block font-medium" for="name_ru">Название (RU)</label>
                <input type="text" id="name_ru" name="name_ru"
                       value="{{ old('name_ru', $drilling->name_ru) }}"
                       class="border px-2 py-1 w-full rounded">
            </div>

            <div>
                <label class="block font-medium" for="name_en">Системное имя (EN)</label>
                <input type="text" id="name_en" name="name_en"
                       value="{{ old('name_en', $drilling->name_en) }}"
                       class="border px-2 py-1 w-full rounded bg-gray-50">
            </div>

            <div>
                <label class="block font-medium" for="price">Цена ($)</label>
                <input type="text" id="price" name="price"
                       value="{{ old('price', $drilling->price) }}"
                       class="border px-2 py-1 w-full rounded">
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Сохранить изменения
                </button>
                <a href="{{ route('drillings.index') }}" class="text-gray-500 underline">Отмена</a>
            </div>
        </form>
    </div>
</x-app-layout>

