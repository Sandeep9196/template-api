<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class LanguageService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Language())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', "%$request->name%");
                $query->orWhere('locale', 'like', "%$request->name%");
            });
            $query->when($request->search_language, function ($query) use ($request) {
                $query->where('name', 'like', "%$request->search_language%");
                $query->orWhere('locale', 'like', "%$request->search_language%");
            });

            $results = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data){
                $language = Language::create($data);

                //save name translation
                saveTranslation($language,'translates',@$data['translation'],'name',true);       
    
            });
            return response()->json([
                'messages' => ['Language created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($language, array $data): JsonResponse
    {
        try {
            $language->update($data);
            //save name translation
            saveTranslation($language,'translates',@$data['translation'],'name',true);       

            return response()->json([
                'messages' => ['Language updated successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($language): JsonResponse
    {
        try {
            $language->delete();

            return response()->json([
                'messages' => ['Language deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }
}
