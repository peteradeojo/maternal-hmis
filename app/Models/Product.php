<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable  = ['product_category_id', 'name', 'description',  'amount', 'is_visible'];

    public function category() {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
