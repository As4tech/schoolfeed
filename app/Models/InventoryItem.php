<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'name', 'unit', 'quantity', 'min_level',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'min_level' => 'decimal:3',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function stockIns(): HasMany
    {
        return $this->hasMany(StockIn::class, 'item_id');
    }

    public function stockOuts(): HasMany
    {
        return $this->hasMany(StockOut::class, 'item_id');
    }

    public function usageItems(): HasMany
    {
        return $this->hasMany(KitchenUsageItem::class, 'item_id');
    }
}
