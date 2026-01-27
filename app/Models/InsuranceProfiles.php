<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InsuranceProfiles extends Model
{
    use HasFactory;

    protected $fillable = ['hmo_name', 'hmo_company', 'hmo_id_no'];

    public function patient() {
        return $this->belongsTo(Patient::class);
    }

    public function status(): Attribute {
        return Attribute::make(
            get: fn ($v) => Status::from($v)->name,
        );
    }
}
