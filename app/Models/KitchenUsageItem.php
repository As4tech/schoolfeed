<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenUsageItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'usage_id', 'item_id', 'quantity',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function usage(): BelongsTo
    {
        return $this->belongsTo(KitchenUsage::class, 'usage_id');
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}
