<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cobros_taller', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario_cobro')->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cobros_taller', function (Blueprint $table) {
            $table->unsignedBigInteger('id_usuario_cobro')->default(14)->change();
        });
    }
};

