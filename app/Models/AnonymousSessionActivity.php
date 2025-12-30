<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnonymousSessionActivity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'model_type',
        'model_id',
        'session_id',
        'recorder'
    ];

    public function model() {
        return $this->morphTo();
    }
}
