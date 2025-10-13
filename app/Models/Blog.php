<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'slug',
        'description',
        'username',
        'status',
    ];

    public function images(){
        return $this->hasMany(BlogImage::class);
    }
}
