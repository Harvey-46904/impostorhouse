<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reuniones', function (Blueprint $table) {
            $table->unsignedBigInteger('expulsado_jugador_sala_id')
                ->nullable();

            $table->string('resultado')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reuniones', function (Blueprint $table) {
            $table->dropColumn([
                'expulsado_jugador_sala_id',
                'resultado'
            ]);
        });
    }
};