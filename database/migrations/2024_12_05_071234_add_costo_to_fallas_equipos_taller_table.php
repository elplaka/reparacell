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
        Schema::table('fallas_equipos_taller', function (Blueprint $table) {
            $table->integer('costo')->after('id_falla')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fallas_equipos_taller', function (Blueprint $table) {
            $table->dropColumn('costo');
        });
    }
};