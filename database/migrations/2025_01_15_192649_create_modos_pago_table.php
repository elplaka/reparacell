<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateModosPagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('icono', 100);
            $table->timestamps();
        });

        // Insertar registros iniciales con íconos y IDs específicos
        DB::table('modos_pago')->insert([
            ['id' => 0, 'nombre' => 'CRÉDITO', 'icono' => 'fa-solid fa-credit-card'],
            ['id' => 1, 'nombre' => 'EFECTIVO', 'icono' => 'fa-solid fa-coins'],
            ['id' => 2, 'nombre' => 'TRANSFERENCIA', 'icono' => 'fa-solid fa-money-bill-transfer'],
        ]);
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('modos_pago');
    }
}
