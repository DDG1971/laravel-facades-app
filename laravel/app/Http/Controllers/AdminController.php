<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function pending()
    {
        $users = User::where('role', 'pending')->get();
        return view('admin.pending', compact('users'));
    }

    public function approve(User $user)
    {
        if ($user->role !== 'pending') {
            return redirect()->route('admin.pending')->with('status', 'Пользователь уже одобрен или имеет другую роль.');
        }

        $user->update(['role' => 'customer']); // или 'partner'
        return redirect()->route('admin.pending')->with('status', 'User approved!');
    }
    public function dashboard(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
       /* if (auth()->user()->role === 'admin') {
        // считаем статистику для админа
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth   = Carbon::now()->endOfMonth();

        $totalSquare = OrderItem::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('square_meters');
            return view('admin.dashboard', ['totalSquare' => 0]);*/
        //return view('admin.dashboard', compact('totalSquare'));
        Log::info('Admin dashboard route reached');

        return view('admin.dashboard', ['totalSquare' => 0]);


        // для обычного пользователя
        //return view('dashboard');
    }
}
