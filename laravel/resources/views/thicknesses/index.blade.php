<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-lg">Список толщин</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    <table class="table-auto border-collapse border border-gray-300 text-sm mx-auto">
        <thead>
        <tr class="bg-gray-100">
            <th class="border px-2 py-1">ID</th>
            <th class="border px-2 py-1">Значение (мм)</th>
            <th class="border px-2 py-1">Метка</th>
            <th class="border px-2 py-1">Цена</th>
            <th class="border px-2 py-1">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($thicknesses as $thickness)
            <tr>
                <td class="border px-2 py-1">{{ $thickness->id }}</td>
                <td class="border px-2 py-1">{{ $thickness->value }}</td>
                <td class="border px-2 py-1">{{ $thickness->label ?? '—' }}</td>
                <td class="border px-2 py-1">
                    {{ $thickness->price ? number_format($thickness->price, 2, ',', ' ') : '—' }}
                </td>
                <td class="border px-2 py-1 whitespace-nowrap">
                    <a href="{{ route('thicknesses.show', $thickness) }}" class="text-blue-600 mr-2">👁️</a>
                    <a href="{{ route('thicknesses.edit', $thickness) }}" class="text-blue-600 mr-2">✏️</a>
                    <form action="{{ route('thicknesses.destroy', $thickness) }}" method="POST" class="inline"
                          onsubmit="return confirm('Удалить эту толщину?');">
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
        <a href="{{ route('thicknesses.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            ➕ Добавить новую толщину
        </a>
    </div>
</x-app-layout>
