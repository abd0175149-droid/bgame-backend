<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leaderboard extends Model
{
    protected $table = 'leaderboard';

    protected $fillable = [
        'user_id', 'season', 'rank_points',
        'wins', 'losses', 'rank_tier',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
