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

    const privateIcon = "<i class='fa fa-user-shield'></i>";
    const publicIcon = "<i class='fa fa-shield-halved'></i>";

    protected $casts = [
        'contact_details' => 'array',
    ];

    public function enrollees()
    {
        return $this->hasMany(InsuranceProfiles::class, 'orgid');
    }
}
