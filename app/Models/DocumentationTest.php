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
        'testable_type',
        'testable_id',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    protected $casts = [
        'results' => 'object',
    ];

    public function documentation()
    {
        return $this->belongsTo(Documentation::class);
    }

    // The event, visit or admission
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

    // Means the product
    public function describable()
    {
        return $this->morphTo();
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getSampleResults()
    {
        $query = static::where('name', $this->name)->where('id', '<', $this->id)->where('results', '!=', NULL)->latest();

        $test = $query->first();
        if (!$test) return;

        $results = array_map(function ($r) {
            $r->result = null;
            return $r;
        }, $test->results);

        return $results;
    }
}
