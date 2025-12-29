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
        $this->authorize('viewAny', AntenatalProfile::class);
        return view('nursing.anc-bookings');
    }

    public function getPendingVitals(Request $request)
    {
        $this->authorize('viewAny', Visit::class);
        return $this->dataTable($request, Visit::accessibleBy($request->user())->with(['patient.category'])->active()->where(function ($query) {
            $query->doesntHave('vitals')->orWhere('awaiting_vitals', true);
        }), [
            function ($query, $search) {
                $query->whereHas('patient', function ($query) use ($search) {
                    $query->where('name', 'ilike', "$search%")->orWhere('card_number', "like", "$search%");
                });
            },
        ]);
    }

    public function getAncBookings(Request $request)
    {
        $this->authorize('viewAny', AntenatalProfile::class);
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
                    $query->where('name', 'ilike', "{$search}%");
                });
            }
        ]);
    }

    public function viewAncBooking(Request $request, AntenatalProfile $profile)
    {
        $this->authorize('view', $profile);
        return view('nursing.anc-booking', ['profile' => $profile]);
    }

    public function submitAncBooking(VitalsRequest $request, AntenatalProfile $profile)
    {
        $this->authorize('update', $profile);
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
