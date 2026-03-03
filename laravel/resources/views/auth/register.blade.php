<x-guest-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- 1. АККАУНТ (Email и Пароль) -->
        <div>
            <x-input-label for="name" :value="__('ФИО Менеджера')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Ваш личный Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
        </div>

        <!-- 2. ЛИЧНЫЙ ТЕЛЕФОН (Пойдет в users) -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Ваш личный телефон')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" placeholder="+375 (__) ___-__-__" required />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- 3. ПАРОЛЬ -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Пароль')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Подтверждение пароля')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
        </div>

        <!-- 4. ДАННЫЕ КОМПАНИИ (Пойдут в customers) -->
        <div class="mt-8 p-4 bg-gray-100 rounded-lg shadow-inner">
            <h3 class="text-xs font-bold text-gray-500 mb-4 uppercase tracking-widest">Организация</h3>

            <div class="mt-4">
                <x-input-label for="company_name" :value="__('Название компании')" />
                <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')" required />
            </div>

            <div class="mt-4">
                <x-input-label for="company_email" :value="__('Email компании (общий)')" />
                <x-text-input id="company_email" class="block mt-1 w-full" type="email" name="company_email" :value="old('company_email')" />
            </div>
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>{{ __('Зарегистрироваться') }}</x-primary-button>
        </div>
    </form>
</x-guest-layout>
