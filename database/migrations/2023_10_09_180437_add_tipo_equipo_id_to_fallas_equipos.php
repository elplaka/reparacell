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
        Schema::table('fallas_equipos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tipo_equipo')->nullable(); // El campo Foreign Key
            $table->foreign('id_tipo_equipo')
                ->references('id')
                ->on('tipos_equipos')
                ->onDelete('restrict'); // Puedes personalizar la acción onDelete según tus necesidades
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fallas_equipos', function (Blueprint $table) {
            $table->dropForeign(['id_tipo_equipo']);
            $table->dropColumn('id_tipo_equipo');
        });
    }
};
