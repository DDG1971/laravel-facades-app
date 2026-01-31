<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg">Список каталогов цветов</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    <table class="table-auto border-collapse border border-gray-300 w-full text-sm">
        <thead>
        <tr class="bg-gray-100">
            <th class="border px-2 py-1">Название (англ.)</th>
            <th class="border px-2 py-1">Количество кодов</th>
            <th class="border px-2 py-1">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($catalogs as $catalog)
            <tr>
                <td class="border px-2 py-1">{{ $catalog->name_en }}</td>
                <td class="border px-2 py-1">{{ $catalog->color_codes_count }}</td>
                <td class="border px-2 py-1 whitespace-nowrap">
                    <a href="{{ route('color_catalogs.show', $catalog) }}" class="text-blue-600 mr-2">👁️</a>
                    <a href="{{ route('color_catalogs.edit', $catalog) }}" class="text-blue-600 mr-2">✏️</a>
                    <form action="{{ route('color_catalogs.destroy', $catalog) }}" method="POST" class="inline"
                          onsubmit="return confirm('Удалить этот каталог?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600">🗑️</button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        <a href="{{ route('color_catalogs.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            ➕ Добавить новый каталог
        </a>
    </div>
</x-app-layout>
