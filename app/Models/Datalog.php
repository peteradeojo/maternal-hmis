<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Datalog extends Model
{
    use HasFactory;

    protected $fillable = [
        'action', 'user_id', 'data'
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
