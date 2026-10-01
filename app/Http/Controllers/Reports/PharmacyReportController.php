<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class PharmacyReportController extends Controller
{
    public function index(Request $request)
    {
        $dispenseQuery = StockTransaction::where('tx_type', StockTransaction::ISSUE);

        $drugsDispensedToday = $dispenseQuery->clone()->with(['item'])
            ->where('created_at', '>=', today())
            ->groupBy('item_id')
            ->selectRaw("count(item_id) as count, item_id")
            ->orderBy('count', 'desc')
            ->limit(20)
            ->get();
        $drugsDispensedLastPeriod = $dispenseQuery->clone()->count();

        $topDrugsDispensed = $dispenseQuery->clone()->with('item')
            ->where('created_at', '>=', today())
            ->groupBy('item_id')->select(['item_id'])
            ->selectRaw('SUM(quantity) as quantity')
            ->orderBy('quantity', 'DESC')
            ->limit(20)
            ->get();

        $data = compact('drugsDispensedToday', 'drugsDispensedLastPeriod', 'topDrugsDispensed');
        return inertia('Reports/PharmacyIndex', [
            'data' => $data,
        ]);
    }
}
