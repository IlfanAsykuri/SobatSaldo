<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop global unique constraint, ganti dengan unique per-user
        Schema::table('keyword_dictionaries', function (Blueprint $table) {
            // Drop index lama jika ada
            $table->dropUnique(['keyword']);
            $table->foreignId('user_id')->nullable()->after('id')
                  ->constrained()->nullOnDelete();
            // Unique keyword per-user
            $table->unique(['user_id', 'keyword']);
        });
    }

    public function down(): void
    {
        Schema::table('keyword_dictionaries', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id', 'keyword']);
            $table->dropColumn('user_id');
            $table->unique('keyword');
        });
    }
};
