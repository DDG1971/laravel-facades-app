<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold mb-6">Код цвета: {{ $colorCode->code }}</h1>
    </x-slot>

    <div class="p-6 bg-white shadow rounded">
        <p><strong>Код:</strong> {{ $colorCode->code }}</p>
        <p><strong>Каталог:</strong> {{ $colorCode->colorCatalog->name_en }}</p>
    </div>

    <div class="mt-4 flex gap-4">
        <a href="{{ route('color_codes.edit', $colorCode) }}" class="text-blue-600">✏️ Редактировать</a>
        <a href="{{ route('color_codes.index') }}" class="text-blue-600">⬅️ Назад к списку</a>

        <form action="{{ route('color_codes.destroy', $colorCode) }}" method="POST"
              onsubmit="return confirm('Удалить этот код?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600">🗑️ Удалить</button>
        </form>
    </div>
</x-app-layout>
