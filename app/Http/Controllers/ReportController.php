<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReportFilterRequest;
use App\Models\Category;
use App\Services\ReportData;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    /** The reports page: a React island that renders its first paint from this payload. */
    public function __invoke(ReportFilterRequest $request, ReportData $reports)
    {
        $filters = $request->validated();

        $initial = [
            'filters' => $filters,
            'categories' => Category::orderBy('name')->get(['id', 'name', 'color']),
            'data' => $reports->for($request->user(), $filters['date_from'], $filters['date_to'], $filters['categories']),
        ];

        return view('reports.index', compact('initial'));
    }

    /** JSON for filter changes on the reports page. */
    public function data(ReportFilterRequest $request, ReportData $reports): JsonResponse
    {
        $filters = $request->validated();

        return response()->json(
            $reports->for($request->user(), $filters['date_from'], $filters['date_to'], $filters['categories'])
        );
    }
}
