<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-lg">Список типов покрытий</h2>
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
            <th class="border px-2 py-1">Системное имя</th>
            <th class="border px-2 py-1">Название</th>
            <th class="border px-2 py-1">Описание</th>
            <th class="border px-2 py-1">Цена</th>
            <th class="border px-2 py-1">Действия</th>
        </tr>
        </thead>
        <tbody>
        @foreach($coatingTypes as $type)
            <tr>
                <td class="border px-2 py-1">{{ $type->id }}</td>
                <td class="border px-2 py-1">{{ $type->name }}</td>
                <td class="border px-2 py-1">{{ $type->label }}</td>
                <td class="border px-2 py-1">{{ $type->description }}</td>
                <td class="border px-2 py-1">{{ $type->price }}</td>
                <td class="border px-2 py-1 whitespace-nowrap">
                    <a href="{{ route('coating-types.show', $type) }}" class="text-blue-600 mr-2">👁️</a>
                    <a href="{{ route('coating-types.edit', $type) }}" class="text-blue-600 mr-2">✏️</a>
                    <form action="{{ route('coating-types.destroy', $type) }}" method="POST" class="inline"
                          onsubmit="return confirm('Удалить этот тип покрытия?');">
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
        <a href="{{ route('coating-types.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            ➕ Добавить новый тип покрытия
        </a>
    </div>
</x-app-layout>
