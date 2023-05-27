<?php

namespace App\Http\Controllers;

use App\Http\Requests\StateFormRequest;
use App\Models\State;
use App\Services\StateService;
use Illuminate\Http\Request;

class StateController extends Controller
{
    public function __construct(private StateService $stateService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->stateService->paginate($request);
    }

    public function all()
    {
        return response()->json(State::all(), 200);
    }

    public function store(StateFormRequest $request)
    {
        return $this->stateService->store($request->all());
    }

    public function update(StateFormRequest $request, State $state)
    {
        return $this->stateService->update($state, $request->all());
    }

    public function delete(State $state)
    {
        return $this->stateService->delete($state);
    }
    public function getById($countryId)
    {
        return response()->json(State::where('country_id', $countryId)->where('status','active')->get(), 200);
    }


    public function getByCountryId(Request $request)
    {
        return $this->stateService->getByCountryId($request->id);
    }
}
