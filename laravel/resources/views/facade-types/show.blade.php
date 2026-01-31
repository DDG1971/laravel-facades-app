<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <div>
        <h1 class="text-2xl font-bold mb-6">Фасад: {{ $facadeType->name_ru }} ({{ $facadeType->name_en }})</h1>

        <div class="p-6 bg-white shadow rounded">
            <p><strong>Название (EN):</strong> {{ $facadeType->name_en }}</p>
            <p><strong>Название (RU):</strong> {{ $facadeType->name_ru }}</p>
            <p><strong>Режим цены:</strong> {{ $facadeType->pricing_mode }}</p>
            <p><strong>Значение:</strong> {{ $facadeType->pricing_value ?? '—' }}</p>
            <p><strong>Единица измерения:</strong> {{ $facadeType->unit_mode }}</p>
        </div>

        <div class="mt-4 flex gap-4">
            <a href="{{ route('facade-types.edit', $facadeType) }}" class="text-blue-600">✏️ Редактировать</a>
            <a href="{{ route('facade-types.index') }}" class="text-blue-600">⬅️ Назад к списку</a>

            <!-- 🔴 Кнопка удаления -->
            <form action="{{ route('facade-types.destroy', $facadeType) }}" method="POST"
                  onsubmit="return confirm('Удалить этот фасад?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600">🗑️ Удалить</button>
            </form>
        </div>
    </div>
</x-app-layout>
