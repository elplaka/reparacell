<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_modo_pago')->default(1)->after('cancelada');

            // Establecer la clave foránea
            $table->foreign('id_modo_pago')->references('id')->on('modos_pago')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['id_modo_pago']);
            // Luego eliminar la columna
            $table->dropColumn('id_modo_pago');
        });
    }
};
