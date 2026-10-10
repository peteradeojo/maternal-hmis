<?php

namespace App\Http\Controllers\Reports;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyReportController extends Controller
{
    public function index(Request $request)
    {
        $today = today();
        $dispenseQuery = StockTransaction::with(['item'])->where('tx_type', StockTransaction::ISSUE)->groupBy('item_id');

        $drugsDispensedToday = $dispenseQuery->clone()
            ->where('created_at', '>=', $today)
            ->selectRaw('count(item_id) as count, item_id')
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();

        $drugsDispensedTodayCount = DB::query()->selectRaw('item_id')->fromSub(
            $dispenseQuery->clone()
                ->where('created_at', '>=', $today)
                ->select(['item_id']),
            'derived'
        )->count();

        $drugsDispensedLastPeriod = $dispenseQuery->clone()->count();

        $topDrugsDispensed = $dispenseQuery->clone()
            ->selectRaw('SUM(quantity) as quantity, item_id')
            ->where('created_at', '>=', $today)
            ->orderBy('quantity', 'DESC')
            ->limit(20)
            ->get();

        $prescriptionsQuery = Prescription::query()->where('created_at', '>=', $today);

        $totalPrescriptionsToday = $prescriptionsQuery->clone()->count();
        $totalPendingPrescriptionsToday = $prescriptionsQuery->clone()->where('status', '=', Status::pending)->count();
        $totalClosedPrescriptionsToday = $prescriptionsQuery->clone()->where('status', '=', Status::closed)->count();

        $data = compact(
            'drugsDispensedToday',
            'drugsDispensedLastPeriod',
            'topDrugsDispensed',
            'drugsDispensedTodayCount',
            'totalClosedPrescriptionsToday',
            'totalPendingPrescriptionsToday',
            'totalPrescriptionsToday',
        );

        // dd($data);
        return inertia('Reports/PharmacyIndex', [
            'data' => $data,
        ]);
    }
}
