<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah tipe pelunasan hutang/piutang, lalu perbaiki catatan hutang lama
     * yang sebelumnya tersimpan sebagai income/expense.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('type', [
                'income', 'expense', 'transfer', 'refund',
                'debt', 'repay_debt', 'receivable', 'collect_receivable',
            ])->change();
        });

        // Dulu "Saya Berhutang" disimpan sebagai income, "Beri Pinjaman" sebagai expense
        DB::table('transactions')->whereNotNull('desc_hutang')->where('type', 'income')->update(['type' => 'debt']);
        DB::table('transactions')->whereNotNull('desc_hutang')->where('type', 'expense')->update(['type' => 'receivable']);
    }

    public function down(): void
    {
        DB::table('transactions')->whereIn('type', ['debt', 'collect_receivable'])->update(['type' => 'income']);
        DB::table('transactions')->whereIn('type', ['receivable', 'repay_debt'])->update(['type' => 'expense']);

        Schema::table('transactions', function (Blueprint $table) {
            $table->enum('type', ['income', 'expense', 'transfer', 'debt', 'receivable', 'refund'])->change();
        });
    }
};
