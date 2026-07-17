<?php

namespace App\Listeners;

use App\Enums\Status;
use App\Events\BillableCreated;
use App\Events\BillableDeleted;
use App\Events\BillableUpdated;
use App\Models\Bill;
use App\Models\Visit;
use Exception;
use Illuminate\Events\Dispatcher;

class BillableSubscriber
{
    public function subscribe(Dispatcher $events)
    {
        return [
            BillableCreated::class => 'handleBillableCreate',
            BillableUpdated::class => 'handleBillableUpdate',
            BillableDeleted::class => 'handleBillableDelete',
        ];
    }

    public function handleBillableCreate(BillableCreated $event)
    {
        $billable = $event->billable;

        $visit = $billable->getVisit();
        $evt = $billable->getEvent();

        if (!$visit) {
            throw new Exception("Attempt to create a billable item without a corresponding bill record. [" . $billable::class . "/{$billable->id}]");
        }

        $bill = $visit->bills()->where('status', Status::active)->latest()->first();
        if (!$bill) {
            $bill = $visit->bills()->create([
                'patient_id' => $visit->patient->id,
                'bill_number' => Bill::generateBillNumber($visit),
                'bill_date' => now(),
                'paid_amount' => 0,
                'created_by' => $event->userId,
            ]);
        }

        $charge = $billable->getChargeFor($evt);

        $bill->entries()->create([
            'user_id' => $event->userId,
            ...$charge,
            'quoted_by' => $event->userId,
        ]);
    }

    public function handleBillableUpdate(BillableCreated $event)
    {
        logger()->info("Billable update");
    }

    public function handleBillableDelete(BillableCreated $event) {}
}
