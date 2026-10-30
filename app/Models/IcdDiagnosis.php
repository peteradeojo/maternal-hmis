<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Override;

class IcdDiagnosis extends Model
{
    protected $fillable = [
        'code',
        'short_description',
        'long_description'
    ];

    #[Override]
    public function __toString()
    {
        return "$this->short_description ($this->code)";
    }
}
