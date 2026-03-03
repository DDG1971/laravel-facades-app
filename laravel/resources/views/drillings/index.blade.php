<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg">Список типов сверления</h2>
    </x-slot>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-3">
            {{ session('success') }}
        </div>
    @endif

    <div class="p-4">
        <table class="table-auto border-collapse border border-gray-300 w-auto mx-auto text-sm">
            <thead>
            <tr class="bg-gray-100">
                <th class="border px-4 py-1">Название (RU)</th>
                <th class="border px-4 py-1">Системное имя (EN)</th>
                <th class="border px-4 py-1">Цена ($)</th>
                <th class="border px-4 py-1">Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($drillings as $drilling)
                <tr>
                    <td class="border px-4 py-1">{{ $drilling->name_ru }}</td>
                    <td class="border px-4 py-1">{{ $drilling->name_en }}</td>
                    <td class="border px-4 py-1 text-center">{{ number_format($drilling->price ?? 0, 2) }}</td>
                    <td class="border px-4 py-1 whitespace-nowrap">
                        <a href="{{ route('drillings.edit', $drilling) }}" class="text-blue-600 mr-2">✏️</a>
                        <form action="{{ route('drillings.destroy', $drilling) }}" method="POST" class="inline"
                              onsubmit="return confirm('Удалить этот тип сверления?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">🗑️</button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <div class="mt-4 text-center">
            <a href="{{ route('drillings.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                ➕ Добавить новое сверление
            </a>
        </div>
    </div>
</x-app-layout>


