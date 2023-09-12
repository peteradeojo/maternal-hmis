<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'documentation_id',
        'name',
        'status',
        'patient_id',
        'results'
    ];

    protected $casts = [
        'results' => 'object',
    ];

    public function documentation()
    {
        return $this->belongsTo(Documentation::class);
    }

    public function testable() {
        return $this->morphTo();
    }
}
