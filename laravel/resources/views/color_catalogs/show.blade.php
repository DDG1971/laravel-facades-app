<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold mb-6">Каталог: {{ $colorCatalog->name_en }}</h1>
    </x-slot>

    <div class="p-6 bg-white shadow rounded mb-6">
        <p><strong>Название (англ.):</strong> {{ $colorCatalog->name_en }}</p>
        <p><strong>Количество кодов:</strong> {{ $colorCatalog->colorCodes->count() }}</p>
    </div>

    <!-- Список кодов этого каталога -->
    <h2 class="text-xl font-semibold mb-4">Коды в этом каталоге</h2>
    <table class="table-auto border-collapse border border-gray-300 w-full text-sm mb-4">
        <thead>
        <tr class="bg-gray-100">
            <th class="border px-2 py-1">Код</th>
            <th class="border px-2 py-1">Действия</th>
        </tr>
        </thead>
        <tbody>
        @forelse($colorCatalog->colorCodes as $code)
            <tr>
                <td class="border px-2 py-1">{{ $code->code }}</td>
                <td class="border px-2 py-1 whitespace-nowrap">
                    <a href="{{ route('color_codes.show', $code) }}" class="text-blue-600 mr-2">👁️</a>
                    <a href="{{ route('color_codes.edit', $code) }}" class="text-blue-600 mr-2">✏️</a>
                    <form action="{{ route('color_codes.destroy', $code) }}" method="POST" class="inline"
                          onsubmit="return confirm('Удалить этот код?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600">🗑️</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="2" class="border px-2 py-1 text-center text-gray-500">Нет кодов</td>
            </tr>
        @endforelse
        </tbody>
    </table>

    <!-- Кнопка добавления нового кода прямо из каталога -->
    <div class="mt-4">
        <a href="{{ route('color_codes.create', ['catalog_id' => $colorCatalog->id]) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            ➕ Добавить код в этот каталог
        </a>
    </div>

    <!-- Навигация -->
    <div class="mt-6 flex gap-4">
        <a href="{{ route('color_catalogs.edit', $colorCatalog) }}" class="text-blue-600">✏️ Редактировать каталог</a>
        <a href="{{ route('color_catalogs.index') }}" class="text-blue-600">⬅️ Назад к списку</a>

        <form action="{{ route('color_catalogs.destroy', $colorCatalog) }}" method="POST"
              onsubmit="return confirm('Удалить этот каталог?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-600">🗑️ Удалить</button>
        </form>
    </div>
</x-app-layout>

