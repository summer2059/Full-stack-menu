<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{
    protected $guarded = [];

    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItems::class, 'inventory_item_id'); // ← explicit FK
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
