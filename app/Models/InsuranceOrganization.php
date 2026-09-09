<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsuranceOrganization extends Model
{
    protected $fillable = [
        'name',
        'is_public',
        'contact_details',
        'portal_url',
    ];

    protected $casts = [
        'contact_details' => 'array',
    ];

    public function enrollees()
    {
        return $this->hasMany(InsuranceProfiles::class, 'orgid');
    }
}
