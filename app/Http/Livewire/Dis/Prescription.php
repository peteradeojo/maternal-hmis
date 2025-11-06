<?php

namespace App\Http\Livewire\Dis;

use App\Enums\AppNotifications;
use App\Enums\Status;
use App\Models\BillDetail;
use App\Models\DocumentationPrescription;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Prescription extends Component
{
    // public $doc;
    // public $type;
    // public $id;

    public $items = [];
    public $quoteDone = false;
    public $bill;

    public $totalAmount = 0;

    public function mount($bill)
    {
        // $this->doc = $doc;

        // $this->items = $doc->treatments->map(function ($t) {
        //     $this->quoteDone = $t->status == Status::quoted->value && $this->quoteDone;
        //     return (object) [
        //         'id' => $t->id,
        //         'name' => $t->name,
        //         'dosage' => $t->dosage,
        //         'frequency' => $t->frequency,
        //         'duration' => $t->duration,
        //         'amount' => $t->amount,
        //         'available' => (bool) $t->available,
        //     ];
        // })->toArray();

        $this->bill = $bill;
        $this->quoteDone = $bill->status == Status::quoted->value;
        $this->items = $bill->entries->where('tag', 'drug')->map(fn($b) => (object) [
            'meta' => $b->meta,
            'description' => $b->description,
            'amount' => $b->total_price,
            'id' => $b->id,
            'available' => $b->meta['available'] ?? false,
        ]);

        $this->totalAmount = $this->items->sum('amount');
    }

    public function render()
    {
        return view('livewire.dis.prescription');
    }

    // public function save()
    // {
    //     DB::beginTransaction();
    //     try {
    //         foreach ($this->items as $item) {
    //             $t = $this->doc->treatments()->find($item->id)?->load('prescriptionable');

    //             $update_amt = $t->amount != $item->amount;

    //             $t->amount = $item->amount;
    //             $t->available = $item->available;
    //             // if ($this->quoteDone || $item->available && (int) $item->amount > 0) {
    //             //     $t->status = Status::quoted->value;
    //             // }
    //             if (!$item->available) {
    //                 $t->status = Status::blocked->value;
    //             } else {
    //                 if ((int) $item->amount > 0) {
    //                     $t->status = Status::quoted->value;
    //                 }
    //             }
    //             $t->save();

    //             if ($update_amt) {
    //                 try {
    //                     // $t->prescriptionable->save(['amount' => $item->amount]);
    //                     Product::where('id', $item->id)->update([
    //                         'amount' => $item->amount,
    //                     ]);
    //                 } catch (\Throwable $th) {
    //                     logger()->emergency("Unable to save price for product: {$item->id}");
    //                 }
    //             }
    //         }

    //         DB::commit();
    //         notifyUserSuccess('Prescription quote saved successfully.', ['mode' => AppNotifications::$IN_APP]);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         notifyUserError('An error occurred while saving the prescription quote: ' . $e->getMessage(), ['mode' => AppNotifications::$IN_APP]);
    //     }
    // }

    public function save()
    {
        $errors = [];
        DB::beginTransaction();
        $this->items->each(function ($item) use (&$errors) {
            try {
                /**
                 * @var BillDetail
                 */
                $entry = BillDetail::find($item->id);
                if (!$entry) return;

                $entry->meta = array_merge($entry->meta, [...($item->meta), 'available' => $item->available]);
                $entry->quoted_at = now();
                $entry->quoted_by = auth()->user()->id;
                $entry->total_price = $item->amount;
                $entry->save();

                $entry->pushMetaData();

                if ($item->meta['data']['prescriptionable_id'] ?? false) {
                    Product::where('id', $item->meta['data']['prescriptionable_id'])->update([
                        'amount' => $item->amount,
                    ]);

                    // DocumentationPrescription::where('id', $item->meta['data']['id'])->update([
                    //     'available' => $item->available,
                    // ]);
                } else {
                    Product::create([
                        'name' => $item->description,
                        'description' => $item->description,
                        'amount' => $item->amount,
                        'product_category_id' => ProductCategory::where('name', 'PHARMACY')->first()->id,
                    ]);
                }
            } catch (\Throwable $th) {
                // dump($th);
                DB::rollBack();

                $errors[] = $item->id;
                notifyUserError($th->getMessage(), ['mode' => 'in-app']);

                logger()->emergency("Failed to save prescription quote: " . $th->getMessage());
                return false;
            }
        });

        DB::commit();
        if (count($errors) > 0) return;

        if ($this->quoteDone) {
            $this->bill->update(['status' => Status::quoted->value]);
        } else {
            $this->bill->update(['status' => Status::pending->value]);
        }

        notifyUserSuccess("Quote saved!", ['mode' => 'in-app']);
        $this->dispatch('$refresh');
    }
}
