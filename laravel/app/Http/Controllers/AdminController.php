<?php

namespace App\Http\Controllers;

use App\Models\Order;
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

        Log::info('Admin dashboard route reached');

        return view('admin.dashboard', ['totalSquare' => 0]);


        // для обычного пользователя
        //return view('dashboard');
    }
    public function clients()
    {
        $clients = \App\Models\Customer::all();
        return view('admin.clients.index', compact('clients'));
    }

    public function editClient($id): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        $client = \App\Models\Customer::findOrFail($id);
        return view('admin.clients.edit', compact('client'));
    }

    public function updateClient(Request $request, $id): \Illuminate\Http\RedirectResponse
    {
        $client = \App\Models\Customer::findOrFail($id);
        $client->update($request->all());

        return redirect()->route('admin.clients')->with('status', 'Client updated successfully!');
    }
    public function fullStatistics(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);

        $startOfMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endOfMonth = $startOfMonth->copy()->endOfMonth();

        // 1. Загружаем данные
        $orders = Order::with(['items.milling', 'items.facadeType', 'paintShop'])
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->get();

        // 2. Общие финансовые показатели (по заказам)
        $totalMoney = $orders->sum('total_price');
        $actuallyPaid = $orders->sum('paid_amount');
        $debt = $totalMoney - $actuallyPaid;

        // 3. Обработка позиций (цеха + фрезеровки + исключения витрин)
        $allItems = collect();
        foreach ($orders as $order) {
            $parentMillingName = $order->milling->name ?? 'Без фрезы';
            $workshopName = $order->paintShop->name ?? 'Не распределено';
            $workshopId = $order->paint_shop_id ?? 0;

            foreach ($order->items as $item) {
                $item->row_square = ($item->height * $item->width / 1_000_000) * $item->quantity;
                $item->workshop_id = $workshopId;
                $item->workshop_name = $workshopName;

                $fName = $item->facadeType->name_en ?? '';

                // Схлопываем витрины (s.o, s.c, s.x) в основную фрезу
                $isWindow = false;
                foreach (['s.o', 's.c', 's.x'] as $prefix) {
                    if (str_starts_with(strtolower(trim($fName)), $prefix)) {
                        $isWindow = true;
                        break;
                    }
                }

                if ($fName && !$isWindow && !in_array($fName, ['—', '', ' ', '-'])) {
                    $item->stat_name = $fName;
                } else {
                    $item->stat_name = $parentMillingName;
                }

                $allItems->push($item);
            }
        }

        // 4. Группировка для таблиц и общая квадратура
        $statsByWorkshop = $allItems->groupBy('workshop_id');
        $totalM2 = $allItems->sum('row_square');

        // 5. Данные для круговой диаграммы (Доли цехов)
        $chartLabels = [];
        $chartData = [];
        foreach ($statsByWorkshop as $id => $workshopItems) {
            $chartLabels[] = $workshopItems->first()->workshop_name;
            $chartData[] = $workshopItems->sum('row_square');
        }

        // 6. Данные для линейного графика (Динамика по дням)
        $daysInMonth = $startOfMonth->daysInMonth;
        $dailyLabels = [];
        $dailyData = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dateString = $startOfMonth->copy()->day($day)->format('d.m');
            $dailyLabels[] = $dateString;

            $dayM2 = $orders->filter(function($order) use ($day) {
                return $order->created_at->day == $day;
            })->sum(fn($o) => $o->total_square);

            $dailyData[] = $dayM2;
        }

        return view('admin.statistics', compact(
            'totalM2', 'totalMoney', 'actuallyPaid', 'debt',
            'statsByWorkshop', 'startOfMonth', 'month', 'year',
            'chartLabels', 'chartData', 'dailyLabels', 'dailyData'
        ));
    }



}
