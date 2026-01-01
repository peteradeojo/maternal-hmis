<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\Patient::class => \App\Policies\PatientPolicy::class,
        \App\Models\Visit::class => \App\Policies\VisitPolicy::class,
        \App\Models\Admission::class => \App\Policies\AdmissionPolicy::class,
        \App\Models\Bill::class => \App\Policies\BillPolicy::class,
        \App\Models\AntenatalProfile::class => \App\Policies\AntenatalProfilePolicy::class,
        \App\Models\DocumentationTest::class => \App\Policies\DocumentationTestPolicy::class,
        \App\Models\Prescription::class => \App\Policies\PrescriptionPolicy::class,
        \App\Models\PatientImaging::class => \App\Policies\PatientImagingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
