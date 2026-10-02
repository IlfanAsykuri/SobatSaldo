<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Gabungkan kategori dobel (user_id + name + type sama) ke kategori dengan id terkecil.
     * Transaksi & keyword yang menunjuk ke duplikat dipindahkan dulu, baru duplikatnya dihapus.
     */
    public function up(): void
    {
        $groups = DB::table('categories')
            ->select('user_id', 'name', 'type', DB::raw('MIN(id) as keep_id'))
            ->groupBy('user_id', 'name', 'type')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($groups as $group) {
            $duplicateIds = DB::table('categories')
                ->where('user_id', $group->user_id)
                ->where('name', $group->name)
                ->where('type', $group->type)
                ->where('id', '!=', $group->keep_id)
                ->pluck('id');

            DB::transaction(function () use ($group, $duplicateIds) {
                DB::table('transactions')->whereIn('category_id', $duplicateIds)->update(['category_id' => $group->keep_id]);
                DB::table('keyword_dictionaries')->whereIn('category_id', $duplicateIds)->update(['category_id' => $group->keep_id]);
                DB::table('categories')->whereIn('id', $duplicateIds)->delete();
            });
        }
    }

    public function down(): void
    {
        // Data migration — tidak bisa dikembalikan
    }
};
