<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Jobs\GenerateVisitReport;
use App\Models\Visit;
use App\Services\Comms;
use Illuminate\Http\Request;

use function Spatie\LaravelPdf\Support\pdf;

class VisitsController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::with(["patient.category"])
            ->whereNotIn("status", [
                Status::cancelled->value,
                Status::completed->value,
                Status::ejected->value,
            ])->limit(100)->latest();

        return $this->dataTable($request, $query, [
            function ($query, $search) {
                $query->whereHas("patient", function ($q) use ($search) {
                    $q->where("name", "ilike", "%{$search}%");
                });
            },
        ]);
    }

    public function generateReport(Request $request, Visit $visit) {
        $this->authorize('print', $visit);
        if ($request->expectsJson()) {
            Comms::notifyUserSuccess("The report is being generated.", $request->user());
            dispatch(new GenerateVisitReport($visit));

            return response()->json([
                'ok' => true,
            ]);
        }
        $visit->load(['patient', ]);

        $disclaimer = "<p style='color: #333;font-size: 0.7em;'>This document is the property of Maternal-Child Specialists' Clinics. The information contained within is confidential and meant only for authorized persons. This report is not to be shared, distributed or reproduced in any form or format, physical or digital.</p>";

        return pdf()->view('visit-report', compact('visit'))->name('Encounter report')->headerHtml($disclaimer)->footerHtml($disclaimer);
    }
}
