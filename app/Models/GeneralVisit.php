<?php

namespace App\Models;

use App\Interfaces\Documentable;
use App\Interfaces\Visitation;
use App\Models\Visit as ModelsVisit;
use App\Traits\Documentable as TraitsDocumentable;
use App\Traits\HasVisitData;
use App\Traits\Visit;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralVisit extends Model implements Visitation //, Documentable
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
