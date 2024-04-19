<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('estatus_ventas_credito', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 15);
            $table->timestamps();
        });

        // Agregar registros por defecto
        DB::table('estatus_ventas_credito')->insert([
            ['descripcion' => 'SIN LIQUIDAR'],
            ['descripcion' => 'LIQUIDADA'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('estatus_ventas_credito');
    }
};
