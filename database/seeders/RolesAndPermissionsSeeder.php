<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Role::findOrCreate('super_admin');
        Role::findOrCreate('admin');
        Role::findOrCreate('staff');
    }
}

