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
        Schema::create('estatus_cobros_taller_credito', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 15);
            $table->timestamps();
        });

          // Agregar registros por defecto
          DB::table('estatus_cobros_taller_credito')->insert([
            ['descripcion' => 'SIN LIQUIDAR'],
            ['descripcion' => 'LIQUIDADO'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estatus_cobros_taller_credito');
    }
};
