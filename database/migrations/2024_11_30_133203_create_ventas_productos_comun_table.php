<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ventas_productos_comun', function (Blueprint $table) {
            $table->unsignedBigInteger('id_venta');
            $table->string('codigo_producto', 20);
            $table->string('descripcion_producto', 100);
            $table->timestamps();

            // Definir la llave primaria compuesta
            $table->primary(['id_venta', 'codigo_producto']);

            $table->foreign('id_venta')->references('id')->on('ventas')->onDelete('cascade');
            $table->foreign('codigo_producto')->references('codigo')->on('productos')->onDelete('cascade');
        });

        // Insertar 10 productos
        for ($i = 1; $i <= 9; $i++) {
            DB::table('productos')->insert([
                'codigo' => 'COM0' . $i,
                'descripcion' => 'Producto común ' . $i,
                'precio_costo' => 0.00,
                'precio_venta' => 0.00,
                'precio_mayoreo' => 0.00,
                'inventario' => 0,
                'inventario_minimo' => 0,
                'id_departamento' => 1,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_productos_comun');
    }
};
