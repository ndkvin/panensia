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

    public function scopeName($query, $request) {
        return $request->input('name') ? $query->where('name', 'like', '%' . $request->input('name') . '%') : $query;
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
}
