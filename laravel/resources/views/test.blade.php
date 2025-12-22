<x-app-layout>
    <x-slot name="head">
        <x-assets />
        <script src="https://cdn.tailwindcss.com"></script>
    </x-slot>

    <div class="p-4">
        <div class="overflow-x-auto">
            <table class="min-w-full table-fixed border border-gray-300 border-collapse">
                <colgroup>
                    <col style="width:120px">   <!-- Тип фасада -->
                    <col style="width:60px">   <!-- Высота -->
                    <col style="width:60px">   <!-- Ширина -->
                    <col style="width:120px">  <!-- Примечания -->
                    <col style="width:40px">   <!-- Файл -->
                    <col style="width:40px">   <!-- + -->
                    <col style="width:40px">   <!-- − -->
                </colgroup>

                <thead class="bg-gray-100 text-sm">
                <tr>
                    <th class="border px-1 py-0.5">Тип фасада</th>
                    <th class="border px-1 py-0.5">Высота</th>
                    <th class="border px-1 py-0.5">Ширина</th>
                    <th class="border px-1 py-0.5">Примечания</th>
                    <th class="border px-1 py-0.5">Файл</th>
                    <th class="border px-1 py-0.5">+</th>
                    <th class="border px-1 py-0.5">−</th>
                </tr>
                </thead>

                <tbody>
                <tr class="text-sm">
                    <td class="border px-1 py-0.5">
                        <select class="w-full min-w-0 border border-gray-400 px-1 py-0 text-sm truncate">
                            <option>Очень длинное название фасада</option>
                            <option>Короткое</option>
                        </select>
                    </td>
                    <td class="border px-1 py-0.5">
                        <input type="number" class="w-full min-w-0 border border-gray-400 px-1 py-0 text-center text-sm">
                    </td>
                    <td class="border px-1 py-0.5">
                        <input type="number" class="w-full min-w-0 border border-gray-400 px-1 py-0 text-center text-sm">
                    </td>
                    <td class="border px-1 py-0.5">
                        <input type="text" class="w-full min-w-0 border border-gray-400 px-1 py-0 text-sm">
                    </td>
                    <td class="border px-1 py-0.5 text-center">
                        <label class="w-5 h-5 flex items-center justify-center bg-blue-200 text-black cursor-pointer text-xs">
                            📎
                            <input type="file" class="hidden">
                        </label>
                    </td>
                    <td class="border px-1 py-0.5 text-center">
                        <button class="w-5 h-5 bg-green-200 text-black text-xs">+</button>
                    </td>
                    <td class="border px-1 py-0.5 text-center">
                        <button class="w-5 h-5 bg-red-600 text-white text-xs">−</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

