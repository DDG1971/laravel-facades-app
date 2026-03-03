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
                <h2 class="font-semibold text-lg mb-4">🏢 Моя компания</h2>
                <p class="text-gray-500 mb-4 text-sm">Информация о вашей организации.</p>

                <div class="space-y-2">
                    <p><strong>Название:</strong> {{ $customer->company_name ?? '—' }}</p>
                    <p><strong>Контактное лицо (директор):</strong> {{ $customer->contact_person ?? '—' }}</p>
                    <p><strong>Телефон компании:</strong> {{ $customer->phone ?? '—' }}</p>

                    <!-- Добавляем вывод email компании -->
                    <p><strong>Email компании:</strong>
                        <span class="text-blue-600">{{ $customer->email ?? '—' }}</span>
                    </p>

                    <p><strong>Адрес:</strong> {{ $customer->address ?? '—' }}</p>
                    <p><strong>Договор №:</strong> <span class="font-mono bg-gray-100 px-1">{{ $customer->contract_number ?? '—' }}</span></p>
                </div>
            </div>
            <!-- Статистика -->
            <div class="p-6 bg-white shadow rounded">
                <h2 class="font-semibold">📊 Статистика</h2>
                <p>Общая квадратура ваших заказов за месяц:</p>
                <p class="text-xl font-bold">{{ $totalSquare ?? 0 }} м²</p>
            </div>
            <!-- Telegram Уведомления -->
            <div class="p-6 bg-white shadow rounded mb-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between mb-2">
                    <h2 class="font-semibold text-gray-800">📲 Telegram-инфо</h2>
                    <!-- Мини-иконка телеграма -->
                    <svg class="w-6 h-6 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm5.891 8.146l-1.92 9.043c-.145.639-.524.796-1.06.495l-2.924-2.154-1.411 1.358c-.156.156-.287.287-.588.287l.209-2.969 5.405-4.884c.235-.209-.052-.325-.366-.117l-6.681 4.208-2.877-.899c-.626-.195-.638-.626.13-.925l11.245-4.333c.521-.195.976.117.838.924z"/>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 mb-4">Получайте статусы готовности фасадов и фото изделий прямо в
                    мессенджер.</p>
                @php /** @var \App\Models\User $user */ $user = auth()->user(); @endphp
                @if($user && $user->telegram_chat_id)
                    <span class="text-green-600 font-medium flex items-center">
            ✅ Подключено
        </span>
                @else
                    <a href="https://t.me/stylefasad_bot?start={{ auth()->id() }}"
                       target="_blank"
                       class="inline-block bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition">
                        Подключить бота
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
