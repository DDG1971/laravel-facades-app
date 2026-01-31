<x-guest-layout>
    <x-slot name="head">
        <x-assets />
    </x-slot>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Спасибо за регистрацию!') }}
        <br><br>
        Перед началом работы подтвердите свой адрес электронной почты.
        Это нужно, чтобы мы убедились, что указанный email принадлежит вам
        и вы сможете получать уведомления о заказах, статусах и других важных событиях.
        <br><br>
        Перейдите по ссылке в письме, которое мы только что отправили.
        Если письмо не пришло — нажмите кнопку ниже, и мы вышлем его повторно.
    </div>
    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Новое письмо с ссылкой для подтверждения отправлено на ваш адрес электронной почты.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <div>
                <x-primary-button>
                    {{ __('Отправить письмо повторно') }}
                </x-primary-button>
            </div>
        </form>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                {{ __('Выйти из аккаунта') }}
            </button>
        </form>
    </div>
</x-guest-layout>
