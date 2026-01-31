<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-lg">Список кодов цветов</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    <table class="table-auto border-collapse border border-gray-300 text-sm mx-auto">
        <thead>
        <tr class="bg-gray-100">
            <th class="border px-2 py-1">Код</th>
            <th class="border px-2 py-1">Каталог</th>
            <th class="border px-2 py-1">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($codes as $code)
            <tr>
                <td class="border px-2 py-1">{{ $code->code }}</td>
                <td class="border px-2 py-1">{{ $code->colorCatalog->name_en }}</td>
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
        @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        <a href="{{ route('color_codes.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            ➕ Добавить новый код
        </a>
    </div>
</x-app-layout>
