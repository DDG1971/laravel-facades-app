<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <!-- header -->
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
            {{ __('Клиентский кабинет') }}
        </h2>
    </x-slot>
    <!-- конец header -->

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            <!-- Заказы -->
            <div class="p-6 bg-white shadow rounded mb-6">
                <h2 class="font-semibold">📦 Мои заказы</h2>
                <p>Просмотр и управление вашими заказами.</p>
                <a href="{{ route('orders.create') }}" class="text-blue-600">➕ Новый заказ</a>
                <a href="{{ route('orders.index') }}" class="text-blue-600">📋 Список заказов</a>
            </div>

            <!-- Профиль -->
            <div class="p-6 bg-white shadow rounded mb-6">
                <h2 class="font-semibold">⚙️ Профиль</h2>
                <p>Редактирование ваших данных и пароля.</p>
                <a href="{{ route('profile.edit') }}" class="text-blue-600">Перейти</a>
            </div>

            <!-- Компания -->
            <div class="p-6 bg-white shadow rounded mb-6">
                <h2 class="font-semibold">🏢 Моя компания</h2>
                <p>Информация о вашей организации.</p>
                <p><strong>Название:</strong> {{ $customer->company_name ?? '—' }}</p>
                <p><strong>Контактное лицо:</strong> {{ $customer->contact_person ?? '—' }}</p>
                <p><strong>Телефон:</strong> {{ $customer->phone ?? '—' }}</p>
                <p><strong>Адрес:</strong> {{ $customer->address ?? '—' }}</p>
                <p><strong>Договор №:</strong> {{ $customer->contract_number ?? '—' }}</p>
            </div>

            <!-- Статистика -->
            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">📊 Статистика</h2>
                <p>Общая квадратура ваших заказов за месяц:</p>
                <p class="text-xl font-bold">{{ $totalSquare ?? 0 }} м²</p>
            </div>
        </div>
    </div>
</x-app-layout>
