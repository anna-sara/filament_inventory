<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reserveditem extends Model
{
    public $timestamps = false;
    use SoftDeletes;

    protected $fillable = [
        'username',
        'reserved_date',
        'delivered_date',
        'return_date',
        'returned_date',
        'delivered',
        'returned',
        'user_id',
        'item_id'
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'user_reserveditems')->withTimestamps();
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
