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
            'phone' => ['nullable', 'string', 'max:50'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'pending',
        ]);

        // 3. Создаём компанию (только базовые поля)
        $customer = Customer::create([
            'company_name' => $request->company_name,
            'phone'        => $request->phone,
            // остальные поля (address, contract_number и т.п.) заполняет админ позже
        ]);

        // 4. Связываем пользователя с компанией
        $user->customer_id = $customer->id;
        $user->save();

        // 5. Запускаем событие Registered (уведомления, письма и т.д.)
        event(new Registered($user));

        // 6. Авторизуем и редиректим на dashboard
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
