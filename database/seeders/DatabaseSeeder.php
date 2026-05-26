<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Roles & admin user (no dependencies)
            RolesAndPermissionsSeeder::class,
            AdminUserSeeder::class,

            // 2. Store configuration
            StoreSettingSeeder::class,

            // 3. Catalog foundation
            ProductTypeSeeder::class,
            CollectionSeeder::class,
            ProductSeeder::class,       // depends on ProductType + Collection

            // 4. Customers
            CustomerSeeder::class,

            // 5. Inventory (depends on Products → variants exist)
            InventorySeeder::class,

            // 6. Orders (depends on Products + Customers)
            OrderSeeder::class,

            // 7. Shipping
            ShippingSeeder::class,

            // 8. Discounts
            DiscountSeeder::class,

            // 9. Tax rates & settings
            TaxSeeder::class,

            // 10. CMS content (no dependencies)
            PageSeeder::class,
            BlogSeeder::class,

            // 11. Home page sections & settings
            HomePageSeeder::class,
        ]);
    }
}
