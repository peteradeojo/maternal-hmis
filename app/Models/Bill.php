<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'patient_id',
        'bill_number',
        'bill_date',
        'paid_amount',
        'status',
        'billable_type',
        'billable_id',
        'created_by',
    ];

    public function billable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entries()
    {
        return $this->hasMany(BillDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(BillPayment::class, 'bill_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }


    public function balance(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->entries->sum('amount') - $this->payments->where('status', Status::PAID->value)->sum('amount'),
        );
    }

    public function paid(): Attribute {
        return Attribute::make(
            get: fn () => $this->payments->where('status', Status::PAID->value)->sum('amount'),
        );
    }

    public function amount(): Attribute {
        return Attribute::make(
            get: fn () => $this->entries->sum('amount'),
        );
    }
}
