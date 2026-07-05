<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->as('api.v1.')
    ->middleware('throttle:api')
    ->group(function () {

        // Health
        Route::get('/health', fn () => response()->json(['success' => true, 'message' => 'OK']))->name('health');

        // Home page (all sections in one call)
        Route::get('home', [\App\Http\Controllers\Api\V1\HomePageController::class, 'show'])->name('home');

        // Shop info
        Route::get('shop', [\App\Http\Controllers\Api\V1\ShopController::class, 'show'])->name('shop');

        // Auth
        Route::prefix('auth')->as('auth.')->group(function () {
            Route::post('register',       [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'register'])->name('register');
            Route::post('login',          [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'login'])->name('login');
            Route::post('forgot-password',[\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'forgotPassword'])->name('forgot-password');
            Route::post('reset-password', [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'resetPassword'])->name('reset-password');

            Route::middleware('auth:customer')->group(function () {
                Route::post('logout',          [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'logout'])->name('logout');
                Route::get('me',               [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'me'])->name('me');
                Route::put('profile',          [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'updateProfile'])->name('profile');
                Route::put('change-password',  [\App\Http\Controllers\Api\V1\Auth\CustomerAuthController::class, 'changePassword'])->name('change-password');
            });
        });

        // Customer profile & addresses
        Route::middleware('auth:customer')->prefix('customers')->as('customers.')->group(function () {
            Route::get('me',                                    [\App\Http\Controllers\Api\V1\CustomerController::class, 'me'])->name('me');
            Route::put('me',                                    [\App\Http\Controllers\Api\V1\CustomerController::class, 'updateMe'])->name('me.update');
            Route::get('me/orders',                             [\App\Http\Controllers\Api\V1\CustomerController::class, 'orders'])->name('me.orders');
            Route::get('me/custom-orders',                      [\App\Http\Controllers\Api\V1\CustomerController::class, 'customOrders'])->name('me.custom-orders');
            Route::get('me/addresses',                          [\App\Http\Controllers\Api\V1\CustomerController::class, 'addresses'])->name('me.addresses');
            Route::post('me/addresses',                         [\App\Http\Controllers\Api\V1\CustomerController::class, 'storeAddress'])->name('me.addresses.store');
            Route::put('me/addresses/{address_id}',             [\App\Http\Controllers\Api\V1\CustomerController::class, 'updateAddress'])->name('me.addresses.update');
            Route::delete('me/addresses/{address_id}',          [\App\Http\Controllers\Api\V1\CustomerController::class, 'destroyAddress'])->name('me.addresses.destroy');
            Route::put('me/addresses/{address_id}/default',     [\App\Http\Controllers\Api\V1\CustomerController::class, 'setDefaultAddress'])->name('me.addresses.default');
        });

        // Product Categories (Tools, Watch Roll, Buckle, etc.)
        Route::get('product-categories',                        [\App\Http\Controllers\Api\V1\ProductCategoryController::class, 'index'])->name('product-categories.index');
        Route::get('product-categories/{slug}/products',        [\App\Http\Controllers\Api\V1\ProductCategoryController::class, 'products'])->name('product-categories.products');
        Route::get('product-categories/{slug}',                 [\App\Http\Controllers\Api\V1\ProductCategoryController::class, 'show'])->name('product-categories.show');

        // Products (before /{product} to avoid route conflicts)
        Route::get('products/count',             [\App\Http\Controllers\Api\V1\ProductController::class, 'count'])->name('products.count');
        Route::get('products/price-range',       [\App\Http\Controllers\Api\V1\ProductController::class, 'priceRange'])->name('products.price-range');
        Route::get('products/handle/{slug}',     [\App\Http\Controllers\Api\V1\ProductController::class, 'showByHandle'])->name('products.handle');
        Route::get('products/{product}/related', [\App\Http\Controllers\Api\V1\ProductController::class, 'related'])->name('products.related');
        Route::get('products/{product}/compatible-accessories', [\App\Http\Controllers\Api\V1\ProductController::class, 'compatibleAccessories'])->name('products.compatible-accessories');
        Route::get('products/{product}',         [\App\Http\Controllers\Api\V1\ProductController::class, 'show'])->name('products.show');
        Route::get('products',                   [\App\Http\Controllers\Api\V1\ProductController::class, 'index'])->name('products.index');

        // Collections
        Route::get('collections/count',                  [\App\Http\Controllers\Api\V1\CollectionController::class, 'count'])->name('collections.count');
        Route::get('collections/handle/{slug}',          [\App\Http\Controllers\Api\V1\CollectionController::class, 'showByHandle'])->name('collections.handle');
        Route::get('collections/{collection}/products',  [\App\Http\Controllers\Api\V1\CollectionController::class, 'products'])->name('collections.products');
        Route::get('collections/{collection}',           [\App\Http\Controllers\Api\V1\CollectionController::class, 'show'])->name('collections.show');
        Route::get('collections',                        [\App\Http\Controllers\Api\V1\CollectionController::class, 'index'])->name('collections.index');

        // Wishlist (session-based, works for guests and authenticated customers)
        Route::prefix('wishlist')->as('wishlist.')->group(function () {
            Route::get('/',           [\App\Http\Controllers\Api\V1\WishlistController::class, 'show'])->name('show');
            Route::post('toggle',     [\App\Http\Controllers\Api\V1\WishlistController::class, 'toggle'])->name('toggle');
            Route::delete('/',        [\App\Http\Controllers\Api\V1\WishlistController::class, 'clear'])->name('clear');
        });

        // Cart (optional auth — works for both guests and authenticated customers)
        Route::prefix('cart')->as('cart.')->group(function () {
            Route::get('/',              [\App\Http\Controllers\Api\V1\CartController::class, 'show'])->name('show');
            Route::post('add',           [\App\Http\Controllers\Api\V1\CartController::class, 'add'])->name('add');
            Route::post('update',        [\App\Http\Controllers\Api\V1\CartController::class, 'update'])->name('update');
            Route::post('remove',        [\App\Http\Controllers\Api\V1\CartController::class, 'remove'])->name('remove');
            Route::post('clear',         [\App\Http\Controllers\Api\V1\CartController::class, 'clear'])->name('clear');
            Route::post('apply-discount',[\App\Http\Controllers\Api\V1\CartController::class, 'applyDiscount'])->name('apply-discount');
            Route::get('shipping-rates', [\App\Http\Controllers\Api\V1\CartController::class, 'shippingRates'])->name('shipping-rates');
        });

        // Checkout
        Route::post('checkout/validate', [\App\Http\Controllers\Api\V1\CheckoutController::class, 'validate'])->name('checkout.validate');

        // Payment callbacks (browser return + server webhook)
        Route::get ('payments/myfatoorah/return',  [\App\Http\Controllers\Api\V1\PaymentCallbackController::class, 'myfatoorahReturn'])->name('payments.myfatoorah.return');
        Route::post('payments/myfatoorah/webhook', [\App\Http\Controllers\Api\V1\PaymentCallbackController::class, 'myfatoorahWebhook'])->name('payments.myfatoorah.webhook');

        // Orders
        Route::post('orders', [\App\Http\Controllers\Api\V1\OrderController::class, 'store'])->name('orders.store');
        // Public tracking (guest-friendly) — must be registered before orders/{order}.
        Route::get('orders/track', [\App\Http\Controllers\Api\V1\OrderController::class, 'track'])->name('orders.track');
        Route::middleware('auth:customer')->group(function () {
            Route::get('orders/{order}',         [\App\Http\Controllers\Api\V1\OrderController::class, 'show'])->name('orders.show');
            Route::post('orders/{order}/cancel', [\App\Http\Controllers\Api\V1\OrderController::class, 'cancel'])->name('orders.cancel');
        });

        // Discounts
        Route::post('discount_codes/lookup', [\App\Http\Controllers\Api\V1\DiscountController::class, 'lookup'])->name('discount_codes.lookup');

        // Shipping
        Route::get('shipping/rates',     [\App\Http\Controllers\Api\V1\ShippingController::class, 'rates'])->name('shipping.rates');
        Route::post('shipping/estimate', [\App\Http\Controllers\Api\V1\ShippingController::class, 'estimate'])->name('shipping.estimate');

        // Custom orders (enquiry form from storefront)
        Route::post('custom-orders', [\App\Http\Controllers\Api\V1\CustomOrderController::class, 'store'])->name('custom-orders.store');

        // Newsletter subscription
        Route::post('newsletter/subscribe',   [\App\Http\Controllers\Api\V1\NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
        Route::post('newsletter/unsubscribe', [\App\Http\Controllers\Api\V1\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');

        // Search
        Route::get('search', [\App\Http\Controllers\Api\V1\SearchController::class, 'search'])->name('search');

        // CMS
        Route::get('pages',                    [\App\Http\Controllers\Api\V1\CmsController::class, 'pages'])->name('pages.index');
        Route::get('pages/{handle}',           [\App\Http\Controllers\Api\V1\CmsController::class, 'page'])->name('pages.show');
        Route::get('blogs',                    [\App\Http\Controllers\Api\V1\CmsController::class, 'blogs'])->name('blogs.index');
        Route::get('blogs/{handle}',           [\App\Http\Controllers\Api\V1\CmsController::class, 'blog'])->name('blogs.show');
        Route::get('blogs/{handle}/articles',  [\App\Http\Controllers\Api\V1\CmsController::class, 'articles'])->name('blogs.articles');
        Route::get('articles/handle/{slug}',     [\App\Http\Controllers\Api\V1\CmsController::class, 'articleBySlug'])->name('articles.handle');
        Route::get('articles/{id}',              [\App\Http\Controllers\Api\V1\CmsController::class, 'article'])->name('articles.show');

        // Media Gallery (public read — for Angular image pickers)
        Route::get('media', [\App\Http\Controllers\Api\V1\MediaController::class, 'index'])->name('media.index');

        // Inventory (staff only)
        Route::middleware('auth:sanctum')->prefix('inventory')->as('inventory.')->group(function () {
            Route::get('/',              [\App\Http\Controllers\Api\V1\InventoryController::class, 'index'])->name('index');
            Route::post('adjust',        [\App\Http\Controllers\Api\V1\InventoryController::class, 'adjust'])->name('adjust');
            Route::post('transfer',      [\App\Http\Controllers\Api\V1\InventoryController::class, 'transfer'])->name('transfer');
            Route::get('{item}/history', [\App\Http\Controllers\Api\V1\InventoryController::class, 'history'])->name('history');
        });
    });
