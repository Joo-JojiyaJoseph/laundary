<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $guarded = [];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'verified_at' => 'datetime',
        'rating' => 'integer',
    ];

    /** Reviews that are phone-verified and approved — safe to show publicly. */
    public function scopePublic($query)
    {
        return $query->where('is_verified', true)->where('is_approved', true);
    }
}
