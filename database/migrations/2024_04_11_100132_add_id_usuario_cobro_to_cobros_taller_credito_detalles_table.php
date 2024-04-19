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
        Schema::table('cobros_taller_credito_detalles', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario_cobro')->nullable();
            $table->foreign('id_usuario_cobro')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cobros_taller_credito_detalles', function (Blueprint $table) {
            $table->dropForeign(['id_usuario_cobro']);
            $table->dropColumn('id_usuario_cobro');
        });
    }
};
