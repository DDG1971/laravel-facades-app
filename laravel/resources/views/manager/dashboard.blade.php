<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manager Dashboard') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <!-- Блок Заказы -->
            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold text-lg mb-2">📦 Orders</h2>
                <p class="text-sm text-gray-600 mb-4">Создание и управление заказами.</p>
                <div class="flex flex-col gap-2">
                    <!-- Ссылка на создание -->
                    <a href="{{ route('admin.orders.create') }}" class="text-blue-600 hover:underline font-medium">➕ Новый заказ</a>
                    <!-- Ссылка на список (уже должна быть) -->
                    <a href="{{ route('manager.orders.index') }}" class="text-blue-600 hover:underline">📋 Список заказов</a>
                </div>
            </div>

            <!-- Блок Статистика -->
            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold text-lg mb-2">📊 Statistics</h2>
                <p class="text-sm text-gray-600">Общая квадратура за месяц:</p>
                <p class="text-2xl font-bold text-indigo-600">{{ $totalSquare ?? 0 }} м²</p>
            </div>

            <!-- Блок Профиль -->
            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold text-lg mb-2">⚙️ Profile</h2>
                <p class="text-sm text-gray-600 mb-4">Настройки вашего аккаунта.</p>
                <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:underline">Перейти в настройки</a>
            </div>

        </div>
    </div>
</x-app-layout>
