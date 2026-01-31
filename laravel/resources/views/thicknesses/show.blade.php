<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-lg">Толщина #{{ $thickness->id }}</h2>
    </x-slot>

    <div class="p-4 space-y-2">
        <p><strong>Значение (мм):</strong> {{ $thickness->value }}</p>
        <p><strong>Метка:</strong> {{ $thickness->label ?? '—' }}</p>
        <p><strong>Цена:</strong> {{ $thickness->price ? number_format($thickness->price, 2, ',', ' ') : '—' }}</p>

        <div class="mt-4">
            <a href="{{ route('thicknesses.edit', $thickness->id) }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                ✏️ Редактировать
            </a>
            <a href="{{ route('thicknesses.index') }}" class="ml-2 text-gray-600">Назад к списку</a>
        </div>
    </div>
</x-app-layout>
