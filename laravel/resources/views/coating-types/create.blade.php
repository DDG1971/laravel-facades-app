<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-lg">Добавить новый тип покрытия</h2>
    </x-slot>

    <form method="POST" action="{{ route('coating-types.store') }}" class="max-w-md mx-auto">
        @csrf

        <div class="mb-3">
            <label class="block">Системное имя</label>
            <input type="text" name="name" value="{{ old('name') }}" class="border px-2 py-1 w-full">
        </div>

        <div class="mb-3">
            <label class="block">Название</label>
            <input type="text" name="label" value="{{ old('label') }}" class="border px-2 py-1 w-full">
        </div>

        <div class="mb-3">
            <label class="block">Описание</label>
            <textarea name="description" class="border px-2 py-1 w-full">{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="block">Цена</label>
            <input type="number" step="0.01" name="price" value="{{ old('price') }}" class="border px-2 py-1 w-full">
        </div>

        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            💾 Сохранить
        </button>
    </form>
</x-app-layout>
