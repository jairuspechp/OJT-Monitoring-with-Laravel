<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    // Composite primary key (board_id, slot_index): Eloquent can't model it,
    // so we read via relations and write with upsert()/query deletes.
    public $incrementing = false;
    protected $primaryKey = null;
    public $timestamps = false;

    protected $fillable = ['board_id', 'slot_index', 'label', 'url'];
}
