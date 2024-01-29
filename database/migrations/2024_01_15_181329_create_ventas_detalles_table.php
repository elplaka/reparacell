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
        Schema::create('ventas_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_venta');
            $table->string('codigo_producto');
            $table->integer('cantidad');
            $table->decimal('importe', 10, 2);
            $table->timestamps();

            $table->foreign('id_venta')->references('id')->on('ventas')->onDelete('cascade');
            $table->foreign('codigo_producto')->references('codigo')->on('productos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
