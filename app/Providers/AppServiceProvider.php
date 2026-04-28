<?php

namespace App\Providers;

use App\Models\InventoryLevel;
use App\Observers\InventoryLevelObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        InventoryLevel::observe(InventoryLevelObserver::class);
    }
}
