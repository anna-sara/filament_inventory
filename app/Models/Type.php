<?php

namespace App\Models;
use App\Models\Item;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $fillable = [
        'name'
    ];

   /**
     * Get the items for the type.
     */
    public function item(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
