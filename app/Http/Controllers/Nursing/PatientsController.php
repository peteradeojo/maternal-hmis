<?php

namespace App\Http\Controllers\Nursing;

use App\Enums\Department;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\VitalsRequest;
use App\Models\AntenatalProfile;
use App\Models\Visit;
use App\Models\Vitals;
use Illuminate\Http\Request;

class PatientsController extends Controller
{
    public function ancBookings(Request $request)
    {
        return view('nursing.anc-bookings');
    }

    public function getPendingVitals(Request $request)
    {
        return $this->dataTable($request, Vitals::getPendingVitalVisits(), [
            function ($query, $search) {
                $query->whereHas('patient', function ($query) use ($search) {
                    $query->where('name', 'like', "$search%")->orWhere('card_number', "like", "$search%");
                });
            },
        ]);
    }

    public function getAncBookings(Request $request)
    {
        $user = $request->user();
        $query = AntenatalProfile::with('patient');

        if (!$request->has('admin')) {
            if ($user->hasRole('lab')) {
                $query = $query->where('awaiting_lab', true)->orWhere('tests', null);
            }

            if ($user->hasRole('nurse'))
                $query = $query->where('awaiting_vitals', true);
            if ($user->hasRole('doctor'))
                $query = $query->where('awaiting_doctor', true);
        }

        return $this->dataTable($request, $query, [
            function ($query, $search) {
                $query->whereHas('patient', function ($query) use ($search) {
                    $query->where('name', 'like', "{$search}%");
                });
            }
        ]);
    }

    public function viewAncBooking(Request $request, AntenatalProfile $profile)
    {
        return view('nursing.anc-booking', ['profile' => $profile]);
    }

    public function submitAncBooking(VitalsRequest $request, AntenatalProfile $profile)
    {
        $validated = $request->safe();

        // $profile->vitals = array_merge($profile->vitals ?? []);
        $profile->parity = $validated->parity;
        $profile->gravida = $validated->gravida;
        $profile->lmp = $validated->lmp;
        $profile->edd = $validated->edd;
        $profile->awaiting_vitals = false;

        // $profile->risk_assessment = $validated->risk_assessment;
        $profile->save();

        return redirect()->route('nurses.anc-bookings')->with('success', 'Vitals submitted successfully');
    }
}
