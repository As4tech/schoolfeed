<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MealRecipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'meal_name', 'item_id', 'quantity_per_student',
    ];

    protected $casts = [
        'quantity_per_student' => 'decimal:4',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
