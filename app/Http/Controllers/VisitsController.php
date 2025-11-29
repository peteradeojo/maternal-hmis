<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Models\Visit;
use Illuminate\Http\Request;

class VisitsController extends Controller
{
    public function index(Request $request)
    {
        $query = Visit::with(["patient.category"])
            ->whereNotIn("status", [
                Status::cancelled->value,
                Status::completed->value,
                Status::ejected->value,
            ])->latest();

        return $this->dataTable($request, $query, [
            function ($query, $search) {
                $query->whereHas("patient", function ($q) use ($search) {
                    $q->where("name", "like", "%{$search}%");
                });
            },
        ]);
    }
}
