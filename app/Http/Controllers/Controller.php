<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function dataTable(Request $request, Builder|QueryBuilder $builder, array $searchableColumns = [])
    {
        $length = $request->query('length', 10);
        $start = $request->query('start', 0);
        $search = $request->input('search', ['value' => null, 'regex' => false])['value'];

        $results = $builder->clone();
        $results = $results->where(function ($query) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $column($query, $search);
            }
        });

        $data = [
            'data' => $results->clone()->skip($start)->limit($length)->get(),
            'recordsTotal' => $builder->count(),
            'recordsFiltered' => $results->count(),
            'draw' => (int) $request->input('draw'),
        ];

        return response()->json($data);
    }
}
