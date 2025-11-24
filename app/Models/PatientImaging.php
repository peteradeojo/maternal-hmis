<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PatientImaging extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patient_id',
        'documentation_id',
        'name',
        'type',
        'path',
        'comment',
        'status',
        'requested_by',
        'uploaded_by',
        'uploaded_at',
        'created_at',
        'updated_at',
        'documentable_type',
        'documentable_id',
        'describable_type',
        'describable_id',
        'results',
        'deleted_at',
    ];

    protected $casts = [
        'results' => 'object',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function requester () {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // Event: visit or admission
    public function documentable() {
        return $this->morphTo();
    }

    // The poduct details
    public function describable() {
        return $this->morphTo();
    }

    public function scopeStatus($query, Status $status) {
        $query->where('status', $status->value);
    }

    public function getSecurePathAttribute() {
        if (str_contains($this->path, 'cloudinary')) {
            return $this->path;
        }

        return asset($this->path);
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getResults() {
        if (empty($this->results)) {
            return;
        }

        if ($this->results->report_type == 'obstetric') {
            return view('rad.results.obstetric', ['scan' => $this]);
        }
        if ($this->results->report_type == 'general') {
            return view('rad.results.general', ['scan' => $this]);
        }
        if ($this->results->report_type == 'echo') {
            return view('rad.results.echo', ['scan' => $this]);
        }       
    }
}
