<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationComplaints extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'duration',
        'documentable_type',
        'documentable_id',
        'documentation_id',
    ];

    public function documentable() {
        return $this->morphTo();
    }
}
