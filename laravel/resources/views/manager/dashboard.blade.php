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
                <p class="text-sm text-gray-600 mb-4">Получайте извещения о новом заказе и фото изделий прямо в
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
    </div>
</x-app-layout>
