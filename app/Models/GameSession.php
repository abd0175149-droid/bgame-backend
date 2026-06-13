<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GameSession extends Model
{
    protected $fillable = [
        'user_id', 'place_id', 'mode', 'score',
        'waves_survived', 'resources_collected',
        'duration_seconds', 'status', 'ended_at',
    ];

    protected $casts = [
        'ended_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function place()
    {
        return $this->belongsTo(SavedPlace::class, 'place_id');
    }
}
