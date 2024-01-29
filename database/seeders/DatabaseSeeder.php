<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Crear roles
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'cash']);
        Role::create(['name' => 'guest']);
    }
}
