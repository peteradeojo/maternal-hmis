<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Bill;
use App\Models\BillPayment;
use Carbon\WeekDay;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function index(Request $request)
    {
        $date = Carbon::today();

        $query = DB::table('bill_payments')->groupBy(['payment_method'])->where('status', Status::PAID->value)->selectRaw("SUM(amount) amount, payment_method as method");

        $payments = $query->get()->toArray();

        $today = $query->clone()->whereBetween('created_at', [$date->format('Y-m-d'), $date->clone()->addDay()->format('Y-m-d')])->get()->toArray();

        usort($payments, function ($a, $b) {
            if ($a->method > $b->method) return 1;
            if ($a->method < $b->method) return -1;

            return 0;
        });

        usort($today, function ($a, $b) {
            if ($a->method > $b->method) return 1;
            if ($a->method < $b->method) return -1;

            return 0;
        });

        $bills = [];
        return view('finance.index', compact('payments', 'today', 'bills'));
    }

    public function getBillTrendStats(Request $request)
    {
        $interval = $request->query('interval', 'day');

        $intervalMultiplier = match ($interval) {
            'day' => 1,
            'week' => 7,
            'month' => 30,
        };

        $date = Carbon::now()->subWeeks($request->query('ago', 0) * $intervalMultiplier)->startOfWeek(WeekDay::Sunday);
        $endDate = $date->clone()->endOfWeek(WeekDay::Saturday);

        $bills =
            DB::query()->fromSub(
                Bill::query()->groupByRaw("status, date_trunc('$interval', created_at)")
                    ->selectRaw("COUNT(id), status, date_trunc('$interval', created_at) as created_at")
                    ->whereBetween('created_at', [$date->format('Y-m-d'), $endDate])
                    ->whereIn('status', [Status::cancelled, Status::PAID, Status::quoted->value]),
                'subquery'
            )->selectRaw(
                "date_trunc('$interval', created_at) as created,
            jsonb_object_agg(status, count) as status_counts,
            extract(dow from created_at) as dow"
            )
            ->groupBy(['created', 'dow'])
            ->orderBy('created', 'asc')
            ->get()->toArray();

        return response()->json($bills);
    }
}
