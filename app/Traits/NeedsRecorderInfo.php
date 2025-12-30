<?php

namespace App\Traits;

use App\Models\AnonymousSessionActivity;
use Illuminate\Support\Facades\Session;

trait NeedsRecorderInfo
{
    protected static function bootNeedsRecorderInfo()
    {
        static::created(function (Self $model) {
            if (
                (app()->bound('session') && Session::isStarted()) == false
            ) return;

            if (!in_array(
                auth()->user()->phone,
                config('app.generic_doctor_profiles')
            )) return;

            AnonymousSessionActivity::create([
                'model_type' => $model::class,
                'model_id' => $model->id,
                'session_id' => Session::getId(),
                'recorder' => Session::get(config('app.generic_doctor_id')),
            ]);
        });
    }

    public function recorder()
    {
        return $this->morphOne(AnonymousSessionActivity::class, 'model');
    }
}
