<?php

namespace App\Models;

use App\Enums\AppNotifications;
use App\Enums\Department;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable  = ['product_category_id', 'name', 'description',  'amount', 'is_visible'];

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }

    static public function created($callback)
    {
        static::booted(function (Self $model) {
            if ($model->amount == 0) {
                foreach ([Department::IT->value, Department::PHA->value, Department::DIS->value] as $deptId) {
                    notifyDepartment($deptId, [
                        'title' => 'New Product Created with No Amount',
                        'message' => "A new product has been created with no amount. Product {$model->name}",
                    ], [
                        'mode' => AppNotifications::$BOTH,
                    ]);
                }
            }
        });
    }
}
