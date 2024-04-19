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
        Schema::create('ventas_credito_detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('id_abono')->default(0);
            $table->decimal('abono', 10, 2);
            $table->decimal('restante', 10, 2);
            $table->timestamps();

            // Definimos una clave primaria compuesta con id y id_abono
            $table->primary(['id', 'id_abono']);          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_credito_detalles');
    }
};
