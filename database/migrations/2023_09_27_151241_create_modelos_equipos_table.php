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
        Schema::create('modelos_equipos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_marca');
            $table->string('nombre', 20);
            $table->boolean('disponible');
            $table->timestamps();

            $table->foreign('id_marca')
            ->references('id')
            ->on('marcas_equipos')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modelos_equipos');
    }
};
