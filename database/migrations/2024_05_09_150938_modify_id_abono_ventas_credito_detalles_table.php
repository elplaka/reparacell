<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     *
     */
    public function up() : void
    {
        Schema::table('ventas_credito_detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('id_abono')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down() : void
    {
        Schema::table('ventas_credito_detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('id_abono')->change();
        });
    }
};
