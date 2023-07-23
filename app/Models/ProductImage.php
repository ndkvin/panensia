<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;

    protected  $guarded = [
        'id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getImageAttribute($value)
    {
        return $value == null ? null : asset('storage/' . $value);
    }

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
