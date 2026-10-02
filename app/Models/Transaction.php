<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    /** Semua tipe yang valid di kolom `type` */
    public const TYPES = ['income', 'expense', 'transfer', 'refund', 'debt', 'repay_debt', 'receivable', 'collect_receivable'];

    /** Tipe yang menambah saldo dompet asal (wallet_id) */
    public const INFLOW_TYPES = ['income', 'refund', 'debt', 'collect_receivable'];

    /** Tipe yang mengurangi saldo dompet asal (wallet_id) */
    public const OUTFLOW_TYPES = ['expense', 'transfer', 'repay_debt', 'receivable'];

    /** Tipe hutang/piutang — tidak dihitung sebagai pemasukan/pengeluaran */
    public const DEBT_TYPES = ['debt', 'repay_debt', 'receivable', 'collect_receivable'];

    public const TYPE_LABELS = [
        'income'             => 'Pemasukan',
        'expense'            => 'Pengeluaran',
        'transfer'           => 'Mutasi',
        'refund'             => 'Refund',
        'debt'               => 'Hutang',
        'repay_debt'         => 'Bayar Hutang',
        'receivable'         => 'Piutang',
        'collect_receivable' => 'Terima Piutang',
    ];

    protected $fillable = [
        'user_id',
        'category_id',
        'wallet_id',
        'to_wallet_id',
        'raw_text',
        'desc_hutang',
        'amount',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'amount'     => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function toWallet()
    {
        return $this->belongsTo(Wallet::class, 'to_wallet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isInflow(): bool
    {
        return in_array($this->type, self::INFLOW_TYPES, true);
    }

    public function isDebt(): bool
    {
        return in_array($this->type, self::DEBT_TYPES, true);
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPE_LABELS[$this->type] ?? ucfirst((string) $this->type);
    }

    /**
     * Scope: filter berdasarkan user (IDOR prevention)
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope: filter bulan dan tahun
     */
    public function scopeForMonth($query, int $month, int $year)
    {
        return $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
    }
}
