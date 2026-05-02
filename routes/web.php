<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


Route::get('/clear', function () {
    // Clear cache
   
    Artisan::call('config:cache');

    Artisan::call('cache:clear');

    // Clear compiled views
    Artisan::call('view:clear');

     // Clear compiled views
    Artisan::call('route:clear');


     // Clear storage:link views
    Artisan::call('storage:link');

    // Clear sessions

    return 'Cache, views ,link cleared successfully.';
});



Route::get('/', function () {
    return redirect()->route('admin.login');
});

Route::get('/api/v1/docs', fn () => view('api.docs'))->name('api.docs');
Route::get('/api/v1/docs/spec', fn () => response()->json(require resource_path('openapi.php')))->name('api.docs.spec');

Route::prefix('admin')->as('admin.')->group(function () {
    Route::get('login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.post');

    Route::middleware('admin')->group(function () {
        Route::post('logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('collections', \App\Http\Controllers\Admin\CollectionController::class)
            ->except(['show'])
            ->names('collections');

        Route::resource('products', \App\Http\Controllers\Admin\ProductController::class)
            ->except(['show'])
            ->names('products');

        // Settings
        Route::prefix('settings')->as('settings.')->group(function () {
            Route::get('payments',  [\App\Http\Controllers\Admin\Settings\PaymentSettingsController::class, 'index'])->name('payments');
            Route::put('payments',  [\App\Http\Controllers\Admin\Settings\PaymentSettingsController::class, 'update'])->name('payments.update');
            Route::get('store',         [\App\Http\Controllers\Admin\Settings\StoreSettingsController::class, 'show'])->name('store');
            Route::put('store',         [\App\Http\Controllers\Admin\Settings\StoreSettingsController::class, 'update'])->name('store.update');
            Route::get('taxes',         [\App\Http\Controllers\Admin\Settings\StoreSettingsController::class, 'taxes'])->name('taxes');
            Route::put('taxes',         [\App\Http\Controllers\Admin\Settings\StoreSettingsController::class, 'updateTaxes'])->name('taxes.update');
            Route::get('notifications', [\App\Http\Controllers\Admin\Settings\StoreSettingsController::class, 'notifications'])->name('notifications');
            Route::put('notifications', [\App\Http\Controllers\Admin\Settings\StoreSettingsController::class, 'updateNotifications'])->name('notifications.update');
            Route::get('scripts',       [\App\Http\Controllers\Admin\Settings\ScriptTagController::class, 'index'])->name('scripts');
            Route::put('scripts',       [\App\Http\Controllers\Admin\Settings\ScriptTagController::class, 'update'])->name('scripts.update');
            Route::get('legal',         [\App\Http\Controllers\Admin\Settings\LegalController::class, 'index'])->name('legal');
            Route::put('legal',         [\App\Http\Controllers\Admin\Settings\LegalController::class, 'update'])->name('legal.update');
            Route::get('checkout',      [\App\Http\Controllers\Admin\Settings\CheckoutController::class, 'index'])->name('checkout');
            Route::put('checkout',      [\App\Http\Controllers\Admin\Settings\CheckoutController::class, 'update'])->name('checkout.update');

            // Country-specific tax rates
            Route::post('tax-rates',              [\App\Http\Controllers\Admin\Settings\StoreSettingsController::class, 'storeTaxRate'])->name('tax-rates.store');
            Route::put('tax-rates/{taxRate}',     [\App\Http\Controllers\Admin\Settings\StoreSettingsController::class, 'updateTaxRate'])->name('tax-rates.update');
            Route::delete('tax-rates/{taxRate}',  [\App\Http\Controllers\Admin\Settings\StoreSettingsController::class, 'destroyTaxRate'])->name('tax-rates.destroy');
        });

        // Product types
        Route::get('product-types',                   [\App\Http\Controllers\Admin\ProductTypeController::class, 'index'])->name('product-types.index');
        Route::post('product-types',                  [\App\Http\Controllers\Admin\ProductTypeController::class, 'store'])->name('product-types.store');
        Route::put('product-types/{productType}',     [\App\Http\Controllers\Admin\ProductTypeController::class, 'update'])->name('product-types.update');
        Route::delete('product-types/{productType}',  [\App\Http\Controllers\Admin\ProductTypeController::class, 'destroy'])->name('product-types.destroy');

        // Activity log
        Route::get('activity', [\App\Http\Controllers\Admin\ActivityController::class, 'index'])->name('activity.index');

        // Reports
        Route::get('reports',            [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/sales',      [\App\Http\Controllers\Admin\ReportController::class, 'sales'])->name('reports.sales');
        Route::get('reports/products',   [\App\Http\Controllers\Admin\ReportController::class, 'products'])->name('reports.products');
        Route::get('reports/customers',  [\App\Http\Controllers\Admin\ReportController::class, 'customers'])->name('reports.customers');
        Route::get('reports/inventory',  [\App\Http\Controllers\Admin\ReportController::class, 'inventory'])->name('reports.inventory');

        // Pages (CMS)
        Route::resource('pages', \App\Http\Controllers\Admin\PageController::class)->names('pages');

        // Blogs & Articles
        Route::get('blogs',                                                       [\App\Http\Controllers\Admin\BlogController::class, 'index'])->name('blogs.index');
        Route::post('blogs',                                                      [\App\Http\Controllers\Admin\BlogController::class, 'storeBlog'])->name('blogs.store');
        Route::delete('blogs/{blog}',                                             [\App\Http\Controllers\Admin\BlogController::class, 'destroyBlog'])->name('blogs.destroy');
        Route::get('blogs/{blog}/articles',                                       [\App\Http\Controllers\Admin\BlogController::class, 'articles'])->name('blogs.articles');
        Route::get('blogs/{blog}/articles/create',                                [\App\Http\Controllers\Admin\BlogController::class, 'createArticle'])->name('blogs.articles.create');
        Route::post('blogs/{blog}/articles',                                      [\App\Http\Controllers\Admin\BlogController::class, 'storeArticle'])->name('blogs.articles.store');
        Route::get('blogs/{blog}/articles/{article}/edit',                        [\App\Http\Controllers\Admin\BlogController::class, 'editArticle'])->name('blogs.articles.edit');
        Route::put('blogs/{blog}/articles/{article}',                             [\App\Http\Controllers\Admin\BlogController::class, 'updateArticle'])->name('blogs.articles.update');
        Route::delete('blogs/{blog}/articles/{article}',                          [\App\Http\Controllers\Admin\BlogController::class, 'destroyArticle'])->name('blogs.articles.destroy');

        // Orders
        Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->except(['show']);
        Route::get('orders/{order}',        [\App\Http\Controllers\Admin\OrderController::class, 'show'])->name('orders.show');
        Route::post('orders/{order}/cancel',    [\App\Http\Controllers\Admin\OrderController::class, 'cancelOrder'])->name('orders.cancel');
        Route::post('orders/{order}/fulfill',   [\App\Http\Controllers\Admin\OrderController::class, 'fulfillOrder'])->name('orders.fulfill');
        Route::post('orders/{order}/mark-paid', [\App\Http\Controllers\Admin\OrderController::class, 'markAsPaid'])->name('orders.mark-paid');
        Route::post('orders/{order}/archive',   [\App\Http\Controllers\Admin\OrderController::class, 'archiveOrder'])->name('orders.archive');
        Route::post('orders/{order}/unarchive', [\App\Http\Controllers\Admin\OrderController::class, 'unarchiveOrder'])->name('orders.unarchive');
        Route::get('orders/{order}/print',      [\App\Http\Controllers\Admin\OrderController::class, 'printOrder'])->name('orders.print');
        Route::get('orders/{order}/refunds/create', [\App\Http\Controllers\Admin\RefundController::class, 'create'])->name('orders.refunds.create');
        Route::post('orders/{order}/refunds',       [\App\Http\Controllers\Admin\RefundController::class, 'store'])->name('orders.refunds.store');

        Route::resource('customers', \App\Http\Controllers\Admin\CustomerController::class)
            ->except(['show'])
            ->names('customers');
        Route::get('customers/{customer}', [\App\Http\Controllers\Admin\CustomerController::class, 'show'])->name('customers.show');
        Route::post('customers/{customer}/invite',     [\App\Http\Controllers\Admin\CustomerController::class, 'sendInvite'])->name('customers.invite');
        Route::post('customers/{customer}/deactivate', [\App\Http\Controllers\Admin\CustomerController::class, 'deactivate'])->name('customers.deactivate');
        Route::post('customers/{customer}/activate',   [\App\Http\Controllers\Admin\CustomerController::class, 'activate'])->name('customers.activate');

        // Discounts
        Route::resource('discounts', \App\Http\Controllers\Admin\DiscountController::class)->names('discounts');
        Route::get('discounts/generate-code', [\App\Http\Controllers\Admin\DiscountController::class, 'generateCode'])->name('discounts.generate-code');

        // Shipping
        Route::get('shipping', [\App\Http\Controllers\Admin\ShippingController::class, 'index'])->name('shipping.index');
        Route::post('shipping/zones',          [\App\Http\Controllers\Admin\ShippingController::class, 'storeZone'])->name('shipping.zones.store');
        Route::put('shipping/zones/{zone}',    [\App\Http\Controllers\Admin\ShippingController::class, 'updateZone'])->name('shipping.zones.update');
        Route::delete('shipping/zones/{zone}', [\App\Http\Controllers\Admin\ShippingController::class, 'deleteZone'])->name('shipping.zones.delete');
        Route::post('shipping/zones/{zone}/rates',  [\App\Http\Controllers\Admin\ShippingController::class, 'storeRate'])->name('shipping.rates.store');
        Route::put('shipping/rates/{rate}',         [\App\Http\Controllers\Admin\ShippingController::class, 'updateRate'])->name('shipping.rates.update');
        Route::delete('shipping/rates/{rate}',      [\App\Http\Controllers\Admin\ShippingController::class, 'deleteRate'])->name('shipping.rates.delete');

        // Inventory
        Route::get('inventory', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('inventory.index');
        Route::post('inventory/adjust',   [\App\Http\Controllers\Admin\InventoryController::class, 'adjust'])->name('inventory.adjust');
        Route::post('inventory/transfer', [\App\Http\Controllers\Admin\InventoryController::class, 'transfer'])->name('inventory.transfer');
        Route::get('inventory/{item}/history', [\App\Http\Controllers\Admin\InventoryController::class, 'history'])->name('inventory.history');

        // Locations
        Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class)->names('locations');

        // Webhooks
        Route::get('webhooks',                       [\App\Http\Controllers\Admin\WebhookController::class, 'index'])->name('webhooks.index');
        Route::post('webhooks',                      [\App\Http\Controllers\Admin\WebhookController::class, 'store'])->name('webhooks.store');
        Route::put('webhooks/{webhook}',             [\App\Http\Controllers\Admin\WebhookController::class, 'update'])->name('webhooks.update');
        Route::delete('webhooks/{webhook}',          [\App\Http\Controllers\Admin\WebhookController::class, 'destroy'])->name('webhooks.destroy');
        Route::patch('webhooks/{webhook}/secret',    [\App\Http\Controllers\Admin\WebhookController::class, 'regenerateSecret'])->name('webhooks.secret');

        Route::prefix('products/{product}')->as('products.')->group(function () {
            Route::get('variants', [\App\Http\Controllers\Admin\ProductVariantController::class, 'index'])->name('variants.index');
            Route::get('variants/create', [\App\Http\Controllers\Admin\ProductVariantController::class, 'create'])->name('variants.create');
            Route::post('variants', [\App\Http\Controllers\Admin\ProductVariantController::class, 'store'])->name('variants.store');
            Route::get('variants/{variant}/edit', [\App\Http\Controllers\Admin\ProductVariantController::class, 'edit'])->name('variants.edit');
            Route::put('variants/{variant}', [\App\Http\Controllers\Admin\ProductVariantController::class, 'update'])->name('variants.update');
            Route::delete('variants/{variant}', [\App\Http\Controllers\Admin\ProductVariantController::class, 'destroy'])->name('variants.destroy');
        });
    });
});
