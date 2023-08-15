<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected  $guarded = [
        'id'
    ];

    public function scopeSearch($query, $request) {
        if($request->input('category')) {
            $query = $query->where('category_id', $request->input('category'));
        };

        if($request->input('name')) {
            $query = $query->where('name', 'like', '%' . $request->input('name') . '%');
        };

        return $query;
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function types() {
      return $this->hasMany(ProductType::class);
    }

    public function orders() {
        return $this->belongsToMany(Order::class, 'order_products', 'product_id', 'order_id');
    }
}
