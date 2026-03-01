<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    // protected $guarded = [];
    protected $fillable = ['uuid', 'name', 'phone', 'notes'];
    public function carts(){
        return $this->hasMany(Cart::class);
    }
    public function orders(){
        return $this->hasMany(Order::class);
    }
}
