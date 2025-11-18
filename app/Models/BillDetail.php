<?php

namespace App\Models;

use App\Enums\Status;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillDetail extends Model
{
    protected $fillable = [
        'bill_id',
        'user_id',
        'description',
        'quantity',
        'unit_price',
        'total_price',
        'chargeable_type',
        'chargeable_id',
        'tag',
        'meta',
        'status',
        'quoted_at',
        'quoted_by',
    ];

    protected $touches = ['bill'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class, 'bill_id');
    }

    public function amount(): Attribute
    {
        return Attribute::make(
            get: fn($v, $attributes) => $attributes['tag'] == 'drug' ? $attributes['total_price'] * 1.5 : $attributes['total_price'],
        );
    }

    public function name(): Attribute
    {
        return Attribute::make(
            // get: fn($value, $attributes) => $attributes['tag'] != 'drug' ? $attributes['description'] : "{$attributes['meta']['data']['name']}"
            get: function ($value, $attributes) {
                if ($attributes['tag'] != 'drug') return $attributes['description'];

                $meta = json_decode($attributes['meta']);
                return "{$meta->data->name} {$meta->data->dosage} {$meta->data->frequency} {$meta->data->duration} (days)";
            }
        );
    }

    public function viewBillableStatus(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attrs) {
                $meta = json_decode($attrs['meta']);
                switch ($attrs['tag']) {
                    case 'drug':
                        if ($attrs['status'] == Status::blocked->value) return "Blocked";

                        if (!empty($attrs['quoted_at'])) {
                            return isset($meta->available) && $meta->available ? "Available" : "Unavailable";
                        } else {
                            return "Not quoted.";
                        }
                        // return !empty($attrs['quoted_at']) ? "Quoted" : (($meta->available ?? false) ? "Available" : false);
                    case 'test':
                        if (isset($meta->data)) {
                            if ($meta->data->status == Status::cancelled->value) {
                                return "Rejected";
                            }
                            if ($meta->data->status == Status::pending->value) {
                                return "Pending";
                            }
                            if ($meta->data->status == Status::closed->value) {
                                return "Delivered";
                            }
                            if ($meta->data->status == Status::active->value) {
                                return "Samples collected";
                            }
                        }

                        return "Added to bill";
                }
            }
        );
    }

    public function pushMetaData()
    {
        if (!isset($this->meta['data']) || !isset($this->tag)) return;

        $tag = $this->tag;
        $meta = $this->meta;
        $id = $this->id;
        dispatch(function () use ($meta, $id, $tag) {
            $meta_data = $meta['data'];
            if ($tag == 'drug' && isset($meta_data['id'])) {
                DocumentationPrescription::find($meta_data['id'])?->update([
                    'available' => isset($meta['available']) ? $meta['available'] : false,
                    // 'amount' => BillDetail::find($id)->
                ]);
            }

            if ($tag == 'test' && isset($meta_data['id'])) {
                BillDetail::find($id)->update([
                    'meta->data' => DocumentationTest::find($meta_data['id'])->toArray(),
                ]);
            }
        });
    }
}
