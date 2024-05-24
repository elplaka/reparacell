<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas_credito_detalles', function (Blueprint $table) {
            // Eliminamos la columna 'restante'
            $table->dropColumn('restante');

            // Añadimos la nueva columna 'id_usuario_venta'
            $table->unsignedBigInteger('id_usuario_venta')->nullable();
            $table->foreign('id_usuario_venta')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas_credito_detalles', function (Blueprint $table) {
            // Revertimos los cambios en caso de rollback
            $table->decimal('restante', 10, 2);
            $table->dropForeign(['id_usuario_venta']);
            $table->dropColumn('id_usuario_venta');
        });
    }
};
