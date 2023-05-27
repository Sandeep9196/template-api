<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Promotion;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PromotionService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Promotion())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });

            $query->when($request->name, function ($q) use ($request) {
                $q->whereHas('translates', function ($q) use ($request) {
                    $q->where('translation', 'like', "%$request->name%");
                })
                ->orWhere('slug', 'like', "%$request->name%");
            });

            $results = $query->with(['translates'])->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {
                $promotion = Promotion::create($data);
                saveFiles($promotion,'image',@$data['image']);
                saveTranslation($promotion,'translates',@$data['translation_name'],'name');
            });


            return response()->json([
                'messages' => ['Promotion created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($data, $promotion): JsonResponse
    {
        try {
            $promo = Promotion::find($data['id']);
            $promo->update($data);
            saveFiles($promo,'image',@$data['image']);
            saveTranslation($promo,'translates',@$data['translation_name'],'name');
            return response()->json([
                'messages' => [$data],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($promotion): JsonResponse
    {
        try {
            $promotion->slug = time();
            $promotion->update();
            $promotion->delete();

            return response()->json([
                'messages' => ['Promotion deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

}
