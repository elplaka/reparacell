<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ventas_credito_detalles', function (Blueprint $table) {
               // Definir la clave externa
            $table->foreign('id')->references('id')->on('ventas_credito')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('ventas_credito_detalles', function (Blueprint $table) {
            // Eliminar la clave externa y la columna asociada
            $table->dropForeign(['id']);
        });
    }
};
