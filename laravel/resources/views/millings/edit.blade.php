<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
    <h1>Редактирование фрезеровки: {{ $milling->name }}</h1>
    </x-slot>
    @if ($errors->any())
        <div style="color:red;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('millings.update', $milling) }}" method="POST">
        @csrf
        @method('PUT')

        <div>
            <label for="price_retail">Розничная цена</label>
            <input type="text" id="price_retail" name="price_retail"
                   value="{{ old('price_retail', $milling->price_retail) }}">
        </div>

        <div>
            <label for="price_dealer">Цена для дилеров</label>
            <input type="text" id="price_dealer" name="price_dealer"
                   value="{{ old('price_dealer', $milling->price_dealer) }}">
        </div>

        <div>
            <label for="price_private">Цена для физ. лиц</label>
            <input type="text" id="price_private" name="price_private"
                   value="{{ old('price_private', $milling->price_private) }}">
        </div>

        <button type="submit">Сохранить</button>
    </form>

</x-app-layout>
