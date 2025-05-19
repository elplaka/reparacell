<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos_caja', function (Blueprint $table) {
            // Campo folio (string de longitud 4, no es clave primaria)
            $table->string('referencia', 5); // No es clave primaria y puede repetirse

            // Campo fecha (timestamps, pero solo uno)
            $table->timestamp('fecha')->useCurrent(); // Fecha de creación

            // Definir la clave primaria compuesta
            $table->primary(['referencia', 'fecha']);

            // Campo id_tipo_movimiento (clave foránea)
            $table->unsignedBigInteger('id_tipo');
            $table->foreign('id_tipo')
                ->references('id')
                ->on('tipos_movimiento_caja')
                ->onDelete('cascade'); // Eliminación en cascada

            // Campo monto (tipo money)
            $table->decimal('monto', 10, 2); // 10 dígitos en total, 2 decimales
            $table->decimal('saldo_caja', 10, 2); // 10 dígitos en total, 2 decimales

            // Campo id_usuario (clave foránea)
            $table->unsignedBigInteger('id_usuario');
            $table->foreign('id_usuario')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); // Eliminación en cascada
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Eliminar la tabla si se revierte la migración
        Schema::dropIfExists('movimientos_caja');
    }
}