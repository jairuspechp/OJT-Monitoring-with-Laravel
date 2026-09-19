<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Board extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // created_at is a manual ms timestamp

    protected $fillable = ['id', 'name', 'layout_mode', 'slot_count', 'created_at'];

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class, 'board_id');
    }
}
