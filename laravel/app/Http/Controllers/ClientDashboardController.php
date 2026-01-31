<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ClientDashboardController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = \Illuminate\Support\Facades\Auth::user();
        Log::info('Client dashboard reached', ['user_id' => $user->id, 'role' => $user->role]);

        $customer = $user->customer;
        $orders = $customer ? $customer->orders()->with('items')->get() : collect();

        // считаю общую квадратуру заказов за месяц
        $totalSquare = $orders
            ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum(function ($order) { return $order->total_square;
                // использую аксессор
                 });

        return view('client.dashboard', compact('user', 'customer', 'orders', 'totalSquare'));
    }
}
