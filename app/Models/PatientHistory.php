<?php

namespace App\Models;

use App\Interfaces\PatientRecord;
use App\Traits\NeedsRecorderInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $presentation
 * @property string $duration
 */
class PatientHistory extends Model implements PatientRecord
{
    use HasFactory, NeedsRecorderInfo;

    protected $guarded = null;

    protected $with = ['recorder'];

    public function visit()
    {
        return $this->morphTo();
    }
}
