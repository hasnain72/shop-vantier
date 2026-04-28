<?php

namespace Database\Seeders;

use App\Models\ProductType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'Watches',
            'Watch Straps',
            'Watch Bands',
            'Watch Cases',
            'Watch Tools',
            'Watch Boxes',
            'Watch Winders',
            'Accessories',
        ];

        foreach ($types as $name) {
            ProductType::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name), 'is_active' => true]
            );
        }
    }
}
