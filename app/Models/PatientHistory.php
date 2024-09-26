<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $presentation
 * @property string $duration
 */
class PatientHistory extends Model
{
    use HasFactory;

    protected $guarded = null;

    public function visit()
    {
        return $this->morphTo();
    }
}
