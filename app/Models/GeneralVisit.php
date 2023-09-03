<?php

namespace App\Models;

use App\Interfaces\Visitation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralVisit extends Model implements Visitation
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
    ];

    public function lab()
    {
    }

    public function pharmacy()
    {
    }

    public function visit()
    {
        return $this->morphOne(Visit::class, 'visit');
    }

    public function getType(): string {
        return "General";
    }
}
