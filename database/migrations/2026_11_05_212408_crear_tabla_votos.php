<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('reunion_id')
                ->constrained('reuniones')
                ->cascadeOnDelete();

            $table->foreignId('jugador_sala_id')
                ->constrained('jugador_sala')
                ->cascadeOnDelete();

            $table->foreignId('votado_jugador_sala_id')
                ->constrained('jugador_sala')
                ->cascadeOnDelete();

            $table->timestamps();

            // Un jugador solo puede votar una vez por reunión.
            $table->unique(
                ['reunion_id', 'jugador_sala_id'],
                'voto_unico_por_reunion'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votos');
    }
};