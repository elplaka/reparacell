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
        Schema::create('estatus_equipos_taller', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 25);
            $table->string('cve_descripcion', 2);
            $table->boolean('disponible');
            $table->timestamps();
        });

        // Insertar los datos iniciales
        DB::table('estatus_equipos_taller')->insert([
            [
                'id' => 1,
                'descripcion' => 'RECIBIDO',
                'cve_descripcion' => 'RC',
                'disponible' => true,
                'created_at' => '2023-09-28 18:37:14',
                'updated_at' => '2023-09-28 18:37:14',
            ],
            [
                'id' => 2,
                'descripcion' => 'EN REPARACIÓN',
                'cve_descripcion' => 'EN',
                'disponible' => true,
                'created_at' => '2023-09-28 18:38:04',
                'updated_at' => '2023-09-28 18:38:04',
            ],
            [
                'id' => 3,
                'descripcion' => 'REPARADO',
                'cve_descripcion' => 'RP',
                'disponible' => true,
                'created_at' => '2023-09-28 18:38:56',
                'updated_at' => '2023-09-28 18:38:56',
            ],
            [
                'id' => 4,
                'descripcion' => 'NO SE PUDO REPARAR',
                'cve_descripcion' => 'NR',
                'disponible' => true,
                'created_at' => '2023-10-17 19:11:17',
                'updated_at' => '2023-10-17 19:11:17',
            ],
            [
                'id' => 5,
                'descripcion' => 'ENTREGADO / REPARADO',
                'cve_descripcion' => 'ET',
                'disponible' => true,
                'created_at' => '2023-09-28 18:39:43',
                'updated_at' => '2023-09-28 18:39:43',
            ],
            [
                'id' => 6,
                'descripcion' => 'ENTREGADO / NO REPARADO',
                'cve_descripcion' => 'EN',
                'disponible' => true,
                'created_at' => '2023-10-17 19:13:59',
                'updated_at' => '2023-10-17 19:13:59',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estatus_equipos_taller');
    }
};
