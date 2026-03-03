<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ManagerDashboardController extends Controller
{
    public function index()
    {
        // Считаем общую квадратуру всех заказов за текущий месяц
        $totalSquare = Order::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('square_meters');

        return view('manager.dashboard', [
            'totalSquare' => round($totalSquare, 2)
        ]);
    }
}
