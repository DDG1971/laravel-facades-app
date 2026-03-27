<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>
    <!--  header -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ __('Admin Panel') }}
        </h2>
    </x-slot>
    <!--  конец header -->

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">👥 Pending Users</h2>
                <p>Управление пользователями со статусом pending.</p>
                <a href="{{ url('/admin/pending-users') }}" class="text-blue-600">Перейти</a>
            </div>

            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">📦 Orders</h2>
                <p>Создание и управление заказами.</p>
                <a href="{{ route('admin.orders.create') }}" class="text-blue-600">➕ Новый заказ</a>
                <a href="{{ route('admin.orders.index') }}" class="text-blue-600">📋 Список заказов</a>
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

            <!--  блок статистики -->
            <div class="p-6 bg-white shadow rounded hover:bg-gray-50 transition">
                <div class="flex justify-between items-center">
                    <h2 class="font-semibold">📊 Statistics</h2>
                    <a href="{{ route('admin.statistics') }}" class="text-sm text-blue-600 hover:underline">Подробнее →</a>
                </div>
                <p class="mt-2">Общая квадратура заказов за месяц:</p>
                <p class="text-xl font-bold">{{ $totalSquare }} м²</p>
            </div>

            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">🪚 Millings</h2>
                <p>Редактирование списка фрезеровок и цен.</p>
                <a href="{{ route('millings.index') }}" class="text-blue-600">📋 Список фрезеровок</a>
            </div>

            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">🪟 Facade Types</h2>
                <p>Редактирование списка типов фасадов (витрины, решётки и др.).</p>
                <a href="{{ route('facade-types.index') }}" class="text-blue-600">📋 Список типов</a>
            </div>

            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">🎨 Color Catalogs</h2>
                <p>Редактирование списка  каталогов цвета (RAL,WCP и др.).</p>
                <a href="{{ route('color_catalogs.index') }}" class="text-blue-600">📋 Список каталогов</a>
            </div>

            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">🟥 🟩 🟦 Color Codes</h2>
                <p>Редактирование списка кодов цвета .</p>
                <a href="{{ route('color_codes.index') }}" class="text-blue-600">📋 Список №цветов</a>
            </div>

            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">🖌️ Coating Types</h2>
                <p>Редактирование списка покрытий  .</p>
                <a href="{{ route('coating-types.index') }}" class="text-blue-600">📋 Список покрытий</a>
            </div>

            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">📏 Thicknesses</h2>
                <p>Редактирование списка толщин.</p>
                <a href="{{ route('thicknesses.index') }}" class="text-blue-600">📋 Список толщин</a>
            </div>

            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">钻 Drilling</h2>
                <p>Редактирование типов сверловки, пазов под ДВП и натяжителей.</p>
                <a href="{{ route('drillings.index') }}" class="text-blue-600">📋 Список сверловок</a>
            </div>


        </div>
    </div>
</x-app-layout>
