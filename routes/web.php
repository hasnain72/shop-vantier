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

        Route::post('products/download-images',      [\App\Http\Controllers\Admin\ProductController::class, 'downloadImages'])->name('products.download-images');
        Route::get('products/image-download-status', [\App\Http\Controllers\Admin\ProductController::class, 'imageDownloadStatus'])->name('products.image-download-status');
        Route::post('products/bulk-price-update',    [\App\Http\Controllers\Admin\ProductController::class, 'bulkPriceUpdate'])->name('products.bulk-price-update');
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

        // Product categories (Tools, Watch Roll, Buckle, etc.)
        Route::get('product-categories',                        [\App\Http\Controllers\Admin\ProductCategoryController::class, 'index'])->name('product-categories.index');
        Route::post('product-categories',                       [\App\Http\Controllers\Admin\ProductCategoryController::class, 'store'])->name('product-categories.store');
        Route::put('product-categories/{productCategory}',      [\App\Http\Controllers\Admin\ProductCategoryController::class, 'update'])->name('product-categories.update');
        Route::delete('product-categories/{productCategory}',   [\App\Http\Controllers\Admin\ProductCategoryController::class, 'destroy'])->name('product-categories.destroy');

        // Product types
        Route::get('product-types',                   [\App\Http\Controllers\Admin\ProductTypeController::class, 'index'])->name('product-types.index');
        Route::post('product-types',                  [\App\Http\Controllers\Admin\ProductTypeController::class, 'store'])->name('product-types.store');
        Route::put('product-types/{productType}',     [\App\Http\Controllers\Admin\ProductTypeController::class, 'update'])->name('product-types.update');
        Route::delete('product-types/{productType}',  [\App\Http\Controllers\Admin\ProductTypeController::class, 'destroy'])->name('product-types.destroy');

        // Import
        Route::get('import',  [\App\Http\Controllers\Admin\ImportController::class, 'index'])->name('import.index');
        Route::post('import', [\App\Http\Controllers\Admin\ImportController::class, 'store'])->name('import.store');

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

        // Home Page CMS
        Route::get('home',                                    [\App\Http\Controllers\Admin\HomePageController::class, 'settings'])->name('home.settings');
        Route::put('home',                                    [\App\Http\Controllers\Admin\HomePageController::class, 'updateSettings'])->name('home.settings.update');
        Route::get('home/craft-cards',                        [\App\Http\Controllers\Admin\HomePageController::class, 'craftCards'])->name('home.craft-cards');
        Route::get('home/craft-cards/create',                 [\App\Http\Controllers\Admin\HomePageController::class, 'createCraftCard'])->name('home.craft-cards.create');
        Route::post('home/craft-cards',                       [\App\Http\Controllers\Admin\HomePageController::class, 'storeCraftCard'])->name('home.craft-cards.store');
        Route::get('home/craft-cards/{card}/edit',            [\App\Http\Controllers\Admin\HomePageController::class, 'editCraftCard'])->name('home.craft-cards.edit');
        Route::put('home/craft-cards/{card}',                 [\App\Http\Controllers\Admin\HomePageController::class, 'updateCraftCard'])->name('home.craft-cards.update');
        Route::delete('home/craft-cards/{card}',              [\App\Http\Controllers\Admin\HomePageController::class, 'destroyCraftCard'])->name('home.craft-cards.destroy');
        Route::get('home/workshop',                           [\App\Http\Controllers\Admin\HomePageController::class, 'workshopItems'])->name('home.workshop');
        Route::get('home/workshop/create',                    [\App\Http\Controllers\Admin\HomePageController::class, 'createWorkshopItem'])->name('home.workshop.create');
        Route::post('home/workshop',                          [\App\Http\Controllers\Admin\HomePageController::class, 'storeWorkshopItem'])->name('home.workshop.store');
        Route::get('home/workshop/{item}/edit',               [\App\Http\Controllers\Admin\HomePageController::class, 'editWorkshopItem'])->name('home.workshop.edit');
        Route::put('home/workshop/{item}',                    [\App\Http\Controllers\Admin\HomePageController::class, 'updateWorkshopItem'])->name('home.workshop.update');
        Route::delete('home/workshop/{item}',                 [\App\Http\Controllers\Admin\HomePageController::class, 'destroyWorkshopItem'])->name('home.workshop.destroy');
        Route::post('home/workshop/{item}/toggle',            [\App\Http\Controllers\Admin\HomePageController::class, 'toggleWorkshopItem'])->name('home.workshop.toggle');
        Route::get('home/reviews',                            [\App\Http\Controllers\Admin\HomePageController::class, 'reviews'])->name('home.reviews');
        Route::get('home/reviews/create',                     [\App\Http\Controllers\Admin\HomePageController::class, 'createReview'])->name('home.reviews.create');
        Route::post('home/reviews',                           [\App\Http\Controllers\Admin\HomePageController::class, 'storeReview'])->name('home.reviews.store');
        Route::get('home/reviews/{review}/edit',              [\App\Http\Controllers\Admin\HomePageController::class, 'editReview'])->name('home.reviews.edit');
        Route::put('home/reviews/{review}',                   [\App\Http\Controllers\Admin\HomePageController::class, 'updateReview'])->name('home.reviews.update');
        Route::delete('home/reviews/{review}',                [\App\Http\Controllers\Admin\HomePageController::class, 'destroyReview'])->name('home.reviews.destroy');
        Route::post('home/reviews/{review}/toggle',           [\App\Http\Controllers\Admin\HomePageController::class, 'toggleReview'])->name('home.reviews.toggle');
        Route::get('home/faqs',                               [\App\Http\Controllers\Admin\HomePageController::class, 'faqs'])->name('home.faqs');
        Route::get('home/faqs/create',                        [\App\Http\Controllers\Admin\HomePageController::class, 'createFaq'])->name('home.faqs.create');
        Route::post('home/faqs',                              [\App\Http\Controllers\Admin\HomePageController::class, 'storeFaq'])->name('home.faqs.store');
        Route::get('home/faqs/{faq}/edit',                    [\App\Http\Controllers\Admin\HomePageController::class, 'editFaq'])->name('home.faqs.edit');
        Route::put('home/faqs/{faq}',                         [\App\Http\Controllers\Admin\HomePageController::class, 'updateFaq'])->name('home.faqs.update');
        Route::delete('home/faqs/{faq}',                      [\App\Http\Controllers\Admin\HomePageController::class, 'destroyFaq'])->name('home.faqs.destroy');
        Route::post('home/faqs/{faq}/toggle',                 [\App\Http\Controllers\Admin\HomePageController::class, 'toggleFaq'])->name('home.faqs.toggle');

        // Webhooks
        Route::get('webhooks',                       [\App\Http\Controllers\Admin\WebhookController::class, 'index'])->name('webhooks.index');
        Route::post('webhooks',                      [\App\Http\Controllers\Admin\WebhookController::class, 'store'])->name('webhooks.store');
        Route::put('webhooks/{webhook}',             [\App\Http\Controllers\Admin\WebhookController::class, 'update'])->name('webhooks.update');
        Route::delete('webhooks/{webhook}',          [\App\Http\Controllers\Admin\WebhookController::class, 'destroy'])->name('webhooks.destroy');
        Route::patch('webhooks/{webhook}/secret',    [\App\Http\Controllers\Admin\WebhookController::class, 'regenerateSecret'])->name('webhooks.secret');

        Route::prefix('products/{product}')->as('products.')->group(function () {
            Route::post('variants/bulk-price-update', [\App\Http\Controllers\Admin\ProductVariantController::class, 'bulkPriceUpdate'])->name('variants.bulk-price-update');
            Route::get('variants', [\App\Http\Controllers\Admin\ProductVariantController::class, 'index'])->name('variants.index');
            Route::get('variants/create', [\App\Http\Controllers\Admin\ProductVariantController::class, 'create'])->name('variants.create');
            Route::post('variants', [\App\Http\Controllers\Admin\ProductVariantController::class, 'store'])->name('variants.store');
            Route::get('variants/{variant}/edit', [\App\Http\Controllers\Admin\ProductVariantController::class, 'edit'])->name('variants.edit');
            Route::put('variants/{variant}', [\App\Http\Controllers\Admin\ProductVariantController::class, 'update'])->name('variants.update');
            Route::delete('variants/{variant}', [\App\Http\Controllers\Admin\ProductVariantController::class, 'destroy'])->name('variants.destroy');

            // Product Addons (Buckle options etc.)
            Route::get('addons',                                    [\App\Http\Controllers\Admin\ProductAddonController::class, 'index'])->name('addons.index');
            Route::post('addons',                                   [\App\Http\Controllers\Admin\ProductAddonController::class, 'store'])->name('addons.store');
            Route::put('addons/{addon}',                            [\App\Http\Controllers\Admin\ProductAddonController::class, 'update'])->name('addons.update');
            Route::delete('addons/{addon}',                         [\App\Http\Controllers\Admin\ProductAddonController::class, 'destroy'])->name('addons.destroy');
            Route::post('addons/{addon}/adjust-inventory',          [\App\Http\Controllers\Admin\ProductAddonController::class, 'adjustInventory'])->name('addons.adjust-inventory');
        });
    });
});
