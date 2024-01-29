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
        Schema::create('cobros_taller', function (Blueprint $table) {
            $table->unsignedBigInteger('num_orden');
            $table->date('fecha');
            $table->decimal('cobro_estimado', 8, 2);
            $table->decimal('cobro_realizado', 8, 2);
            $table->timestamps();

            $table->primary('num_orden');

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
        Schema::dropIfExists('cobros_taller');
    }
};
