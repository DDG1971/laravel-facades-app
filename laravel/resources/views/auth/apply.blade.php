<!-- resources/views/auth/login.blade.php -->

<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Email -->
    <input type="email" name="email" required autofocus>

    <!-- Password -->
    <input type="password" name="password" required>

    <button type="submit">
        Войти
    </button>

    <div class="mt-4">
        <a href="{{ url('/apply') }}">Стать партнёром</a>
    </div>
</form>
