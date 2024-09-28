<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentationTest extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
        'patient_id',
        'results',
        'describable_type',
        'describable_id',
    ];

    protected $casts = [
        'results' => 'object',
    ];

    public function documentation()
    {
        return $this->belongsTo(Documentation::class);
    }

    public function testable()
    {
        return $this->morphTo();
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'tested_by');
    }

    public function scopePending()
    {
        $this->where('status', Status::pending->value);
    }

    public function scopeStatus($query, Status $status)
    {
        $query->where('status', $status->value);
    }

    public function describable()
    {
        return $this->morphTo();
    }
}
