<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function dataTable(Request $request, Builder|QueryBuilder $builder, array $searchableColumns = [], ?Closure $orderFunction = null)
    {
        $length = $request->query('length', 10);
        $start = $request->query('start', 0);
        $search = $request->input('search', ['value' => null, 'regex' => false])['value'];

        $order = $request->input('order');
        $results = $builder->clone();
        $results = $results->where(function ($query) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $column($query, $search);
            }
        });

        $countQuery = DB::table($builder, 't1')->selectRaw("COUNT(*) total");
        $filteredCount = DB::table($results, 't1')->selectRaw("COUNT(*) as total");

        $data = $results->skip($start)->limit($length)->get()->toArray();

        if ($orderFunction)
            $data = $orderFunction($data, $order);

        $data = [
            'data' => $data,
            'recordsTotal' => $countQuery->first()->total,
            'recordsFiltered' => $filteredCount->first()->total,
            'draw' => (int) $request->input('draw'),
        ];

        return response()->json($data);
    }
}
