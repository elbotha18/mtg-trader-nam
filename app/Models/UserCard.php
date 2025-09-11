<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class UserCard extends Model
{
    use SoftDeletes, Loggable;

    protected $fillable = [
        'user_id',
        'card_id',
        'is_private',
        'is_foil',
        'is_borderless',
        'is_retro_frame',
        'is_etched_foil',
        'is_judge_promo_foil',
        'is_japanese_language',
        'is_signed_by_artist',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function card()
    {
        return $this->belongsTo(AllCard::class, 'card_id');
    }

    /**
     * Scope a query to only include public cards.
     */
    public function scopePublic($query)
    {
        return $query->where('is_private', false);
    }

    /**
     * Scope a query to only include private cards.
     */
    public function scopePrivate($query)
    {
        return $query->where('is_private', true);
    }

    /**
     * Scope a query to only include foil cards.
     */
    public function scopeFoil($query)
    {
        return $query->where('is_foil', true);
    }
}
