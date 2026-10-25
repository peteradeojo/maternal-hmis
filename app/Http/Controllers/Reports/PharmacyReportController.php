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
            ORDER BY qty DESC LIMIT 10;
            ");

            // $topStockLevels = DB::select("
            // SELECT * FROM inventory_balances ib
            // LEFT JOIN stock_items si ON ib.item_id = si.id
            // LEFT JOIN (SELECT pl.item_id, sum(qty_dispensed) qty FROM prescription_lines pl
            //     WHERE pl.qty_dispensed != 0
            //     AND pl.created_at >= timestamp '{$weekStart}'
            //     AND pl.created_at < timestamp '{$weekEnd}'
            //     GROUP BY pl.item_id
            //     ORDER BY qty ASC LIMIT 20
            // ) as st ON st.item_id = ib.item_id
            // ORDER BY qty_on_hand ASC limit 30;");

            $topStockLevels = DB::select("
            SELECT
    ib.*,
    si.name,
    top.qty AS qty_dispensed_week
FROM (
    SELECT
        pl.item_id,
        SUM(pl.qty_dispensed) AS qty
    FROM prescription_lines pl
    WHERE pl.qty_dispensed != 0
      AND pl.created_at >= timestamp '{$weekStart}'
      AND pl.created_at <  timestamp '{$weekEnd}'
    GROUP BY pl.item_id
    ORDER BY qty DESC
    LIMIT 20
) top
JOIN inventory_balances ib ON ib.item_id = top.item_id
JOIN stock_items si        ON si.id = top.item_id
ORDER BY top.qty DESC;");


            $closedPrescriptions = DB::select("SELECT to_char(d.day, 'YYYY-MM-DD') AS dayt, COUNT(p.id) AS qty
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

            $openPrescriptions = DB::select("SELECT to_char(d.day, 'YYYY-MM-DD') AS dayt, COUNT(p.id) AS qty
            FROM generate_series(
                    (SELECT MIN(created_at)::date FROM prescriptions WHERE status = 1 AND created_at >= '$weekStart'),
                    CURRENT_DATE,
                    interval '1 day'
                 ) AS d(day)
            LEFT JOIN prescriptions p
                   ON p.created_at >= d.day
                  AND p.created_at <  d.day + interval '1 day'
                  AND p.status = 1
            where d.day >= '$weekStart' AND d.day <= '$weekEnd'
            GROUP BY d.day
            ORDER BY d.day ASC;");

            $data = compact('topDrugs', 'topStockLevels', 'closedPrescriptions', 'openPrescriptions', 'weekEnd', 'weekStart');
            // dd($data);
            Cache::set($cacheKey, $data, 30);
        }

        return inertia('Reports/PharmacyIndex', ['data' => $data]);
    }

    protected function getDispenseQuery()
    {
        return StockTransaction::with(['item'])->where('tx_type', StockTransaction::ISSUE)->groupBy('item_id');
    }
}
