<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\KeywordDictionary;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Pengeluaran
            [
                'name' => 'Makanan',
                'type' => 'expense',
                'is_default' => false,
                'keywords' => ['makan', 'nasi', 'warteg', 'warung', 'ayam', 'bakso', 'mie', 'soto', 'indomie', 'rendang', 'restoran']
            ],

            [
                'name' => 'Minuman',
                'type' => 'expense',
                'is_default' => false,
                'keywords' => ['kopi', 'coffee', 'teh', 'minuman', 'jus', 'bubble', 'boba', 'susu', 'es', 'starbucks', 'jahe']
            ],

            [
                'name' => 'Transport',
                'type' => 'expense',
                'is_default' => false,
                'keywords' => ['bensin', 'pertamax', 'solar', 'parkir', 'grab', 'gojek', 'ojek', 'bus', 'kereta', 'toll', 'tol', 'angkot']
            ],

            [
                'name' => 'Hiburan',
                'type' => 'expense',
                'is_default' => false,
                'keywords' => ['game', 'topup', 'netflix', 'spotify', 'bioskop', 'cinema', 'main', 'hiburan', 'steam', 'nonton']
            ],

            [
                'name' => 'Belanja',
                'type' => 'expense',
                'is_default' => false,
                'keywords' => ['beli', 'belanja', 'shopee', 'tokopedia', 'lazada', 'indomaret', 'alfamart', 'minimarket', 'baju', 'sepatu', 'tas']
            ],

            [
                'name' => 'Utilitas',
                'type' => 'expense',
                'is_default' => false,
                'keywords' => ['listrik', 'pln', 'token', 'air', 'pdam', 'internet', 'wifi', 'pulsa', 'data', 'indihome', 'telkom']
            ],

            [
                'name' => 'Kesehatan',
                'type' => 'expense',
                'is_default' => false,
                'keywords' => ['obat', 'dokter', 'apotek', 'klinik', 'vitamin', 'rumah sakit', 'rs', 'puskesmas', 'konsultasi']
            ],

            [
                'name' => 'Pendidikan',
                'type' => 'expense',
                'is_default' => false,
                'keywords' => ['buku', 'kursus', 'les', 'sekolah', 'kuliah', 'biaya', 'pendidikan', 'jurnal', 'spp', 'tuition']
            ],

            [
                'name' => 'Lain-lain',
                'type' => 'expense',
                'is_default' => true,
                'keywords' => []
            ],

            // Pemasukan
            [
                'name' => 'Gaji',
                'type' => 'income',
                'is_default' => false,
                'keywords' => ['gaji', 'salary', 'upah', 'honor', 'honorarium', 'payroll']
            ],

            [
                'name' => 'Freelance',
                'type' => 'income',
                'is_default' => false,
                'keywords' => ['freelance', 'proyek', 'project', 'klien', 'client', 'jasa']
            ],

            [
                'name' => 'Kiriman',
                'type' => 'income',
                'is_default' => false,
                'keywords' => ['kiriman', 'kiriman ibu', 'kiriman bulanan']
            ],

            [
                'name' => 'Pemasukan Lain',
                'type' => 'income',
                'is_default' => false,
                'keywords' => ['cair', 'terima', 'transfer masuk', 'bonus', 'reward', 'cashback', 'refund', 'kembali']
            ],
        ];

        foreach ($categories as $cat) {
            $keywords = $cat['keywords'];
            unset($cat['keywords']);

            $category = Category::create($cat);

            foreach ($keywords as $kw) {
                // keyword global (user_id = null) sebagai seed awal
                KeywordDictionary::firstOrCreate(
                    ['keyword' => $kw, 'user_id' => null],
                    ['category_id' => $category->id]
                );
            }
        }
    }
}
