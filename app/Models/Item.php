<?php

namespace App\Models;
use App\Models\User;
use App\Models\Category;
use App\Models\Reserveditem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'desc',
        'image',
        'acquisition_date',
        'quantity',
        'cost',
        'can_be_loaned',
        'category_id',
        'players',
        'play_time',
        'age',
        'type'
    ];


    /**
     * Get the type of the item
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reserveditem()
    {
        return $this->hasOne(Reserveditem::class);
    }
}
