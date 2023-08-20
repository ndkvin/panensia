<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $guarded = [
        'id'
    ];

    public function scopeSearch($query, $request) {
        if($request->input('status')) {
            $query = $query->where('status', $request->input('status'));
        };

        if($request->input('invoice')) {
            $query = $query->where('invoice', 'like', '%' . $request->input('invoice') . '%');
        };

        return $query;
    }

    public function orderProducts() {
        return $this->hasMany(OrderProduct::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function payment() {
        return $this->hasOne(Payment::class);
    }

    public function shipment() {
        return $this->hasOne(Shipment::class);
    }

    public function address() {
        return $this->hasOne(OrderAddress::class);
    }
}
