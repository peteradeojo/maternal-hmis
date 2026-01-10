<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'user_id',
        'medially_type',
        'medially_id',
        'file_url',
        'file_name',
        'file_type',
        'size',
        'user_id',
        'receiver_type',
        'receiver_id',
        'expires_at',
    ];

    protected $with = ['sender', 'receiver'];

    public function medially()
    {
        return $this->morphTo();
    }

    public function scopeAccessible($query, User $user)
    {
        return $query->where('user_id', $user->id)->orWhere(function ($qu) use (&$user) {
            $qu->where('receiver_type', User::class)->where('receiver_id', $user->id);
        })->orWhere(function ($qu) use (&$user) {
            $qu->where('receiver_type', Department::class)->where('receiver_id', $user->department_id);
        });
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('expires_at', '>', now())->orWhereNull('expires_at');
        });
    }

    public function receiver()
    {
        return $this->morphTo();
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
