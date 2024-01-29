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
        Schema::create('fallas_equipos_taller', function (Blueprint $table) {
            $table->unsignedBigInteger('num_orden');
            $table->unsignedBigInteger('id_falla');
            $table->timestamps();

            $table->foreign('num_orden')
            ->references('num_orden')
            ->on('equipos_taller')
            ->onDelete('restrict'); 

            $table->foreign('id_falla')
            ->references('id')
            ->on('fallas_equipos')
            ->onDelete('restrict'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fallas_equipos_taller');
    }
};
