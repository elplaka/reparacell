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
        Schema::create('productos', function (Blueprint $table) {
            $table->string('codigo', 20)->primary();
            $table->string('descripcion', 60);
            $table->decimal('precio_costo', 7, 2);  // 10 dígitos en total, 2 dígitos después del punto decimal
            $table->decimal('precio_venta', 7, 2);
            $table->decimal('precio_mayoreo', 7, 2);
            $table->integer('inventario');
            $table->integer('inventario_minimo');
            $table->unsignedBigInteger('id_departamento');

            $table->foreign('id_departamento')
            ->references('id')
            ->on('departamentos_productos')
            ->onDelete('restrict'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
