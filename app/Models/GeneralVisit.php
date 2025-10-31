<?php

namespace App\Models;

use App\Interfaces\OperationalEvent;
use App\Interfaces\Visitation;
use App\Traits\Documentable as TraitsDocumentable;
use App\Traits\HasVisitData;
use App\Traits\Visit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralVisit extends Model implements Visitation, OperationalEvent //, Documentable
{
    use HasFactory, Visit, TraitsDocumentable, HasVisitData;

    protected $fillable = [
        'patient_id',
    ];

    protected $appends = [
        'type',
    ];

    public function getType(): string
    {
        return "General";
    }
}
