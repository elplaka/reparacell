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
        Schema::create('ventas_credito', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_estatus');
            $table->timestamps();

            $table->foreign('id')
            ->references('id')
            ->on('ventas')
            ->onDelete('cascade');

            $table->foreign('id_estatus')
            ->references('id')
            ->on('estatus_ventas_credito')
            ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_credito');
    }
};
