<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'wallet_id',
        'to_wallet_id',
        'raw_text',
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
