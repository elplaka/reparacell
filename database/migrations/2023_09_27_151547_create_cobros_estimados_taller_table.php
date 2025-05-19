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
        Schema::create('cobros_estimados_taller', function (Blueprint $table) {
            $table->unsignedBigInteger('id');
            $table->unsignedBigInteger('num_orden')->index();
            $table->decimal('cobro_estimado', 8, 2);
            // Otras columnas que puedas necesitar
            $table->primary(['id', 'num_orden']);
                    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cobros_estimados_taller');
    }
};
