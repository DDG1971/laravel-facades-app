<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Customer;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['nullable', 'email', 'max:255'], // Новое поле для компании
            'phone' => ['nullable', 'string', 'max:50'], // Личный телефон менеджера
        ]);

        // 1. Сначала создаём компанию (Customer)
        $customer = Customer::create([
            'company_name' => $request->company_name,
            'email'        => $request->company_email, // Почта компании
            // Поле phone в Customer пока можно оставить пустым или писать туда рабочий тел.
        ]);

        // 2. Создаём пользователя (User) и сразу привязываем к компании
        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'phone'       => $request->phone, // Личный телефон в таблицу users
            'role'        => 'pending',
            'customer_id' => $customer->id, // Привязка сразу при создании
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
