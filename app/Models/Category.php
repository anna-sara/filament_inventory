<?php

namespace App\Models;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\HasMany;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'type'
    ];

   /**
     * Get the items for the type.
     */
    public function item(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
