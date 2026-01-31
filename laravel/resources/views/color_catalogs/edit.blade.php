<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>
    <x-slot name="header">
        <h1 class="text-2xl font-bold mb-6">Редактировать каталог: {{ $colorCatalog->name_en }}</h1>
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

    <form action="{{ route('color_catalogs.update', $colorCatalog) }}" method="POST" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="name_en">Название (англ.)</label>
            <input type="text" id="name_en" name="name_en"
                   value="{{ old('name_en', $colorCatalog->name_en) }}" class="border px-2 py-1 w-full">
        </div>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
            💾 Сохранить
        </button>
    </form>
</x-app-layout>
