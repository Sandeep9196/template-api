<?php

namespace App\Services;

use App\Models\City;
use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class CityService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = 'updated_at';
            $sortOrder = $request->descending == 'false' ? 'desc' : 'asc';

            $query = (new City())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });

            $query->when($request->city_name, function ($query) use ($request) {
                $query->leftjoin("translations","translationable_id","cities.id")
                ->where('translations.translation', 'like', "%$request->city_name%");
            })->groupBy("cities.id");

            $query->where(function ($q) use ($request) {
                $q->whereHas('state', function ($q) use ($request) {
                    $q->leftjoin("translations","translationable_id","states.id")
                    ->where('translations.translation', 'like', "%$request->state_name%");
                });
            })->groupBy("cities.id");

            $results = $query->select('cities.*')->with('country', 'state')->with(['translates'])->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {
                $city = City::create($data);
                \Log::debug(gettype( $data['translation_name']));
                saveTranslation($city,'translates',@$data['translation_name'],'name');
            });

            return response()->json([
                'messages' => ['City created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($city, array $data): JsonResponse
    {
        try {

            City::find($data['id'])->update($data);
            $city1 = City::find($data['id']);
            saveTranslation($city1,'translates',@$data['translation_name'],'name');
            return response()->json([
                'messages' => ['City updated successfully', $city1],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }


    public function delete($city): JsonResponse
    {
        try {
            $city->delete();

            return response()->json([
                'messages' => ['City deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

}
