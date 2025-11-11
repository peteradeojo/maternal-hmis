<?php

namespace App\Http\Livewire\Dis;

use App\Enums\AppNotifications;
use App\Enums\Status;
use App\Models\BillDetail;
use App\Models\DocumentationPrescription;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
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

    public $pendingUpdate = false;

    public function mount($bill)
    {
        $this->bill = $bill;
        $this->quoteDone = $bill->status == Status::quoted->value;

        $this->getItems();
    }

    public function render()
    {
        return view('livewire.dis.prescription');
    }

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
                notifyUserError($th->getMessage(), auth()->user(), ['mode' => 'in-app']);

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

        notifyUserSuccess("Quote saved!", auth()->user(), ['mode' => 'in-app']);
        $this->dispatch('quote-saved');
        $this->dispatch('$refresh');
    }

    public function hydrate()
    {
        $this->getItems();
        $this->dispatch('$refresh');
    }

    public function getItems()
    {
        $this->items = $this->bill->entries->where('tag', 'drug')->map(fn($b) => (object) [
            'meta' => $b->meta,
            'description' => $b->description,
            'amount' => $b->total_price,
            'id' => $b->id,
            'available' => $b->meta['available'] ?? false,
            'status' => $b->status,
        ]);

        $this->totalAmount = $this->items->sum('amount');
    }

    public function reload() {
        $this->getItems();
        $this->pendingUpdate = false;
        // $this->dispatch('$refresh');
    }

    #[On('echo:bill-update.{bill.id},.BillingUpdate')]
    public function newUpdate() {
        $this->pendingUpdate = true;
        $this->dispatch('$refresh');
    }
}
