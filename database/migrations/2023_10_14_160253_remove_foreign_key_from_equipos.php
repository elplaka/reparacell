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
            $table->dropForeign(['telefono_cliente']);
        });
    }

    public function down()
    {
        Schema::table('equipos', function (Blueprint $table) {
            $table->foreign('telefono_cliente')
                ->references('telefono')
                ->on('clientes')
                ->onDelete('restrict');
        });
    }
};
