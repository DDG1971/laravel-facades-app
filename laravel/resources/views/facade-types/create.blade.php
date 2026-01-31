<x-app-layout>
    <x-slot name="head">
        <x-assets/>
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg">Добавить новый фасад</h2>
    </x-slot>

    <div class="p-4">
        <form action="{{ route('facade-types.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block">Название (EN)</label>
                <input type="text" name="name_en" class="border rounded w-full px-2 py-1" required>
            </div>

            <div>
                <label class="block">Название (RU)</label>
                <input type="text" name="name_ru" class="border rounded w-full px-2 py-1" required>
            </div>

            <div>
                <label class="block">Режим цены</label>
                <select name="pricing_mode" class="border rounded w-full px-2 py-1">
                    <option value="inherit">inherit (как Milling)</option>
                    <option value="set_base">set_base (новая база)</option>
                    <option value="percent_add">percent_add (% к базе)</option>
                    <option value="none">none (без изменений)</option>
                </select>
            </div>

            <div>
                <label class="block">Значение (₽ или %)</label>
                <input type="number" step="0.01" name="pricing_value" class="border rounded w-full px-2 py-1">
            </div>

            <div>
                <label class="block">Единица измерения</label>
                <select name="unit_mode" class="border rounded w-full px-2 py-1">
                    <option value="inherit">inherit</option>
                    <option value="piece">штука</option>
                    <option value="m2">м²</option>
                    <option value="lm">пог. метр</option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                💾 Сохранить
            </button>
        </form>
    </div>
</x-app-layout>
