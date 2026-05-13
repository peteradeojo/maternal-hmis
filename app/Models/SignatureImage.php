<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SignatureImage extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'storage', 'location', 'tag'
    ];

    public function event() {
        return $this->morphTo();
    }
}
