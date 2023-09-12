<?php

namespace App\Http\Controllers\Nursing;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\VitalsRequest;
use App\Models\AntenatalProfile;
use Illuminate\Http\Request;

class PatientsController extends Controller
{
    public function ancBookings(Request $request)
    {
        return view('nursing.anc-bookings');
    }

    public function getAncBookings(Request $request)
    {
        return $this->dataTable($request, AntenatalProfile::with('patient')->where('status', Status::pending->value), [
            function ($query, $search) {
                $query->whereHas('patient', function ($query) use ($search) {
                    // dd($search);
                    $query->where('name', 'like', "%{$search}%");
                });
            }
        ]);
    }

    public function submitAncBooking(VitalsRequest $request, AntenatalProfile $profile)
    {
        $validated = $request->safe()->all();
        dd($validated);
    }
}
