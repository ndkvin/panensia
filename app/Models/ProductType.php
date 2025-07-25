<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;

    protected  $guarded = [
        'id'
    ];

    protected $hidden = [
        'image',
        'created_at',
        'updated_at'
    ];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}
