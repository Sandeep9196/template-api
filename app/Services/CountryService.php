<?php

namespace App\Services;

use App\Models\Country;
use App\Models\State;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CountryService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = 'updated_at'; //$request->sortBy ?:
            $sortOrder = $request->descending == 'false' ? 'desc' : 'asc';

            $query = (new Country())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });

            $query->when($request->name, function ($query) use ($request) {
                $query->leftjoin("translations","translationable_id","countries.id")
                    ->where('translations.translation', 'like', "%$request->name%");
            })->groupBy("countries.id");

            $results = $query->select('countries.*')->with(['translates'])->with('states')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {
                $country = Country::create($data);
               \Log::debug(gettype( $data['translation_name']));
                saveTranslation($country,'translates',@$data['translation_name'],'name');
            });

            return response()->json([
                'messages' => ['Country created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($country, array $data): JsonResponse
    {
        try {
                $data['updated_at'] = Carbon::now();
                $country->update($data);
                saveTranslation($country,'translates',@$data['translation_name'],'name');

            return response()->json([
                'messages' => ['Country updated successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($country): JsonResponse
    {
        try {
            $country->delete();

            return response()->json([
                'messages' => ['Country deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

}
