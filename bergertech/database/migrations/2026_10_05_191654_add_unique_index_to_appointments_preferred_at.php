<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One booking per slot. Validation already checks availability; the index
     * closes the gap when two visitors submit the same slot at once.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->unique('preferred_at');
        });
    }

    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropUnique(['preferred_at']);
        });
    }
};
