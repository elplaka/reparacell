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
        Schema::table('equipos', function (Blueprint $table) {
            $table->unsignedBigInteger('id_tipo')->nullable(); // El campo Foreign Key
            $table->foreign('id_tipo')
                ->references('id')
                ->on('tipos_equipos')
                ->onDelete('restrict'); // Puedes personalizar la acción onDelete según tus necesidades
        });
    }

    public function down()
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->dropForeign(['id_tipo']);
            $table->dropColumn('id_tipo');
        });
    }
};
