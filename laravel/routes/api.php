<?php

use App\Http\Controllers\TelegramWebhookController;
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

