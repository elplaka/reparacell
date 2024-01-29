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
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_marca');
            $table->unsignedBigInteger('id_modelo');
            $table->timestamps();

            $table->foreign('id_cliente')
            ->references('id')
            ->on('clientes')
            ->onDelete('restrict');

            $table->foreign('id_marca')
            ->references('id')
            ->on('marcas_equipos')
            ->onDelete('restrict');

            $table->foreign('id_modelo')
            ->references('id')
            ->on('modelos_equipos')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
