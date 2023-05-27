<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryFormRequest;
use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function __construct(private CountryService $countryService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->countryService->paginate($request);
    }

    public function all()
    {
        return response()->json(Country::all(), 200);
    }

    public function store(CountryFormRequest $request)
    {
        return $this->countryService->store($request->all());
    }

    public function update(CountryFormRequest $request, Country $country)
    {
        return $this->countryService->update($country, $request->all());
    }

    public function delete(Country $country)
    {
        return $this->countryService->delete($country);
    }
    public function get(Country $country)
    {
        return response()->json($country, 200);
    }
}
