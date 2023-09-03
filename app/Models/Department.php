<?php

namespace App\Models;

use App\Notifications\StaffNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Notification as FacadesNotification;

class Department extends Model
{
    use HasFactory, Notifiable;

    public function members()
    {
        return $this->hasMany(User::class);
    }

    public function notifyParticipants(Notification $notification)
    {
        FacadesNotification::send($this->members, $notification);
    }
}
