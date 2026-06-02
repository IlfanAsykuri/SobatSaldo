<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeywordDictionary extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'keyword',
        'is_quick_habit',
    ];

    protected $casts = [
        'is_quick_habit' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: cari keyword milik user atau keyword global (user_id = null)
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }
}
