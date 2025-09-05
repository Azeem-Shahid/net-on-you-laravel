<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MagazineEntitlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'magazine_id',
        'granted_at',
        'reason',
    ];

    protected $casts = [
        'granted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function magazine()
    {
        return $this->belongsTo(Magazine::class);
    }
}

