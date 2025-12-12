<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    Log::info('Dashboard route reached');
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Профиль
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Заказы
    Route::resource('orders', OrderController::class);
    Route::post('/orders/{order}/submit', [OrderController::class, 'submit'])->name('orders.submit');
    Route::delete('/order-items/{item}', [OrderController::class, 'destroyItem'])->name('order-items.destroy');
});

// Админка
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/pending-users', [AdminController::class, 'pending'])->name('admin.pending');
    Route::post('/admin/approve/{user}', [AdminController::class, 'approve'])->name('admin.approve');
});

Route::get('/test', function () {
    return view('test');
});


require __DIR__.'/auth.php';
