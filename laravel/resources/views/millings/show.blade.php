<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <div>
        <h1 class="text-2xl font-bold mb-6">Фрезеровка: {{ $milling->name }}</h1>

        <div class="p-6 bg-white shadow rounded">
            <p><strong>Код:</strong> {{ $milling->code }}</p>
            <p><strong>Английское имя:</strong> {{ $milling->name_en ?? '—' }}</p>
            <p><strong>Розничная цена:</strong> {{ $milling->price_retail ?? '—' }}</p>
            <p><strong>Цена для дилеров:</strong> {{ $milling->price_dealer ?? '—' }}</p>
            <p><strong>Цена для физ. лиц:</strong> {{ $milling->price_private ?? '—' }}</p>
            <p><strong>Цена для физ. лиц:</strong> {{ $milling->price_coloring ?? '—' }}</p>
        </div>

        <div class="mt-4 flex gap-4">
            <a href="{{ route('millings.edit', $milling) }}" class="text-blue-600">✏️ Редактировать</a>
            <a href="{{ route('millings.index') }}" class="text-blue-600">⬅️ Назад к списку</a>

            <!-- 🔴 Кнопка удаления -->
            <form action="{{ route('millings.destroy', $milling) }}" method="POST" onsubmit="return confirm('Удалить эту фрезеровку?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600">🗑️ Удалить</button>
            </form>
        </div>

    </div>
</x-app-layout>
