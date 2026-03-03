<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-6">Тип сверления: {{ $drilling->name_ru }}</h1>

        <div class="p-6 bg-white shadow rounded max-w-lg">
            <p class="mb-2"><strong>Название (RU):</strong> {{ $drilling->name_ru }}</p>
            <p class="mb-2"><strong>Системное имя (EN):</strong> {{ $drilling->name_en ?? '—' }}</p>
            <p class="mb-2"><strong>Цена за ед.:</strong> {{ number_format($drilling->price ?? 0, 2) }}$</p>
        </div>

        <div class="mt-4 flex gap-4 items-center">
            <a href="{{ route('drillings.edit', $drilling) }}" class="text-blue-600">✏️ Редактировать</a>
            <a href="{{ route('drillings.index') }}" class="text-blue-600">⬅️ Назад к списку</a>

            <!-- 🔴 Кнопка удаления -->
            <form action="{{ route('drillings.destroy', $drilling) }}" method="POST"
                  onsubmit="return confirm('Удалить этот тип сверления?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600">🗑️ Удалить</button>
            </form>
        </div>
    </div>
</x-app-layout>

