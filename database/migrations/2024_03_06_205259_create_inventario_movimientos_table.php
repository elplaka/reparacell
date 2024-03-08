<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventario_movimientos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_tipo_movimiento');
            $table->string('codigo_producto', 20);
            $table->integer('existencia_anterior');
            $table->integer('existencia_movimiento');
            $table->integer('existencia_minima_anterior');
            $table->integer('existencia_minima_movimiento');
            $table->decimal('precio_costo_anterior', 8, 2);
            $table->decimal('precio_costo_movimiento', 8, 2);
            $table->decimal('precio_venta_anterior', 8, 2);
            $table->decimal('precio_venta_movimiento', 8, 2);
            $table->decimal('precio_mayoreo_anterior', 8, 2);
            $table->decimal('precio_mayoreo_movimiento', 8, 2);
            $table->timestamps();

            // Definir la clave foránea
            $table->foreign('id_tipo_movimiento')
            ->references('id')
            ->on('inventario_tipos_movimientos')
            ->onDelete('restrict') // Opciones: restrict, cascade, set null, no action
            ->onUpdate('cascade'); // Opciones: cascade, set null, no action

            $table->foreign('codigo_producto')
            ->references('codigo')
            ->on('productos')
            ->onDelete('restrict') // Opciones: restrict, cascade, set null, no action
            ->onUpdate('cascade'); // Opciones: cascade, set null, no action
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
    }
};
