<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reuniones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('idsala')
                ->constrained('salas')
                ->cascadeOnDelete();

            $table->foreignId('convocada_por')
                ->constrained('jugador_sala')
                ->cascadeOnDelete();

            $table->string('tipo'); 
            // emergencia / ordinaria

            $table->string('estado')->default('cuenta_regresiva');
            // cuenta_regresiva / votacion / hablando / finalizada

            $table->timestamp('iniciada_at')->nullable();
            $table->timestamp('votacion_inicia_at')->nullable();
            $table->timestamp('votacion_finaliza_at')->nullable();
            $table->timestamp('finalizada_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reuniones');
    }
};