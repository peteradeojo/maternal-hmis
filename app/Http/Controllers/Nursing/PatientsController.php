<?php

namespace App\Http\Controllers\Nursing;

use App\Enums\Department;
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
        $user = $request->user();
        $query = AntenatalProfile::with('patient');

        if (!$request->has('admin')) {
            if ($user->department_id == Department::LAB->value) {
                $query->where('awaiting_lab', true)->orWhere('tests', null);
            }

            if ($user->department_id == Department::NUR->value) $query->where('awaiting_vitals', true);
            if ($user->department_id == Department::DOC->value) $query->where('awaiting_doctor', true);
        }

        return $this->dataTable($request, $query, [
            function ($query, $search) {
                $query->whereHas('patient', function ($query) use ($search) {
                    $query->where('name', 'like', "{$search}%");
                });
            }
        ]);
    }

    public function submitAncBooking(VitalsRequest $request, AntenatalProfile $profile)
    {
        $validated = $request->safe()->all();

        $profile->vitals = array_merge($profile->vitals ?? [], $validated);
        $profile->awaiting_vitals = false;
        $profile->save();

        return redirect()->route('nurses.anc-bookings')->with('success', 'Vitals submitted successfully');
    }
}
