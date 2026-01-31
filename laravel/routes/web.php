<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CoatingTypeController;
use App\Http\Controllers\ColorCatalogController;
use App\Http\Controllers\ColorCodeController;
use App\Http\Controllers\FacadeTypeController;
use App\Http\Controllers\MillingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ThicknessController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;


Route::get('/', function () {
    return view('welcome');
});

/*Route::get('/dashboard', function () {
    Log::info('Dashboard route reached');
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');--}}*/

// Универсальный маршрут Dashboard
Route::middleware(['auth'])->get('/dashboard', function () {
    $user = auth()->user();

    return match (true) {
        $user->hasRole('admin') => redirect()->route('admin.dashboard'),
        $user->hasRole('customer') => redirect()->route('client.dashboard'),
        $user->hasRole('manager') => redirect()->route('manager.dashboard'),
        default => redirect()->route('client.dashboard'),
    };
   })->name('dashboard');


// Профиль
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Заказы
    Route::resource('orders', OrderController::class);
    Route::post('/orders/{order}/submit', [OrderController::class, 'submit'])->name('orders.submit');
    Route::delete('/order-items/{item}', [OrderController::class, 'destroyItem'])->name('order-items.destroy');
    Route::get('/orders/{order}/preview', [OrderController::class, 'preview'])->name('orders.preview');
    Route::get('/orders/{order}/saw', [OrderController::class, 'saw'])->name('orders.saw');

});

// Админка
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/pending-users', [AdminController::class, 'pending'])->name('admin.pending');
    Route::post('/admin/approve/{user}', [AdminController::class, 'approve'])->name('admin.approve');

    // 🔹 управление клиентами
    Route::get('/admin/clients', [AdminController::class, 'clients'])->name('admin.clients');
    Route::get('/admin/clients/{id}/edit', [AdminController::class, 'editClient'])->name('admin.clients.edit');
    Route::put('/admin/clients/{id}', [AdminController::class, 'updateClient'])->name('admin.clients.update');

    // 🔹 управление фрезеровками,типами фасадов
    Route::resource('/admin/millings', MillingController::class);
    Route::resource('/admin/facade-types', FacadeTypeController::class);
    Route::resource('/admin/color_catalogs', ColorCatalogController::class);
    Route::resource('/admin/color_codes', ColorCodeController::class);
    Route::resource('/admin/coating-types', CoatingTypeController::class);
    Route::resource('/admin/thicknesses', ThicknessController::class);

    // 🔹 заказы для админа
    Route::get('/admin/orders', [OrderController::class, 'index']) ->name('admin.orders.index');
    Route::get('/admin/orders/create', [OrderController::class, 'create'])->name('admin.orders.create');
    Route::post('/admin/orders', [OrderController::class, 'store'])->name('admin.orders.store');
    // расчеты для админа
    Route::get('/orders/{order}/manage', [OrderController::class, 'manage']) ->name('orders.manage');
    // изм.статуса для админа
    Route::put('/orders/{order}/status', [OrderController::class, 'updateStatus']) ->name('orders.updateStatus');

    Route::get('/orders/{order}/export-pdf', [OrderController::class, 'exportClientPdf']) ->name('orders.export.pdf');
    Route::post('/orders/{order}/send-calculation', [OrderController::class, 'sendCalculation']) ->name('orders.send.calculation');

});


// Клиентский дашборд
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/client/dashboard', [\App\Http\Controllers\ClientDashboardController::class, 'index'])->name('client.dashboard');
    Route::get('/orders/create', [OrderController::class, 'createClient'])->name('orders.create');
    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders', [OrderController::class, 'indexClient'])->name('orders.index');

});

Route::get('/test', function () {
    return view('test');
});


require __DIR__.'/auth.php';
