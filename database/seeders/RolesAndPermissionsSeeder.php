<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Crear el permiso para acceder a la configuración de usuarios
        Permission::firstOrCreate(['name' => 'configurar-usuarios']);

        // Asignar el permiso al rol "admin"
        $adminRole = Role::where('name', 'admin')->first();
        $adminRole->givePermissionTo('configurar-usuarios');

        Role::findByName('admin')->givePermissionTo('configurar-usuarios');
    }
}
