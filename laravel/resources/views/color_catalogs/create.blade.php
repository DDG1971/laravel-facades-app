<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold mb-6">Добавить новый каталог</h1>
    </x-slot>

    @if ($errors->any())
        <div class="text-red-600 mb-4">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('color_catalogs.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label for="name_en">Название (англ.)</label>
            <input type="text" id="name_en" name="name_en"
                   value="{{ old('name_en') }}" class="border px-2 py-1 w-full">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            ➕ Добавить
        </button>
    </form>
</x-app-layout>
