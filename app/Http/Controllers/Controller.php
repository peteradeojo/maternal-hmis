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

        $order = $request->input('order', []);
        $results = $builder->clone();

        // Time filters
        $results = $results->when($request->from_date, function ($query, $fromDate) {
            $query->where('created_at', '>=', $fromDate);
        });

        $results = $results->when($request->to_date, function ($query, $date) {
            $query->where('created_at', '<=', $date);
        });

        $results = $results->where(function ($query) use ($search, $searchableColumns) {
            foreach ($searchableColumns as $column) {
                $column($query, $search);
            }
        });

        $countQuery = DB::table($builder, 't1')->selectRaw('COUNT(*) total');
        $filteredCount = DB::table($results, 't1')->selectRaw('COUNT(*) as total');

        $data = $results->skip($start)->limit($length)->get()->toArray();

        function findNestedValue($data, $values)
        {
            if (count($values) <= 1) {
                return $data[$values[0]];
            }

            return findNestedValue($data[$values[0]], array_slice($values, 1));
        }

        if (count($order) > 0) {
            foreach ($order as $o) {
                if (is_null($o['name'])) {
                    continue;
                }

                $nameComponents = explode('.', $o['name']);

                if (count($nameComponents) == 1) {
                    $name = $nameComponents[0];

                    usort($data, function ($a, $b) use (&$name) {
                        if ($a[$name] > $b[$name]) {
                            return 1;
                        } else {
                            return 0;
                        }
                    });
                } else {
                    usort($data, function ($a, $b) use ($nameComponents, &$o) {
                        $aValue = findNestedValue($a, $nameComponents);
                        $bValue = findNestedValue($b, $nameComponents);

                        $k = 0;
                        if ($aValue > $bValue) {
                            $k = 2;
                        } elseif ($aValue == $bValue) {
                            return 0;
                        } else {
                            $k = -2;
                        }

                        return $o['dir'] == 'desc' ? -1 * $k : $k;
                    });
                }
            }
        }

        $data = [
            'data' => $data,
            'recordsTotal' => $countQuery->first()->total,
            'recordsFiltered' => $filteredCount->first()->total,
            'draw' => (int) $request->input('draw'),
        ];

        return response()->json($data);
    }

    protected function patientSearchDatatable(Request $request, Builder|QueryBuilder $builder, array $searchableColumns = [], ?Closure $orderFunction = null)
    {
        return $this->dataTable($request, $builder, [
            function ($query, $search) {
                return $query->whereHas('patient', function ($q) use (&$search) {
                    return $q->where('name', 'ilike', "$search%")->orWhere('phone', 'ilike', "$search%")->orWhere('card_number', 'ilike', "$search%");
                });
            },
            ...$searchableColumns,
        ], $orderFunction);
    }
}
