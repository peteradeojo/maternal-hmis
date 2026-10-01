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

        $drugsDispensedToday = $dispenseQuery->clone()->with(['item'])->where('created_at', '>=', today())->groupBy('item_id')->selectRaw("count(item_id) as count, item_id")->get(); //->count();
        $drugsDispensedLastPeriod = $dispenseQuery->clone()->count();

        $topDrugsDispensed = $dispenseQuery->clone()->with('item')->where('created_at', '>=', today())->groupBy('item_id')->select(['item_id'])->selectRaw('SUM(quantity) as quantity')->get(); //->get();
        // dd($topDrugsDispensed);

        $data = compact('drugsDispensedToday', 'drugsDispensedLastPeriod', 'topDrugsDispensed');
        // dd($data);
        return inertia('Reports/PharmacyIndex', [
            'data' => $data,
        ]);
    }
}
