<?php

namespace App\Models;

use App\Enums\Department as DepartmentEnum;
use App\Notifications\StaffNotification;
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
                Department::find(
                    DepartmentEnum::IT->value,
                    DepartmentEnum::PHA->value,
                    DepartmentEnum::DIS->value
                )->notifyParticipants(new StaffNotification("A new product has been created with no amount. Product id {$model->id}"));
            }
        });
    }
}
