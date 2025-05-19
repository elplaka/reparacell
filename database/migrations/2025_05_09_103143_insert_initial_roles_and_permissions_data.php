<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insertar roles
        DB::table('roles')->insert([
            ['name' => 'admin', 'guard_name' => 'web'],
            ['name' => 'editor', 'guard_name' => 'web'],
            ['name' => 'author', 'guard_name' => 'web'],
            ['name' => 'subscriber', 'guard_name' => 'web'],
        ]);

        // Insertar permisos (ejemplos)
        DB::table('permissions')->insert([
            ['name' => 'create-post', 'guard_name' => 'web'],
            ['name' => 'edit-post', 'guard_name' => 'web'],
            ['name' => 'delete-post', 'guard_name' => 'web'],
            ['name' => 'manage-users', 'guard_name' => 'web'],
        ]);

        // Insertar relaciones role_has_permissions
        DB::table('role_has_permissions')->insert([
            ['permission_id' => 1, 'role_id' => 1], // admin create-post
            ['permission_id' => 2, 'role_id' => 1], // admin edit-post
            ['permission_id' => 3, 'role_id' => 1], // admin delete-post
            ['permission_id' => 4, 'role_id' => 1], // admin manage-users
            ['permission_id' => 1, 'role_id' => 2], // editor create-post
            ['permission_id' => 2, 'role_id' => 2], // editor edit-post
            ['permission_id' => 1, 'role_id' => 3], // author create-post
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('role_has_permissions')->delete();
        DB::table('roles')->delete();
        DB::table('permissions')->delete();
    }
};