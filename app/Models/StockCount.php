<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Model;

class StockCount extends Model
{
    protected $fillable = [
        'performed_by', 'location_id', 'count_date', 'status', 'applied_at'
    ];

    protected $casts = [
        'status' => Status::class,
        'count_date' => 'datetime',
    ];

    public function counter() {
        return $this->belongsTo(User::class, 'performed_by');
    }

    public function records() {
        return $this->hasMany(StockCountLine::class, 'stock_count_id');
    }
}
