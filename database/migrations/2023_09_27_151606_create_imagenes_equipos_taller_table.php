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
        Schema::create('imagenes_equipos_taller', function (Blueprint $table) {
            $table->unsignedBigInteger('num_orden');
            $table->string('nombre_archivo', 15);
            $table->timestamps();

            $table->foreign('num_orden')
            ->references('num_orden')
            ->on('equipos_taller')
            ->onDelete('restrict'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imagenes_equipos_taller');
    }
};
