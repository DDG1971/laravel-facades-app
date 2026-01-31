<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg">Редактировать фасад</h2>
    </x-slot>

    <div class="p-4">
        <form action="{{ route('facade-types.update', $facadeType) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block">Название (EN)</label>
                <input type="text" name="name_en" value="{{ $facadeType->name_en }}" class="border rounded w-full px-2 py-1" required>
            </div>

            <div>
                <label class="block">Название (RU)</label>
                <input type="text" name="name_ru" value="{{ $facadeType->name_ru }}" class="border rounded w-full px-2 py-1" required>
            </div>

            <div>
                <label class="block">Режим цены</label>
                <select name="pricing_mode" class="border rounded w-full px-2 py-1">
                    <option value="inherit" @selected($facadeType->pricing_mode === 'inherit')>inherit</option>
                    <option value="set_base" @selected($facadeType->pricing_mode === 'set_base')>set_base</option>
                    <option value="percent_add" @selected($facadeType->pricing_mode === 'percent_add')>percent_add</option>
                    <option value="none" @selected($facadeType->pricing_mode === 'none')>none</option>
                </select>
            </div>

            <div>
                <label class="block">Значение (₽ или %)</label>
                <input type="number" step="0.01" name="pricing_value" value="{{ $facadeType->pricing_value }}" class="border rounded w-full px-2 py-1">
            </div>

            <div>
                <label class="block">Единица измерения</label>
                <select name="unit_mode" class="border rounded w-full px-2 py-1">
                    <option value="inherit" @selected($facadeType->unit_mode === 'inherit')>inherit</option>
                    <option value="piece" @selected($facadeType->unit_mode === 'piece')>штука</option>
                    <option value="m2" @selected($facadeType->unit_mode === 'm2')>м²</option>
                    <option value="lm" @selected($facadeType->unit_mode === 'lm')>пог. метр</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                💾 Обновить
            </button>
        </form>
    </div>
</x-app-layout>
