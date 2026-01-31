<x-app-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @if(auth()->user()->role === 'pending')
        <div class="flex items-center justify-center min-h-[70vh] bg-gray-100 dark:bg-gray-900">
            <div class="bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg text-center max-w-md">
                <!-- Иконка ожидания -->
                <svg class="mx-auto mb-4 w-16 h-16 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>

                <h1 class="text-2xl font-bold mb-2 text-gray-800 dark:text-gray-100">
                    Ваш аккаунт ожидает подтверждения
                </h1>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Администратор проверит вашу регистрацию и активирует доступ.
                </p>

                <!-- Кнопка выхода -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md shadow">
                        Выйти
                    </button>
                </form>
            </div>
        </div>
    @else
        <!-- Старый дашборд -->
        <h1>СТАРЫЙ DASHBOARD</h1>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __("You're logged in!") }}
                    </div>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>

