<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsultationNote extends Model
{
    use HasFactory;

    protected $fillable =  ['patient_id',  'visit_id', 'consultant_id', 'note'];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function consultant()
    {
        return  $this->belongsTo(User::class, 'consultant_id');
    }
}
