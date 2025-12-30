<?php

namespace App\Models;

use App\Enums\NoteCodes;
use App\Interfaces\PatientRecord;
use App\Traits\NeedsRecorderInfo;
use Illuminate\Database\Eloquent\Casts\AsEnumArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationNote extends Model implements PatientRecord
{
    use HasFactory, NeedsRecorderInfo;

    protected $fillable =  [
        'patient_id',
        'visit_id',
        'consultant_id',
        'note',
        'code',
    ];

    protected $with = ['consultant', 'recorder'];

    protected $casts = [
        'code' => NoteCodes::class,
    ];

    public function visit()
    {
        return $this->morphTo();
    }

    public function consultant()
    {
        return  $this->belongsTo(User::class, 'consultant_id');
    }
}
