<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\KeywordDictionary;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Wallet $bca;
    private Wallet $cash;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->bca  = Wallet::create(['user_id' => $this->user->id, 'name' => 'BCA',  'type' => 'bank', 'color_theme' => 'blue']);
        $this->cash = Wallet::create(['user_id' => $this->user->id, 'name' => 'Cash', 'type' => 'cash', 'color_theme' => 'emerald']);

        Category::create(['user_id' => $this->user->id, 'name' => 'Lain-lain', 'type' => 'expense', 'is_default' => true]);
        foreach (['Transport' => 'bensin', 'Hiburan' => 'game'] as $name => $keyword) {
            $category = Category::create(['user_id' => $this->user->id, 'name' => $name, 'type' => 'expense', 'is_default' => false]);
            KeywordDictionary::create(['user_id' => $this->user->id, 'category_id' => $category->id, 'keyword' => $keyword]);
        }
    }

    private function storeHutang(string $type, int $amount, ?int $walletId = null)
    {
        return $this->actingAs($this->user)->postJson('/transactions', [
            'inputMode'   => 'hutang',
            'type'        => $type,
            'amount'      => $amount,
            'desc_hutang' => 'Budi',
            'wallet_id'   => $walletId ?? $this->bca->id,
        ]);
    }

    public function test_all_four_debt_types_are_accepted_and_stored_with_their_own_type(): void
    {
        foreach (Transaction::DEBT_TYPES as $type) {
            $this->storeHutang($type, 1000)->assertOk()->assertJson(['success' => true]);
            $this->assertDatabaseHas('transactions', ['type' => $type, 'desc_hutang' => 'Budi']);
        }
    }

    public function test_debts_move_wallet_balance_but_are_not_counted_as_income_or_expense(): void
    {
        $this->storeHutang('debt', 500_000);         // pinjam: +500rb
        $this->storeHutang('repay_debt', 200_000);   // bayar:  -200rb
        $this->storeHutang('receivable', 100_000);   // pinjamkan: -100rb
        $this->storeHutang('collect_receivable', 40_000); // tagih: +40rb

        $this->assertSame(240_000.0, $this->bca->fresh()->balance);

        $response = $this->actingAs($this->user)->get('/workspace')->assertOk();
        $this->assertEquals(0, $response->viewData('totalIncome'));
        $this->assertEquals(0, $response->viewData('totalExpense'));

        $this->actingAs($this->user)->get('/wallet')->assertOk();
    }

    public function test_cannot_use_another_users_wallet(): void
    {
        $other       = User::factory()->create();
        $otherWallet = Wallet::create(['user_id' => $other->id, 'name' => 'Lain', 'type' => 'bank', 'color_theme' => 'blue']);

        $this->actingAs($this->user)->postJson('/transactions', [
            'inputMode'    => 'mutasi',
            'amount'       => 100_000,
            'wallet_id'    => $this->bca->id,
            'to_wallet_id' => $otherWallet->id,
        ])->assertStatus(422)->assertJsonValidationErrors('to_wallet_id');

        $this->storeHutang('debt', 1000, $otherWallet->id)
            ->assertStatus(422)->assertJsonValidationErrors('wallet_id');

        $this->assertSame(0, Transaction::count());
    }

    public function test_category_keywords_win_over_transfer_keywords(): void
    {
        $cases = [
            'isi bensin 20k'  => ['expense', 'Transport'],
            'topup game 50k'  => ['expense', 'Hiburan'],
            'posisi parkir 5k' => ['expense', 'Lain-lain'], // "isi" di dalam "posisi" bukan transfer
            'tarik bca 100k'  => ['transfer', null],
        ];

        foreach ($cases as $text => [$type, $category]) {
            $this->actingAs($this->user)->postJson('/transactions', ['raw_text' => $text])->assertOk();

            $trx = Transaction::with('category')->latest('id')->first();
            $this->assertSame($type, $trx->type, $text);
            $this->assertSame($category, $trx->category?->name, $text);
        }

        $transfer = Transaction::where('type', 'transfer')->first();
        $this->assertSame($this->bca->id, $transfer->wallet_id);
        $this->assertSame($this->cash->id, $transfer->to_wallet_id);
    }

    public function test_edit_form_redirects_back_instead_of_returning_json(): void
    {
        $trx = Transaction::create(['user_id' => $this->user->id, 'raw_text' => 'makan', 'amount' => 10_000, 'type' => 'expense']);
        $category = Category::where('name', 'Transport')->first();

        $this->actingAs($this->user)->from('/workspace')->put("/transactions/{$trx->id}", [
            'raw_text'    => 'bensin',
            'amount'      => 20_000,
            'type'        => 'expense',
            'category_id' => $category->id,
            'wallet_id'   => $this->cash->id,
        ])->assertRedirect('/workspace')->assertSessionHas('success');

        $this->assertSame('bensin', $trx->fresh()->raw_text);
    }

    public function test_edit_validation_errors_are_shown_not_turned_into_500(): void
    {
        $trx = Transaction::create(['user_id' => $this->user->id, 'raw_text' => 'makan', 'amount' => 10_000, 'type' => 'expense']);

        $this->actingAs($this->user)->from('/workspace')->put("/transactions/{$trx->id}", [
            'raw_text' => 'makan',
            'amount'   => 10_000,
            'type'     => 'expense',
        ])->assertRedirect('/workspace')->assertSessionHasErrors('category_id');
    }

    public function test_cannot_assign_another_users_category(): void
    {
        $trx           = Transaction::create(['user_id' => $this->user->id, 'raw_text' => 'makan', 'amount' => 10_000, 'type' => 'expense']);
        $other         = User::factory()->create();
        $otherCategory = Category::create(['user_id' => $other->id, 'name' => 'Rahasia', 'type' => 'expense', 'is_default' => false]);

        $this->actingAs($this->user)->patchJson("/transactions/{$trx->id}/category", ['category_id' => $otherCategory->id])
            ->assertStatus(422);

        $this->actingAs($this->user)->putJson("/transactions/{$trx->id}", [
            'raw_text' => 'makan', 'amount' => 10_000, 'type' => 'expense', 'category_id' => $otherCategory->id,
        ])->assertStatus(422);

        $this->assertNull($trx->fresh()->category_id);
    }
}
