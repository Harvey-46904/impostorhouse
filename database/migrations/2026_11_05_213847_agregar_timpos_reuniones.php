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
         Schema::table('reuniones', function (Blueprint $table) {
           
            
            $table->timestamp('iniciada_at')->nullable();
            $table->timestamp('votacion_inicia_at')->nullable();
            $table->timestamp('votacion_finaliza_at')->nullable();
            $table->timestamp('hablar_finaliza_at')->nullable();
            $table->timestamp('finalizada_at')->nullable();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('reuniones', function (Blueprint $table) {
            $table->dropColumn(['iniciada_at', 'votacion_inicia_at', 'votacion_finaliza_at', 'hablar_finaliza_at', 'finalizada_at']);
        });
    }
};
