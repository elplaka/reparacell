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
        Schema::create('anotaciones_equipos_taller', function (Blueprint $table) {
            $table->unsignedBigInteger('num_orden')->primary(); // Clave primaria
            $table->text('contenido');
            $table->timestamps();
        
            // Ejemplo de relación con un equipo, ajusta según tus necesidades
            $table->foreign('num_orden')->references('num_orden')->on('equipos_taller')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anotaciones_equipos_taller');
    }
};
