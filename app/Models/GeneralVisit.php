<?php

namespace App\Models;

use App\Interfaces\Visitable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralVisit extends Model implements Visitable
{
    use HasFactory;

    protected $fillable = [
        'vitals',
    ];

    public function lab()
    {
    }

    public function pharmacy()
    {
    }
}
