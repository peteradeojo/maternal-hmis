<?php

namespace App\Http\Controllers\Reports;

use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PharmacyReportController extends Controller
{
    // public function index(Request $request)
    // {
    //     $today = $request->input('date') ? Carbon::parse($request->input('date')) : today();
    //     $dispenseQuery = StockTransaction::with(['item'])->where('tx_type', StockTransaction::ISSUE)->groupBy('item_id');
    //
    //     $drugsDispensedToday = $dispenseQuery->clone()
    //         ->where('created_at', '>=', $today)
    //         ->selectRaw('count(item_id) as count, item_id')
    //         ->orderBy('count', 'desc')
    //         ->limit(20)
    //         ->get();
    //
    //     $drugsDispensedTodayCount = DB::query()->selectRaw('item_id')->fromSub(
    //         $dispenseQuery->clone()
    //             ->where('created_at', '>=', $today)
    //             ->select(['item_id']),
    //         'derived'
    //     )->count();
    //
    //     $drugsDispensedLastPeriod = $dispenseQuery->clone()->count();
    //
    //     $topDrugsDispensed = $dispenseQuery->clone()
    //         ->selectRaw('SUM(quantity) as quantity, item_id')
    //         ->where('created_at', '>=', $today)
    //         ->orderBy('quantity', 'DESC')
    //         ->limit(20)
    //         ->get();
    //
    //     $prescriptionsQuery = Prescription::query()->where('created_at', '>=', $today);
    //
    //     $totalPrescriptionsToday = $prescriptionsQuery->clone()->count();
    //     $totalPendingPrescriptionsToday = $prescriptionsQuery->clone()->where('status', '=', Status::pending)->count();
    //     $totalClosedPrescriptionsToday = $prescriptionsQuery->clone()->where('status', '=', Status::closed)->count();
    //
    //     $data = compact(
    //         'drugsDispensedToday',
    //         'drugsDispensedLastPeriod',
    //         'topDrugsDispensed',
    //         'drugsDispensedTodayCount',
    //         'totalClosedPrescriptionsToday',
    //         'totalPendingPrescriptionsToday',
    //         'totalPrescriptionsToday',
    //     );
    //
    //     if ($request->expectsJson()) {
    //         return response()->json(['data' => $data]);
    //     }
    //
    //     // dd($data);
    //     return inertia('Reports/PharmacyIndex', [
    //         'data' => $data,
    //     ]);
    // }

    public function index(Request $request)
    {
        $now = now(); //->subMonth();
        if (app()->isProduction() == false) $now = $now->subMonth();

        $weekStart = $now->copy()->startOfWeek(); //->format('Y-m-d');
        $weekEnd = $weekStart->copy()->addDays(7)->format('Y-m-d');
        $weekStart = $weekStart->format('Y-m-d');

        $cacheKey = "reports:phm:" . md5("{$weekStart}-{$weekEnd}");

        $data = Cache::get($cacheKey);
        if (!$data) {
            $topDrugs = DB::select("
            SELECT pl.item_id, si.name, sum(qty_dispensed) qty FROM prescription_lines pl
            LEFT JOIN stock_items si on si.id = pl.item_id
            WHERE pl.qty_dispensed != 0
            AND pl.created_at >= timestamp '{$weekStart}'
            AND pl.created_at < timestamp '{$weekEnd}'
            GROUP BY pl.item_id, si.name
            ORDER BY qty DESC LIMIT 5;
            ");

            $bottom = DB::select("
            SELECT pl.item_id, si.name, sum(qty_dispensed) qty FROM prescription_lines pl
            LEFT JOIN stock_items si on si.id = pl.item_id
            WHERE pl.qty_dispensed != 0
            AND pl.created_at >= timestamp '{$weekStart}'
            AND pl.created_at < timestamp '{$weekEnd}'
            GROUP BY pl.item_id, si.name
            ORDER BY qty ASC LIMIT 5;
            ");

            // $dailyDispensings = DB::select("SELECT SUM(pl.qty_dispensed) as qty, to_char(pl.created_at, 'yyyy-mm-dd') dayt
            // FROM prescription_lines pl
            // LEFT JOIN stock_items si on si.id = pl.item_id
            // WHERE pl.qty_dispensed > 0
            // AND pl.created_at >= timestamp '{$weekStart}'
            // AND pl.created_at < timestamp '{$weekEnd}'
            // GROUP BY dayt, prescription_id
            // ORDER BY dayt ASC
            // ");

            $dailyDispensings = DB::select("SELECT
    to_char(d.day, 'YYYY-MM-DD') AS dayt,
    COUNT(p.id) AS qty
FROM generate_series(
        (SELECT MIN(created_at)::date FROM prescriptions WHERE status = 0 AND created_at >= '$weekStart'),
        CURRENT_DATE,
        interval '1 day'
     ) AS d(day)
LEFT JOIN prescriptions p
       ON p.created_at >= d.day
      AND p.created_at <  d.day + interval '1 day'
      AND p.status = 0
where d.day >= '$weekStart' AND d.day <= '$weekEnd'
GROUP BY d.day
ORDER BY d.day ASC;");

            $data = compact('topDrugs', 'bottom', 'dailyDispensings', 'weekEnd', 'weekStart');
            Cache::set($cacheKey, $data, 30);
        }

        return inertia('Reports/PharmacyIndex', ['data' => $data]);
    }

    protected function getDispenseQuery()
    {
        return StockTransaction::with(['item'])->where('tx_type', StockTransaction::ISSUE)->groupBy('item_id');
    }
}
