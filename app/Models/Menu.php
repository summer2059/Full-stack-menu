<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';
    protected $guarded = [];

    public function menuCategory()
    {
        return $this->belongsTo(MenuCategory::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
