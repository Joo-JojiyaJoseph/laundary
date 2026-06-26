<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'feedback';

    protected $guarded = [];

    protected $casts = [
<<<<<<< HEAD
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'rating' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** Only approved feedback (used on the public site). */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopePending($query)
    {
        return $query->where('is_approved', false);
=======
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'verified_at' => 'datetime',
        'rating' => 'integer',
    ];

    /** Reviews that are phone-verified and approved — safe to show publicly. */
    public function scopePublic($query)
    {
        return $query->where('is_verified', true)->where('is_approved', true);
>>>>>>> c0aaad51798596a0c0b373416eb6c774bfc4be3d
    }
}
