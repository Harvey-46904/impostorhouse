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
        Schema::table('jugadors', function (Blueprint $table) {
             $table->string('tagid')->nullable()->unique()->after('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jugadors', function (Blueprint $table) {
             $table->dropUnique(['tagid']);
        $table->dropColumn('tagid');
        });
    }
};
