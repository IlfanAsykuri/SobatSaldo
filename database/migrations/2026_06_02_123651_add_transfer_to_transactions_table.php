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
        Schema::table('transactions', function (Blueprint $table) {
            // Untuk fitur mutasi/transfer antar dompet
            $table->unsignedBigInteger('to_wallet_id')->nullable()->after('wallet_id');
            $table->foreign('to_wallet_id')->references('id')->on('wallets')->nullOnDelete();

            // Untuk fitur hutang/piutang
            $table->string('desc_hutang')->nullable()->after('raw_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            //
        });
    }
};
