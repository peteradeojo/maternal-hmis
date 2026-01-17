<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\Status;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'phone',
        'password',
        'department_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['name'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => Status::class,
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function name(): Attribute
    {
        return Attribute::make(fn() => $this->firstname . ' ' . $this->lastname);
    }

    public function __toString()
    {
        return $this->name;
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($user) {
            if ($user->isDirty('department_id')) {
                $roleMap = [
                    \App\Enums\Department::IT->value => 'admin',
                    \App\Enums\Department::DOC->value => 'doctor',
                    \App\Enums\Department::NUR->value => 'nurse',
                    \App\Enums\Department::REC->value => 'record',
                    \App\Enums\Department::PHA->value => 'pharmacy',
                    \App\Enums\Department::LAB->value => 'lab',
                    \App\Enums\Department::RAD->value => 'radiology',
                    \App\Enums\Department::DIS->value => 'billing',
                    \App\Enums\Department::NHI->value => 'billing',
                ];

                if (isset($roleMap[$user->department_id])) {
                    $user->syncRoles([$roleMap[$user->department_id]]);
                }
            }
        });
    }
}
