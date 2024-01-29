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
        Schema::dropIfExists('clientes');
    }

    public function down()
    {
        // Puedes dejar el método "down" vacío ya que no necesitas volver a crear la tabla
    }
};
