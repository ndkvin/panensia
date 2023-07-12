<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected  $guarded = [
        'id'
    ];

    public function getImageAttribute($value)
    {
        return $value == null ? null : asset('storage/' . $value);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
