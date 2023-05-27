<?php

namespace App\Http\Controllers;

use App\Http\Requests\CityFormRequest;
use App\Models\City;
use App\Services\CityService;
use App\Jobs\ClearDeals;

use Illuminate\Http\Request;

class CityController extends Controller
{
    public function __construct(private CityService $cityService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->cityService->paginate($request);
    }

    public function all()
    {
        return response()->json(City::all(), 200);
    }
    public function getByStateId(Request $request)
    {
        return response()->json(City::where('state_id', $request->stateId)->where('status','active')->get(), 200);
    }

    public function store(CityFormRequest $request)
    {
        return $this->cityService->store($request->all());
    }

    public function update(CityFormRequest $request, City $state)
    {
        return $this->cityService->update($state, $request->all());
    }

    public function delete(City $state)
    {
        return $this->cityService->delete($state);
    }
}

