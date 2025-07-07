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
        'number'
    ];

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

    public function user_card()
    {
        return $this->hasMany(UserCard::class);
    }

    public function seller()
    {
        return $this->hasMany(UserCard::class)->where('is_private', false)->with('user');
    }
}
