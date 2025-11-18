<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentedDiagnosis extends Model
{
    use HasFactory;

    protected $guarded = null;

    public function diagnosable()
    {
        return $this->morphTo();
    }

    public function made_by()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function consultant()
    {
        return $this->made_by();
    }
}
