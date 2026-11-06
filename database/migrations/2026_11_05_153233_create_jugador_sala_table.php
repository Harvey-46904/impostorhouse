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
        Schema::create('jugador_sala', function (Blueprint $table) {
           $table->id();
        $table->unsignedBigInteger('idjugador');
        $table->unsignedBigInteger('idsala');
        $table->timestamps();

        $table->foreign('idjugador')->references('id')->on('jugadors')->onDelete('cascade');
        $table->foreign('idsala')->references('id')->on('salas')->onDelete('cascade');

        $table->unique(['idjugador', 'idsala']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jugador_sala');
    }
};
