<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    use HasFactory;

    protected  $guarded = [
        'id'
    ];

    protected $hidden = [
      'created_at',
      'updated_at',
      'order_id',
      'product_type_id',
      'id'
    ];

    public function order() {
        return $this->belongsTo(Order::class);
    }

    public function productType() {
        return $this->belongsTo(ProductType::class);
    }
}
