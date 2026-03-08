<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            Редактирование фрезеровки: {{ $milling->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('millings.update', $milling) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Основная информация -->
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Название (RU)</label>
                            <input type="text" name="name" value="{{ old('name', $milling->name) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Название (EN/System)</label>
                            <input type="text" name="name_en" value="{{ old('name_en', $milling->name_en) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Код артикула</label>
                            <input type="text" name="code" value="{{ old('code', $milling->code) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Цены -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Цена (Дилер)</label>
                            <input type="text" name="price_dealer" value="{{ old('price_dealer', $milling->price_dealer) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Цена (Розница)</label>
                            <input type="text" name="price_retail" value="{{ old('price_retail', $milling->price_retail) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Цена (Физ. лица)</label>
                            <input type="text" name="price_private" value="{{ old('price_private', $milling->price_private) }}"
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-between border-t pt-4">
                        <a href="{{ route('millings.index') }}" class="text-sm text-gray-600 hover:underline">
                            ← Отмена
                        </a>
                        <button type="submit"
                                class="px-6 py-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 transition duration-150">
                            💾 Сохранить изменения
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

