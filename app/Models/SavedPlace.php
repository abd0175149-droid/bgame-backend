<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedPlace extends Model
{
    protected $fillable = [
        'user_id', 'name', 'grid_data',
        'area_width', 'area_height', 'is_temporary',
    ];

    protected $casts = [
        'grid_data'    => 'array',
        'is_temporary' => 'boolean',
        'area_width'   => 'float',
        'area_height'  => 'float',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
