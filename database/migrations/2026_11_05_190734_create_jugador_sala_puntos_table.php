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
        Schema::create('jugador_sala_puntos', function (Blueprint $table) {
            $table->id();

        $table->foreignId('jugador_sala_id')
            ->constrained('jugador_sala')
            ->cascadeOnDelete();

        $table->foreignId('punto_interes_id')
    ->constrained('punto_interes')
    ->cascadeOnDelete();

        $table->timestamps();

        $table->unique([
            'jugador_sala_id',
            'punto_interes_id'
        ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jugador_sala_puntos');
    }
};
