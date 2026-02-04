<?php

namespace App\Http\Livewire\Inventory;

use App\Enums\Status;
use App\Jobs\StockTakeReport;
use App\Models\Location;
use App\Models\StockCount;
use App\Models\StockCountLine;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Validate;
use Livewire\Component;

class StockTake extends Component
{
    public StockCount $take;
    public $counted = [];

    public $report = null;
    public $reportGenerating = null;
    private $redis;

    #[Validate('required|integer')]
    public $status = Status::completed->value;

    public function render()
    {
        return view('livewire.inventory.stock-take');
    }

    public function mount(StockCount $_take)
    {
        $this->take = $_take;
        $this->status = $this->take->status->value;

        $this->counted = $_take->records->map(function ($line) {
            return [
                'id' => $line->id,
                'item_id' => $line->item_id,
                'balance' => $line->item->balance,
                'unit_cost' => $line->item->costs->first()?->cost,
                'base_unit' => $line->item->base_unit,
                'quantity' => $line->counted_qty ?? $line->balance,
                'system' => $line->system_qty ?? $line->balance,
                'name' => $line->item->name,
                'item' => $line->item->toArray(),
            ];
        })->toArray();

        $this->redis = Redis::client();
    }

    public function addItem($data)
    {

        if (array_find($this->counted, fn($item) => $data['item_id'] == @$item['item_id'])) {
            return;
        }

        DB::beginTransaction();
        try {
            $item = collect($data['item']);

            $id = StockCountLine::create([
                'stock_count_id' => $this->take->id,
                'item_id' => $data['item']['id'],
                'counted_qty' => $item->get('balance'),
            ]);

            array_push(
                $this->counted,
                [
                    'id' => $id->id,
                    'item_id' => $item->get('id'),
                    'balance' => $item->get('balance'),
                    'unit_cost' => $data['unit_cost'],
                    'base_unit' => $item->get('base_unit'),
                    'system' => $item->get('balance'),
                    'quantity' => 0,
                    'name' => $item->get('name'),
                    'item' => array_merge($data['item'], [
                        'quantity' => $data['item']['balance'],
                        'unit_cost' => $data['unit_cost'],
                    ]),
                ]
            );

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            report($th);
            notifyUserError($th->getMessage(), auth()->user()->id);
        }
    }

    public function save()
    {
        DB::beginTransaction();
        try {
            foreach ($this->counted as $count) {
                StockCountLine::where('id', $count['id'])->update([
                    'counted_qty' => $count['quantity'],
                    'system_qty' => $count['balance'],
                ]);
            }

            DB::commit();
            notifyUserSuccess("Stock take saved!", auth()->user()->id, [
                'bg' => ['bg-green-400', 'text-white'],
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();

            report($th);
            notifyUserError($th->getMessage(), auth()->user()->id);
        }
    }

    public function approve()
    {
        if ($this->take->status == Status::closed) return;

        if ($this->take->status == Status::active) {
            $this->take->status = Status::completed;
        } else {
            $this->take->status = Status::active;
        }

        $this->take->save();

        notifyUserSuccess($this->take->status->name, auth()->user()->id);
        $this->dispatch('$refresh');
    }

    public function apply()
    {
        $this->save();

        if ($this->take->status == Status::closed) {
            return notifyUserError("This stock adjustment has already been applied.", auth()->user()->id);
        }

        if ($this->take->status != Status::completed) {
            return notifyUserError("This stock adjustment needs to be approved first.", auth()->user()->id);
        }

        $userId = auth()->user()->id;

        DB::beginTransaction();

        try {
            foreach ($this->take->records()->lockForUpdate()->get() as $line) {
                if ($line->applied) continue;

                $delta = $line->counted_qty - $line->system_qty;

                if ($delta != 0) {
                    if ($delta > 0) {
                        $tx = StockTransaction::create([
                            'tx_type' => StockTransaction::ADJUSTMENT,
                            'item_id' => $line->item_id,
                            'quantity' => $delta,
                            'from_location_id' => Location::INBOUND,
                            'to_location_id' => Location::STORE,
                            'unit' => $line->item->base_unit,
                            'unit_cost' => $line->item->costs->first()?->cost,
                            'reason' => "Stock count: Gain",
                            'performed_by' => $userId,
                        ]);
                    } else {
                        $tx = StockTransaction::create([
                            'tx_type' => StockTransaction::ADJUSTMENT,
                            'item_id' => $line->item_id,
                            'quantity' => abs($delta),
                            'from_location_id' => Location::STORE,
                            'to_location_id' => Location::OUTBOUND,
                            'unit' => $line->item->base_unit,
                            'unit_cost' => $line->item->costs->first()?->cost,
                            'reason' => "Stock count: Loss",
                            'performed_by' => $userId,
                        ]);
                    }

                    $line->stock_transaction_id = $tx->id;
                }

                $line->applied = true;
                $line->save();
            }

            $this->take->status = Status::closed;
            $this->take->applied_at = now();
            $this->take->save();

            DB::commit();

            notifyUserSuccess("Stock take applied successfully", $userId);
            $this->dispatch('$refresh');
        } catch (\Throwable $th) {
            DB::rollBack();
            report($th);

            notifyUserError($th->getMessage(), $userId);
        }
    }

    public function removeItem($index, $id) {
        DB::beginTransaction();

        try {
            StockCountLine::where('id', $id)->delete();
            array_splice($this->counted, $index, 1);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            report($th);
            notifyUserError($th->getMessage(), request()->user()->id);
        }
    }

    public function checkReportStatus() {
        if ($this->take->status !== Status::closed) {
            return;
        }

        // get the data from the cache
        $this->reportGenerating = StockTakeReport::getReportStatus($this->take->id);

        if ($this->reportGenerating == Status::completed->value) {
            $this->report = StockTakeReport::getReportFile($this->take->id);
        }
    }

    public function generateReport() {
        dispatch(new StockTakeReport($this->take->id));
    }

    public function downloadReport() {
        if ($this->report) {
            return Storage::download($this->report);
        }
    }
}
