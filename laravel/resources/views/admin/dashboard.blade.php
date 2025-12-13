<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>
    <!-- 🔹 Вот здесь добавляем header -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ __('Admin Panel') }}
        </h2>
    </x-slot>
    <!-- 🔹 конец header -->

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-6">Admin Panel</h1>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">👥 Pending Users</h2>
                <p>Управление пользователями со статусом pending.</p>
                <a href="{{ url('/admin/pending-users') }}" class="text-blue-600">Перейти</a>
            </div>

            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">📦 Orders</h2>
                <p>Создание и управление заказами.</p>
                <a href="{{ url('/orders/create') }}" class="text-blue-600">Перейти</a>
            </div>

            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">⚙️ Profile</h2>
                <p>Редактирование профиля и пароля.</p>
                <a href="{{ url('/profile/edit') }}" class="text-blue-600">Перейти</a>
            </div>

            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">🏢 Clients</h2>
                <p>Редактирование данных клиентов.</p>
                <a href="{{ url('/admin/clients') }}" class="text-blue-600">Перейти</a>
            </div>

            <!-- 🔹 Новый блок статистики -->
            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">📊 Statistics</h2>
                <p>Общая квадратура заказов за месяц:</p>
                <p class="text-xl font-bold">{{ $totalSquare }} м²</p>
            </div>
        </div>
    </div>
</x-app-layout>
