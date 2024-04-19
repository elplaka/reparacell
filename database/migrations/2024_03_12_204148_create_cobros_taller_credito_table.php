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
        Schema::create('cobros_taller_credito', function (Blueprint $table) {
            $table->unsignedBigInteger('num_orden')->primary();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_estatus');
            $table->timestamps();

            $table->foreign('num_orden')
            ->references('num_orden')
            ->on('cobros_estimados_taller')
            ->onDelete('cascade');

            $table->foreign('id_cliente')
            ->references('id')
            ->on('clientes')
            ->onDelete('cascade');

            $table->foreign('id_estatus')
            ->references('id')
            ->on('estatus_cobros_taller_credito')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobros_taller_credito');
    }
};
