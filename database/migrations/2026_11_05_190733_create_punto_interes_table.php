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
        Schema::create('punto_interes', function (Blueprint $table) {
             $table->id();

        $table->foreignId('lugar_id')
            ->constrained('lugares')
            ->cascadeOnDelete();

        $table->string('nombre')->nullable();

        $table->decimal('x', 8, 4);
        $table->decimal('y', 8, 4);
        $table->decimal('ancho', 8, 4);
        $table->decimal('alto', 8, 4);

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('punto_interes');
    }
};
