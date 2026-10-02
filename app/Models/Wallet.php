<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'color_theme',
        'account_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Saldo dihitung dinamis: semua uang masuk - semua uang keluar
     * (lihat Transaction::INFLOW_TYPES / OUTFLOW_TYPES)
     */
    public function getBalanceAttribute(): float
    {
        $inflow     = $this->transactions()->whereIn('type', Transaction::INFLOW_TYPES)->sum('amount');
        $outflow    = $this->transactions()->whereIn('type', Transaction::OUTFLOW_TYPES)->sum('amount');
        $transferIn = Transaction::where('to_wallet_id', $this->id)
            ->where('user_id', $this->user_id)
            ->where('type', 'transfer')
            ->sum('amount');

        return (float) ($inflow - $outflow + $transferIn);
    }

    /**
     * Mapping color_theme ke kelas Tailwind gradient
     */
    public function getGradientAttribute(): string
    {
        return match ($this->color_theme) {
            'blue'    => 'from-blue-500 via-blue-600 to-indigo-600',
            'amber'   => 'from-yellow-600 via-amber-600 to-orange-500',
            'rose'    => 'from-rose-500 via-rose-600 to-pink-600',
            'violet'  => 'from-violet-500 via-violet-600 to-purple-600',
            'slate'   => 'from-slate-600 via-slate-700 to-slate-800',
            default   => 'from-emerald-500 via-emerald-600 to-teal-600', // emerald = default
        };
    }

    public function getShadowColorAttribute(): string
    {
        return match ($this->color_theme) {
            'blue'   => 'shadow-blue-300/40 dark:shadow-blue-900/40',
            'amber'  => 'shadow-amber-300/40 dark:shadow-amber-900/40',
            'rose'   => 'shadow-rose-300/40 dark:shadow-rose-900/40',
            'violet' => 'shadow-violet-300/40 dark:shadow-violet-900/40',
            'slate'  => 'shadow-slate-400/40 dark:shadow-slate-900/40',
            default  => 'shadow-emerald-300/40 dark:shadow-emerald-900/40',
        };
    }

    public function getIconAttribute(): string
    {
        return match ($this->type) {
            'bank'    => '🏦',
            'ewallet' => '📱',
            default   => '💵',
        };
    }
}
