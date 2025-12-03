<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OutPatient extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'country',
        'activity_id',
        'activity_type',
        'deleted_at',
        'created_at',
        'updated_at',
    ];

    public function activity()
    {
        return $this->morphTo();
    }
}
