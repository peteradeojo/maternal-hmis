<?php

namespace App\Http\Livewire\Dis;

use App\Enums\AppNotifications;
use App\Enums\Status;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Prescription extends Component
{
    public $doc;
    public $type;
    public $id;

    public $items = [];
    public $quoteDone = true;

    public function mount($doc)
    {
        $this->doc = $doc;

        $this->items = $doc->treatments->map(function ($t) {
            $this->quoteDone = $t->status == Status::quoted->value && $this->quoteDone;
            return (object) [
                'id' => $t->id,
                'name' => $t->name,
                'dosage' => $t->dosage,
                'frequency' => $t->frequency,
                'duration' => $t->duration,
                'amount' => $t->amount,
                'available' => (bool) $t->available,
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.dis.prescription');
    }

    public function save()
    {
        DB::beginTransaction();
        try {
            foreach ($this->items as $item) {
                $t = $this->doc->treatments()->find($item->id);
                $t->amount = $item->amount;
                $t->available = $item->available;
                if ($this->quoteDone || $item->available && (int) $item->amount > 0) {
                    $t->status = Status::quoted->value;
                }
                $t->save();
            }

            DB::commit();
            notifyUserSuccess('Prescription quote saved successfully.', ['mode' => AppNotifications::$IN_APP]);
        } catch (\Exception $e) {
            DB::rollBack();
            notifyUserError('An error occurred while saving the prescription quote: ' . $e->getMessage(), ['mode' => AppNotifications::$IN_APP]);
        }
    }
}
