<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg">Список типов фасадов</h2>
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
                <th class="border px-2 py-1">Название (EN)</th>
                <th class="border px-2 py-1">Название (RU)</th>
                <th class="border px-2 py-1">Режим цены</th>
                <th class="border px-2 py-1">Значение</th>
                <th class="border px-2 py-1">Ед. изм.</th>
                <th class="border px-2 py-1">Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($facadeTypes as $facadeType)
                <tr>
                    <td class="border px-2 py-1">{{ $facadeType->name_en }}</td>
                    <td class="border px-2 py-1">{{ $facadeType->name_ru }}</td>
                    <td class="border px-2 py-1">{{ $facadeType->pricing_mode }}</td>
                    <td class="border px-2 py-1">{{ $facadeType->pricing_value }}</td>
                    <td class="border px-2 py-1">{{ $facadeType->unit_mode }}</td>
                    <td class="border px-2 py-1 whitespace-nowrap">
                        <a href="{{ route('facade-types.show', $facadeType) }}" class="text-blue-600 mr-2">👁️</a>
                        <a href="{{ route('facade-types.edit', $facadeType) }}" class="text-blue-600 mr-2">✏️</a>
                        <form action="{{ route('facade-types.destroy', $facadeType) }}" method="POST" class="inline"
                              onsubmit="return confirm('Удалить этот фасад?');">
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
            <a href="{{ route('facade-types.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                ➕ Добавить новый фасад
            </a>
        </div>
    </div>
</x-app-layout>
