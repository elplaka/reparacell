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
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario_movimiento')->nullable();
            $table->foreign('id_usuario_movimiento')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario_movimientos', function (Blueprint $table) {
            $table->dropForeign(['id_usuario_movimiento']);
            $table->dropColumn('id_usuario_movimiento');
        });
    }
};
