<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-lg">Просмотр типа покрытия</h2>
    </x-slot>

    <div class="max-w-md mx-auto border rounded p-4 bg-gray-50">
        <p><strong>ID:</strong> {{ $coatingType->id }}</p>
        <p><strong>Системное имя:</strong> {{ $coatingType->name }}</p>
        <p><strong>Название:</strong> {{ $coatingType->label }}</p>
        <p><strong>Описание:</strong> {{ $coatingType->description }}</p>
        <p><strong>Цена:</strong> {{ $coatingType->price }}</p>
    </div>

    <div class="mt-4 flex space-x-2">
        <a href="{{ route('coating-types.edit', $coatingType) }}"
           class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
            ✏️ Редактировать
        </a>
        <a href="{{ route('coating-types.index') }}"
           class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
            ⬅️ Назад к списку
        </a>
    </div>
</x-app-layout>
