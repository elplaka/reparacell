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
        Schema::create('cobros_taller_credito_detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('num_orden');
            $table->unsignedBigInteger('id_abono')->default(0);
            $table->decimal('abono', 10, 2);
            $table->timestamps();

            // Definimos una clave primaria compuesta con id y id_abono
            $table->primary(['num_orden', 'id_abono']); 
            
            $table->foreign('num_orden')
            ->references('num_orden')
            ->on('cobros_taller_credito')
            ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobros_taller_credito_detalles');
    }
};
