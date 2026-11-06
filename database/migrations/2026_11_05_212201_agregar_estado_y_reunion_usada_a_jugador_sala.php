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
        Schema::table('jugador_sala', function (Blueprint $table) {
            $table->string('estado')->default('vivo');
            $table->boolean('reunion_usada')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
      public function down(): void
    {
        Schema::table('jugador_sala', function (Blueprint $table) {
            $table->dropColumn(['estado', 'reunion_usada']);
        });
    }
};
