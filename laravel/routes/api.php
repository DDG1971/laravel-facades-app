<?php

use App\Http\Controllers\TelegramWebhookController;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Telegram\Bot\Laravel\Facades\Telegram;

// Это  маршрут Laravel,
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//  это  НОВЫЙ маршрут для бота (без middleware auth)
Route::post('/telegram/webhook', [TelegramWebhookController::class, 'handle']);

// Маршрут для получения данных статуса (без защиты sanctum)
// Маршрут для получения данных статуса
Route::get('/orders/{order}/status-data', function (Order $order) {
    // Подгружаем статус, чтобы не было ошибок
    $order->load('status');

    return response()->json([
        'success'     => true,
        'status_id'   => $order->status_id, // КРИТИЧНО для селекта менеджера
        'label'       => $order->status->label,
        'status_key'  => $order->status->name,
        'date_status' => $order->date_status
            ? \Illuminate\Support\Carbon::parse($order->date_status)->format('d.m.Y')
            : '—',
    ]);
});

