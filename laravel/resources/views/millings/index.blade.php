<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg">Список фрезеровок</h2>
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
                <th class="border px-2 py-1">Код</th>
                <th class="border px-2 py-1">Название</th>
              {{--  <th class="border px-2 py-1">Англ. имя</th>--}}
                <th class="border px-2 py-1">Дилерская</th>
                <th class="border px-2 py-1">Розничная</th>
                <th class="border px-2 py-1">Физ. лица</th>
                <th class="border px-2 py-1">Действия</th>
            </tr>
            </thead>
            <tbody>
            @foreach($millings as $milling)
                <tr>
                    <td class="border px-2 py-1">{{ $milling->code }}</td>
                    <td class="border px-2 py-1">{{ $milling->name }}</td>
                   {{-- <td class="border px-2 py-1">{{ $milling->name_en }}</td>--}}
                    <td class="border px-2 py-1">{{ $milling->price_dealer }}</td>
                    <td class="border px-2 py-1">{{ $milling->price_retail }}</td>
                    <td class="border px-2 py-1">{{ $milling->price_private }}</td>
                    <td class="border px-2 py-1 whitespace-nowrap">
                        <a href="{{ route('millings.show', $milling) }}" class="text-blue-600 mr-2">👁️</a>
                        <a href="{{ route('millings.edit', $milling) }}" class="text-blue-600 mr-2">✏️</a>
                        <form action="{{ route('millings.destroy', $milling) }}" method="POST" class="inline"
                              onsubmit="return confirm('Удалить эту фрезеровку?');">
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
            <a href="{{ route('millings.create') }}"
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                ➕ Добавить новую фрезеровку
            </a>
        </div>
    </div>
</x-app-layout>

