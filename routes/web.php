<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Catalog\CatalogController;
use App\Http\Controllers\Cart\CartController;
use App\Http\Controllers\Orders\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:login')
        ->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');

    Route::get('/', function () {
        return redirect()->route('catalog.index');
    })->name('home');

    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
    Route::get('/catalog/{product}', [CatalogController::class, 'show'])->name('catalog.show');
    Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
    Route::post('/cart/items/{product}', [CartController::class, 'add'])->block(10, 10)->name('cart.add');
    Route::patch('/cart/items/{product}', [CartController::class, 'update'])->block(10, 10)->name('cart.update');
    Route::delete('/cart/items/{product}', [CartController::class, 'remove'])->block(10, 10)->name('cart.remove');
    Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('/orders', [CheckoutController::class, 'store'])->block(10, 10)->name('orders.store');
    Route::get('/orders/{order}', [CheckoutController::class, 'order'])->whereNumber('order')->name('orders.show');
});
