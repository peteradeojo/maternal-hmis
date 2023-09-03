<?php

namespace App\Http\Controllers\Records;

use App\Enums\Department as EnumsDepartment;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AncVisit;
use App\Models\AntenatalProfile;
use App\Models\Department;
use App\Models\GeneralVisit;
use App\Models\Patient;
use App\Models\PatientCategory;
use App\Models\Visit;
use App\Notifications\StaffNotification;
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
        $length = $request->query('length', 10);
        $start = $request->query('start', 0);

        $patientData = Patient::with('category');

        $search = $request->input('search', ['value' => null, 'regex' => false])['value'];

        $results = $patientData->clone();
        if ($search) {
            $results = $results->where('name', "like", "$search%")->orWhere("card_number", "like", "%$search%");
        }

        $data = [
            'data' => $results->clone()->skip($start)->take($length)->get(),
            'recordsTotal' => Patient::count(),
            'recordsFiltered' => $results->count(),
            'draw' => (int) $request->input('draw'),
        ];

        return response()->json($data);
    }

    public function show(Request $request, Patient $patient)
    {
        $patient->load('antenatalProfiles');

        return view('records.patient', compact('patient'));
    }

    public function checkIn(Request $request, Patient $patient)
    {
        /**
         * @var Department
         */
        $nursing = Department::where('id', EnumsDepartment::NUR->value)->first();

        $nursing->notifyParticipants(new StaffNotification("A patient was just checked in"));

        if (Visit::where('patient_id', $patient->id)->where('status', Status::active->value)->whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Patient already checked-in today',
            ]);
        }

        if ($request->query('mode') == 'anc') {
            $subVisit = AncVisit::create([
                'patient_id' => $patient->id,
            ]);
        } else {
            $subVisit = GeneralVisit::create([
                'patient_id' => $patient->id,
            ]);
        }

        Visit::create([
            'patient_id' => $patient->id,
            'visit_type' => $subVisit::class,
            'visit_id' => $subVisit->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Patient Checked-In Successfully',
        ]);
    }

    public function createAncProfile(Request $request, Patient $patient)
    {
        if ($request->method() !== 'POST') {
            return view('records.forms.anc-profile', compact('patient'));
        }

        $data = $request->validate([
            'card_type' => 'required|in:1,2,3,4,5',
            'lmp' => 'required|date',
            'edd' => 'required|date',
            'spouse_name' => 'nullable|string',
            'spouse_phone' => 'nullable|string',
            'spouse_occupation' => 'nullable|string',
            'spouse_educational_status' => 'nullable|string',
        ]);

        $profile = AntenatalProfile::create($data + ['patient_id' => $patient->id]);
        return redirect()->route('records.patient', ['patient' => $patient->id]);
    }
}
