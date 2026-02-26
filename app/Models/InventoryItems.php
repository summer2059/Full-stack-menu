<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItems extends Model
{
    protected $guarded = [];

    public function menus()
    {
        return $this->belongsToMany(Menu::class, 'menu_inventory')
                    ->withPivot('quantity_required');
    }

    public function logs()
    {
        return $this->hasMany(InventoryLog::class, 'inventory_item_id'); // ← explicit FK
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }
}
