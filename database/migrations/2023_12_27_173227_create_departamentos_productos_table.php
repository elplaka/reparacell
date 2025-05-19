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
        Schema::create('departamentos_productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 30);
            $table->boolean('disponible');
        });

            DB::table('departamentos_productos')->insert([
            ['nombre' => 'Electrónica', 'disponible' => true],
            ['nombre' => 'Ropa', 'disponible' => true],
            ['nombre' => 'Hogar', 'disponible' => false],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departamentos_productos');
    }
};
