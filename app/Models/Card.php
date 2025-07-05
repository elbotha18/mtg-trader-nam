<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Card extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'set',
        'number',
        'is_foil',
        'is_borderless',
        'is_retro_frame',
        'is_etched_foil',
        'is_judge_promo_foil',
        'is_japanese_language',
        'is_signed_by_artist',
        'is_private'
    ];

    /**
     * The user that owns the card.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the card's full name including set and number.
     */
    public function getFullNameAttribute()
    {
        $fullName = $this->name;
        if ($this->set) {
            $fullName .= ' (' . $this->set . ')';
        }
        if ($this->number) {
            $fullName .= ' ' . $this->number;
        }
        return $fullName;
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
