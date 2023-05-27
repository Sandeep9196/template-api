<?php

namespace App\Http\Controllers;

use App\Http\Requests\StateFormRequest;
use App\Models\State;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->response()->json([]);
    }

    public function dashboard()
    {
        return $this->reportService->dashboard();
    }

    public function store(StateFormRequest $request)
    {
        return $this->reportService->store($request->all());
    }

    public function update(StateFormRequest $request, State $state)
    {
        return $this->reportService->update($state, $request->all());
    }

    public function delete(State $state)
    {
        return $this->reportService->delete($state);
    }
}
