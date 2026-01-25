<?php

namespace App\Http\Controllers;

use App\Enums\Department;
use App\Enums\Status;
use App\Models\PatientAppointment;
use App\Models\Visit;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AppointmentsController extends Controller
{
    public function index(Request $request)
    {
        return view('appointments.index');
    }

    public function create(Request $request) {}

    public function show(Request $request, PatientAppointment $appointment)
    {
        if ($request->has('mini')) {
            return view('appointments.show-mini', compact('appointment'));
        }
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Request $request, PatientAppointment $appointment) {}

    public function store(Request $request)
    {
        $data = $request->validate([
            'visit_id' => 'required|integer',
            'appointment_date' => 'required|date|after:now',
            'note' => 'string|nullable',
            'source' => 'required|string',
        ]);

        $visit = Visit::findOrFail($data['visit_id']);

        try {
            $app = PatientAppointment::create([
                'booked_by' => $request->user()->id,
                'patient_id' => $visit->patient_id,
                'status' => Status::active->value,
                ...$data,
            ]);

            notifyDepartment(Department::REC->value, "Appointment booked for {$visit->patient->name} [{$data['appointment_date']}].");

            return response()->json($app, 200);
        } catch (UniqueConstraintViolationException $un) {

            return response()->json([
                'message' => "Patient still has an active appointment booking",
            ], Response::HTTP_EXPECTATION_FAILED);
        } catch (\Throwable $th) {
            report($th);
            return response()->json([
                'message' => $th->getMessage(),
            ], 500);
        }

        return response()->json($request->all());
    }

    public function fetchAppointments(Request $request)
    {
        $query = PatientAppointment::with(['patient'])->active();

        return $this->patientSearchDatatable($request, $query);
    }
}
