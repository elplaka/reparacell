<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Para usar DB::table

class CreateTiposMovimientoCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Crear la tabla
        Schema::create('tipos_movimiento_caja', function (Blueprint $table) {
            $table->id(); // id autoincremental
            $table->string('nombre', 25); // nombre del tipo de movimiento
            $table->string('inicial', 1); // inicial del tipo de movimiento
            $table->timestamps(); // created_at y updated_at
        });

        // Insertar los registros iniciales
        DB::table('tipos_movimiento_caja')->insert([
            ['nombre' => 'VENTA (AL CONTADO)', 'inicial' => 'V'],
            ['nombre' => 'REPARACIÓN (AL CONTADO)', 'inicial' => 'R'],
            ['nombre' => 'ABONO', 'inicial' => 'A'],
            ['nombre' => 'INICIALIZACIÓN', 'inicial' => 'I'],
            ['nombre' => 'ENTRADA MANUAL', 'inicial' => 'E'],
            ['nombre' => 'SALIDA MANUAL', 'inicial' => 'S'],
            ['nombre' => 'BORRADO DE ABONO', 'inicial' => 'B'],
            ['nombre' => 'CAMBIO A TRANSFERENCIA', 'inicial' => 'T'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar la tabla si se revierte la migración
        Schema::dropIfExists('tipos_movimiento_caja');
    }
}