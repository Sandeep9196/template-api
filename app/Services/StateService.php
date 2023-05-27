<?php

namespace App\Services;

use App\Models\State;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class StateService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = 'updated_at'; //$request->sortBy ?:
            $sortOrder = $request->descending == 'false' ? 'desc' : 'asc';

            $query = (new State())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });

            $query->when($request->state_name, function ($query) use ($request) {
                $query->leftjoin("translations","translationable_id","states.id")
                    ->where('translations.translation', 'like', "%$request->state_name%");
            })->groupBy("states.id");


            $results = $query->select('states.*')->with(['translates'])->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {
                $state = State::create($data);
                saveTranslation($state,'translates',@$data['translation_name'],'name');
            });

            return response()->json([
                'messages' => ['State created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($state, array $data): JsonResponse
    {
        try {

                $state->update($data);
                saveTranslation($state,'translates',@$data['translation_name'],'name');

            return response()->json([
                'messages' => ['State updated successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($state): JsonResponse
    {
        try {
            $state->delete();

            return response()->json([
                'messages' => ['State deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function getByCountryId($id): JsonResponse
    {
        try {
            $results = State::where('country_id',$id)->get();
            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }


}
