<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->as('api.v1.')
    ->group(function () {
        Route::get('/health', fn () => response()->json(['ok' => true]))->name('health');

        Route::prefix('auth')->as('auth.')->group(function () {
            Route::post('register', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'register'])->name('register');
            Route::post('login', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'login'])->name('login');
            Route::post('forgot-password', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'forgotPassword'])->name('forgot-password');
            Route::post('reset-password', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'resetPassword'])->name('reset-password');

            Route::middleware('auth:customer')->group(function () {
                Route::post('logout', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'logout'])->name('logout');
                Route::get('me', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'me'])->name('me');
                Route::put('profile', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'updateProfile'])->name('profile');
                Route::put('change-password', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'changePassword'])->name('change-password');
            });
        });

        // Customer profile & addresses (authenticated)
        Route::middleware('auth:customer')->prefix('customers')->as('customers.')->group(function () {
            Route::get('me',     [\App\Http\Controllers\Api\V1\CustomerController::class, 'me'])->name('me');
            Route::put('me',     [\App\Http\Controllers\Api\V1\CustomerController::class, 'updateMe'])->name('me.update');
            Route::get('me/orders', [\App\Http\Controllers\Api\V1\CustomerController::class, 'orders'])->name('me.orders');
            Route::get('me/addresses',                         [\App\Http\Controllers\Api\V1\CustomerController::class, 'addresses'])->name('me.addresses');
            Route::post('me/addresses',                        [\App\Http\Controllers\Api\V1\CustomerController::class, 'storeAddress'])->name('me.addresses.store');
            Route::put('me/addresses/{address_id}',            [\App\Http\Controllers\Api\V1\CustomerController::class, 'updateAddress'])->name('me.addresses.update');
            Route::delete('me/addresses/{address_id}',         [\App\Http\Controllers\Api\V1\CustomerController::class, 'destroyAddress'])->name('me.addresses.destroy');
            Route::put('me/addresses/{address_id}/default',    [\App\Http\Controllers\Api\V1\CustomerController::class, 'setDefaultAddress'])->name('me.addresses.default');
        });

        // Orders
        Route::post('orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'store'])->name('orders.store');
        Route::middleware('auth:customer')->group(function () {
            Route::get('orders/{order}',         [\App\Http\Controllers\Api\V1\OrderController::class, 'show'])->name('orders.show');
            Route::post('orders/{order}/cancel', [\App\Http\Controllers\Api\V1\OrderController::class, 'cancel'])->name('orders.cancel');
        });

        // Shipping rates (public)
        Route::get('shipping/rates',    [\App\Http\Controllers\Api\V1\ShippingController::class, 'rates'])->name('shipping.rates');
        Route::post('shipping/estimate',[\App\Http\Controllers\Api\V1\ShippingController::class, 'estimate'])->name('shipping.estimate');

        // Inventory (admin API — auth:sanctum for staff)
        Route::middleware('auth:sanctum')->prefix('inventory')->as('inventory.')->group(function () {
            Route::get('/',                  [\App\Http\Controllers\Api\V1\InventoryController::class, 'index'])->name('index');
            Route::post('adjust',            [\App\Http\Controllers\Api\V1\InventoryController::class, 'adjust'])->name('adjust');
            Route::post('transfer',          [\App\Http\Controllers\Api\V1\InventoryController::class, 'transfer'])->name('transfer');
            Route::get('{item}/history',     [\App\Http\Controllers\Api\V1\InventoryController::class, 'history'])->name('history');
        });

        // Products
        Route::get('products/count', [\App\Http\Controllers\Api\V1\ProductController::class, 'count'])->name('products.count');
        Route::get('products/{product}', [\App\Http\Controllers\Api\V1\ProductController::class, 'show'])->name('products.show');
        Route::get('products', [\App\Http\Controllers\Api\V1\ProductController::class, 'index'])->name('products.index');

        // Collections
        Route::get('collections/{collection}/products', [\App\Http\Controllers\Api\V1\CollectionController::class, 'products'])->name('collections.products');
        Route::get('collections/{collection}', [\App\Http\Controllers\Api\V1\CollectionController::class, 'show'])->name('collections.show');
        Route::get('collections', [\App\Http\Controllers\Api\V1\CollectionController::class, 'index'])->name('collections.index');
    });

