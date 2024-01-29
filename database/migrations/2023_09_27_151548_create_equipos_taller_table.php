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
        Schema::create('equipos_taller', function (Blueprint $table) {
            $table->bigIncrements('num_orden');
            $table->unsignedBigInteger('id_equipo');
            $table->unsignedBigInteger('id_usuario_recibio');
            $table->unsignedBigInteger('id_estatus');
            $table->string('observaciones', 50);
            
            $table->timestamp('fecha_entrada')->useCurrent();
            $table->timestamp('fecha_actualizacion')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->foreign('id_equipo')
            ->references('id')
            ->on('equipos')
            ->onDelete('restrict'); 

            $table->foreign('id_usuario_recibio')
                ->references('id')
                ->on('users')
                ->onDelete('restrict'); 
    
            $table->foreign('id_estatus')
                ->references('id')
                ->on('estatus_equipos_taller')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipos_taller');
    }
};
