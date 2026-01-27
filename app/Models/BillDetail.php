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
            get: fn($v, $attributes) => $attributes['total_price'],
        );
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                return $attributes['description'];
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
                        if ($attrs['status'] == Status::cancelled->value) return "Blocked";

                        return ucfirst(Status::from($attrs['status'])->name);
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
                    default:
                        return Status::tryFrom($attrs['status'])?->name;
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
                ]);
            }

            if ($tag == 'test' && isset($meta_data['id'])) {
                BillDetail::find($id)->update([
                    'meta->data' => DocumentationTest::find($meta_data['id'])->toArray(),
                ]);
            }
        });
    }

    public function chargeable() {
        return $this->morphTo();
    }
}
