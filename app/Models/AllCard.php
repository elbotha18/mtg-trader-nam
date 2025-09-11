<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AllCard extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'lang',
        'set',
        'type_line',
        'collector_number',
        'image_url',
    ];

    /**
     * Get the card's full name including set and collector_number.
     */
    public function getFullNameAttribute()
    {
        $fullName = $this->name;
        if ($this->set) {
            $fullName .= ' (' . $this->set . ')';
        }
        if ($this->collector_number) {
            $fullName .= ' ' . $this->collector_number;
        }
        return $fullName;
    }

    public function user_card()
    {
        return $this->hasMany(UserCard::class, 'card_id');
    }

    public function seller()
    {
        return $this->hasMany(UserCard::class, 'card_id')->where('is_private', false)->with('user');
    }
    
}
