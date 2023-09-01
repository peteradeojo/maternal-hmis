<?php

namespace App\Http\Controllers\Records;

use App\Http\Controllers\Controller;
use App\Models\AntenatalProfile;
use App\Models\Patient;
use App\Models\PatientCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PatientsController extends Controller
{
    public function index()
    {
        return view('records.patients');
    }

    public function create(Request $request)
    {
        $categories = PatientCategory::all();

        $ancCategory = $categories->where('name', 'Antenatal')->first();

        if ($request->method() !== 'POST') {
            return match ($request->query('mode')) {
                null => view('records.new-patient', [
                    'categories' => $categories->where('name', '!=', 'Antenatal'),
                ]),
                'anc' => view('records.new-anc', compact('ancCategory')),
            };
        }

        $rules = [
            'category_id' => 'required|integer|exists:patient_categories,id',
            'card_number' => 'nullable|string',
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'dob' => 'nullable|date',
            'gender' => 'integer|in:0,1',
            'marital_status' => 'integer|in:1,2,3,4',
            'address' => 'nullable|string',
            'occupation' => 'nullable|string',
            'religion' => 'nullable|integer|in:1,2,3',
            'email' => 'nullable|email',
            'tribe' => 'nullable|string',
            'place_of_origin' => 'nullable|string',
            'nok_name' => 'nullable|string',
            'nok_phone' => 'nullable|string',
            'nok_address' => 'nullable|string',
        ];

        if ($request->query('mode') === 'anc') {
            $rules = array_merge($rules, [
                'card_type' => 'required|in:1,2,3,4,5',
                'lmp' => 'required|date',
                'edd' => 'required|date',
                'spouse_name' => 'nullable|string',
                'spouse_phone' => 'nullable|string',
                'spouse_occupation' => 'nullable|string',
                'spouse_educational_status' => 'nullable|string',
            ]);
        }

        DB::beginTransaction();
        try {
            $data = $request->validate($rules);
            $patient = Patient::create($data);

            if ($request->query('mode') == 'anc') {
                AntenatalProfile::create([
                    'patient_id' => $patient->id,
                    'lmp' => $data['lmp'],
                    'edd' => Carbon::createFromFormat('Y-m-d', $data['edd'])->addMonths(9)->addDays(7),
                    'spouse_name' => $data['spouse_name'],
                    'spouse_phone' => $data['spouse_phone'],
                    'spouse_occupation' => $data['spouse_occupation'],
                    'spouse_educational_status' => $data['spouse_educational_status'],
                    'card_type' => $data['card_type'] ?? '1',
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'An error occurred while creating patient record. Please try again later.');
        }
        return redirect()->route('records.patients');
    }

    public function getPatients(Request $request)
    {
        $patients = Patient::with('category')->paginate($request->query('count', 20));
        return $patients;
    }

    public function show(Request $request, Patient $patient)
    {
        $patient->load('antenatalProfiles');

        return view('records.patient', compact('patient'));
    }

    public function checkIn(Request $request, Patient $patient)
    {
    }
}
