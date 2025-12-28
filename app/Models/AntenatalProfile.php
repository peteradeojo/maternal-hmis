<?php

namespace App\Models;

use App\Enums\AncCategory;
use App\Enums\Status;
use App\Http\Controllers\LabController;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class AntenatalProfile extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'patient_id',
        'lmp',
        'edd',
        'spouse_name',
        'spouse_phone',
        'spouse_occupation',
        'spouse_educational_status',
        'gravida',
        'parity',
        'card_type',
        'status',
        'fundal_height',
        'presentation',
        'lie',
        'fetal_heart_rate',
        'presentation_relationship',
        'status',
        'risk_assessment',
        'doctor_id',

        // closing
        'closed_on',
        'closed_by',
        'closed_date',
        'close_reason',
    ];

    protected $casts = [
        'lmp' => 'datetime',
        'edd' => 'datetime',
        'vitals' => 'array',
        'examination' => 'array',
    ];

    protected $with = ['tests'];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function history()
    {
        return $this->hasMany(AncVisit::class)->where('doctor_id', '!=', null);
    }

    public function cardType(): Attribute
    {
        return Attribute::make(get: fn($value) => AncCategory::tryFrom($value)->name);
    }

    public function tests()
    {
        return $this->morphMany(DocumentationTest::class, 'testable');
    }

    public function getVitals()
    {
        return $this->vitals;
    }

    public function calculateEddLmp($resave = false)
    {
        if ($this->lmp && empty($this->edd)) {
            $this->edd = Carbon::parse($this->lmp)->addMonths(9)->addDays(7);
        }

        if ($this->edd && empty($this->lmp)) {
            $this->lmp = Carbon::parse($this->edd)->subMonths(9)->subDays(7);
        }

        if ($resave && $this->isDirty()) {
            $this->save();
            $this->refresh();
        }
    }

    public function initLabTests()
    {
        if ($this->tests->count() > 0) {
            return;
        }

        $products = Product::whereIn('name', LabController::$ancBookingTests)->get();

        foreach ($products as $p) {
            $this->tests()->firstOrCreate([
                'name' => $p->name,
                'describable_type' => Product::class,
                'describable_id' => $p->id,
                'patient_id' => $this->patient->id,
            ], [
                'name' => $p->name,
                'describable_type' => Product::class,
                'describable_id' => $p->id,
                'status' => Status::pending->value,
                'patient_id' => $this->patient->id,
                'results' => [],
            ]);
        }
    }

    public function ancVisits()
    {
        return $this->hasMany(AncVisit::class, 'antenatal_profile_id')->latest();
    }

    public function getTestResult($name)
    {
        $test = $this->tests->where('name', $name)->first();
        if (empty($test) || empty($test->results)) {
            return '';
        }

        $results = is_array($test->results) ? $test->results : (array) $test->results;
        return $results[0]['result'] ?? '';
    }

    public function maturity($datetime = null, $short = false)
    {
        if ($this->lmp) {
            $days = ($this->lmp->diffInDays($datetime ?? now()));

            $weeks = intdiv($days, 7);
            $days = abs($days % 7);

            if ($short) {
                return "{$weeks}w" . ($days > 0 ? " +$days" : '');
            }

            return "$weeks week(s) $days day(s)";
        }

        return "No LMP";
    }

    public function consultant()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function scopeAccessibleBy($query, User $user)
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        if ($user->hasAnyRole(['doctor', 'nurse', 'record', 'lab'])) {
            return $query;
        }

        return $query->whereRaw('1 = 0');
    }
}
