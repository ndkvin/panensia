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

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getImageAttribute($value)
    {
        return $value == null ? null : asset('storage/' . $value);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeSearch($query, $request) {
        if($request->input('name')) {
            $query = $query->where('name', 'like', '%' . $request->input('name') . '%');
        };

        return $query;
    }
}
