<?php

namespace App\Http\Controllers\Records;

use App\Enums\AncCategory;
use App\Enums\AppNotifications;
use App\Enums\Department as EnumsDepartment;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AncVisit;
use App\Models\AntenatalProfile;
use App\Models\GeneralVisit;
use App\Models\Patient;
use App\Models\PatientCategory;
use App\Models\Visit;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PatientsController extends Controller
{
    public function __construct(private PatientService $patientService) {}

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
                'card_type' => 'required|in:' . join(',', AncCategory::getValues()),
                // 'lmp' => 'nullable|date',
                // 'edd' => 'nullable|date',
                'spouse_name' => 'nullable|string',
                'spouse_phone' => 'nullable|string',
                'spouse_occupation' => 'nullable|string',
                'spouse_educational_status' => 'nullable|string',
            ]);
        }

        $data = $request->validate($rules);
        DB::beginTransaction();
        try {
            $patient = Patient::create($data);


            if ($request->anyFilled(['hmo_name', 'hmo_company', 'hmo_id_no'])) {
                $this->patientService->createInsuranceProfile($patient, $request->only(['hmo_name', 'hmo_company', 'hmo_id_no']));
            }

            if ($request->query('mode') == 'anc') {
                AntenatalProfile::create([
                    'patient_id' => $patient->id,
                    'lmp' => $data['lmp'] ?? null,
                    'edd' => isset($data['edd']) ? Carbon::createFromFormat('Y-m-d', $data['edd'])->addMonths(9)->addDays(7) : null,
                    'spouse_name' => $data['spouse_name'],
                    'spouse_phone' => $data['spouse_phone'],
                    'spouse_occupation' => $data['spouse_occupation'],
                    'spouse_educational_status' => $data['spouse_educational_status'],
                    'card_type' => $data['card_type'] ?? '1',
                    // 'status' => Status::pending->value,
                ]);

                notifyDepartment(EnumsDepartment::NUR->value, [
                    'title' => 'New Antenatal Patient Registered',
                    'message' => "A new antenatal patient was just registered. Pending booking for {$patient->name}",
                ], [
                    'mode' => AppNotifications::$BOTH,
                ]);
                notifyDepartment(EnumsDepartment::LAB->value, [
                    'title' => 'New Antenatal Patient Registered',
                    'message' => "A new antenatal patient was just registered. Pending booking for {$patient->name}",
                ], [
                    'mode' => AppNotifications::$BOTH,
                ]);
                notifyDepartment(EnumsDepartment::DOC->value, [
                    'title' => 'New Antenatal Patient Registered',
                    'message' => "A new antenatal patient was just registered. Pending booking for {$patient->name}",
                ], [
                    'mode' => AppNotifications::$BOTH,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return redirect()->back()->with('error', 'An error occurred while creating patient record. Please try again later.');
        }
        return redirect()->route('records.patients');
    }

    public function getPatients(Request $request)
    {
        return $this->dataTable($request, Patient::with('category')->latest(), [
            function ($query, $search) {
                $query->where('name', 'like', "%$search%")->orWhere('card_number', 'like', "$search%")->orWhere('phone', 'like', "$search%");
            },
        ]);
    }

    public function show(Request $request, Patient $patient)
    {
        $patient->load('antenatalProfiles');

        return view('records.patient', compact('patient'));
    }

    public function edit(Request $request, Patient $patient)
    {
        if (!$request->isMethod('POST')) {
            $categories = PatientCategory::all();
            return view('records.edit-patient', compact('patient', 'categories'));
        }

        $data = $request->except('_token');
        DB::beginTransaction();
        try {
            $patient->update($data);
            DB::commit();

            return redirect()->route('records.patient', $patient->id);
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function checkIn(Request $request, Patient $patient)
    {
        return view('records.check-in', compact('patient'));
    }

    public function createAncProfile(Request $request, Patient $patient)
    {
        if ($request->method() !== 'POST') {
            return view('records.forms.anc-profile', compact('patient'));
        }

        // AncCategory::cases()

        $data = $request->validate([
            'card_type' => 'required|in:' . join(',', AncCategory::getValues()),
            'lmp' => 'nullable|date',
            'edd' => 'nullable|date',
            'spouse_name' => 'nullable|string',
            'spouse_phone' => 'nullable|string',
            'spouse_occupation' => 'nullable|string',
            'spouse_educational_status' => 'nullable|string',
        ]);

        $profile = AntenatalProfile::create($data + ['patient_id' => $patient->id]);
        $profile->initLabTests();

        return redirect()->route('records.patient', ['patient' => $patient->id]);
    }

    public function checkOut(Request $request, Visit $visit)
    {
        if ($visit->status != Status::active->value) {
            return response()->json([
                'message' => 'Patient has been checked out. Please contact IT if there is any issue.',
                'ok' => false,
            ]);
        }

        if ($visit->bills->where('status', '!=', Status::cancelled->value)->count() == 0) {
            return response()->json([
                'message' => 'No bill has been created for this patient. Please create a bill payment first.',
                'ok' => false,
                'status' => 'requires_action'
            ]);
        }

        if ($visit->bills->where('status', '!=', Status::cancelled->value)->contains(fn($bill) => $bill->balance > 0)) {
            return response()->json([
                'message' => 'Patient has unpaid bills. Please check their bills and try again.',
                'action' => 'confirm_action',
                'confirmation_data' => ['force' => true],
                'status' => 'requires_action',
                'ok' => false,
            ]);
        }

        $visit->update(['status' => Status::completed->value]);
        return response()->json([
            'message' => 'Patient checked out!',
            'status' => 'success',
            'ok' => true,
        ]);
    }

    public function medicalHistory(Request $request, Patient $patient)
    {
        return view('patients.medical-history', compact('patient'));
    }

    public function addInsuranceProfile(Request $request, Patient $patient)
    {
        $profile = $patient->insurance()->create($request->except('_token'));

        notifyDepartment(EnumsDepartment::NHI->value, [
            'message' => "New patient profile has been added for patient #{$patient->card_number}",
            'bg' => ['bg-blue-400', 'text-white'],
        ], ['mode' => AppNotifications::$BOTH]);

        notifyUserSuccess("Insurance profile added successfully", $request->user(), ['mode' => 'in-app']);

        return response()->json($profile);
    }

    public function getVisits(Request $request)
    {
        $query = Visit::with(['patient.insurance'])->latest();

        return $this->dataTable($request, $query, [
            function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")
                        ->orWhere('card_number', 'like', "$search%")
                        ->orWhere('phone', 'like', "$search%")
                        ->orWhereHas('insurance', function ($q) use ($search) {
                            $q->where('hmo_name', 'like', "$search%");
                        });
                });
            },
        ], function (array $data, $orders) {
            if (empty($orders)) return $data;
            logger()->info($orders);

            $name = array_filter($orders, fn($o) => $o['name'] == 'insurance');

            if (!empty($name)) {
                usort($data, function ($a, $b) {
                    if (isset($a['patient']['insurance'])) {
                        if (isset($b['patient']['insurance'])) {
                            return 0;
                        }

                        return 1;
                    }

                    if (isset($b['patient']['insurance'])) {
                        return -1;
                    }

                    return 0;
                });
            }

            logger()->info($data);

            return $data;
        });
    }

    public function getAntenatalAppointments(Request $request)
    {
        $query = AncVisit::with(['patient', 'visit'])->where('return_visit', '>=', now()->subDays(7))->select([
            'patient_id',
            'return_visit',
            'id',
        ])->orderBy('return_visit', 'desc');

        return $this->dataTable($request, $query, [
            function ($query, $search) {
                $query->whereHas('patient', function ($q) use ($search) {
                    $q->where('name', 'like', "%$search%")->orWhere('card_number', 'like', "$search%")->orWhere('phone', 'like', "$search%");
                });
            },
        ]);
    }
}
