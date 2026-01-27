<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\AntenatalProfile;
use App\Models\Patient;
use App\Models\PatientCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Antenatal extends Controller
{
    public function create(Request $request, Patient $patient)
    {
        if ($request->isMethod('POST')) {
            DB::beginTransaction();

            try {
                $profile = $patient->antenatalProfiles()->create($request->except('_token'));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }

            return redirect()->route('records.patient', $patient);
        }

        $ancCategory = PatientCategory::where('name', 'Antenatal')->first();
        return view('records.new-anc-form', compact('patient', 'ancCategory'));
    }

    public function closeProfile(Request $request, AntenatalProfile $profile) {
        if ($request->isMethod('GET')) {
            return view('records.components.close-anc', compact('profile'));
        }

        $request->validate([
            'closed_on' => 'required|date',
            'close_reason' => 'required|string',
        ]);

        $data = array_merge($request->except(['_token', '_method']), [
            'closed_by' => auth()->user()->id,
            'closed_date' => now(),
            'status' => Status::closed->value,
        ]);

        $data['closed_on'] = Carbon::createFromFormat("Y-m-d\TH:i", $data['closed_on']);

        $profile->update($data);
        return redirect()->route('records.patient', $profile->patient);
    }
}
