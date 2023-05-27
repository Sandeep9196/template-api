<?php

namespace App\Services;

use App\Models\File;
use App\Models\Language;
use App\Models\SubCategory;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SubCategoryService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';
            if ($sortBy == 'categoryName') {
                $sortBy = 'categories.name';
            }

            $query = (new SubCategory())->newQuery()->orderBy($sortBy, $sortOrder);
            if ($sortBy == 'categories.name') {
                $query->leftJoin('categories', 'categories.id', 'sub_categories.category_id');
            }

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });
            $query->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            });

            $query->when($request->sub_category_id, function ($query) use ($request) {
                $query->where('sub_categories.id', $request->sub_category_id);
            });
            $query->when($request->search, function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', "%$request->search%")
                        ->orWhere('slug', 'like', "%$request->search%")
                        ->orWhere('description', 'like', "%$request->search%")
                        ->orWhereHas('translates', function ($query) use ($request) {
                            $query->where('translation', 'like', "%$request->search%");
                        });
                });
            });

            $results = $query->select('sub_categories.*')->with('image:id,path,fileable_id', 'category', 'subCategories')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function paginateApi($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';
            if ($sortBy == 'categoryName') {
                $sortBy = 'categories.name';
            }

            $query = (new SubCategory())->newQuery()->orderBy($sortBy, $sortOrder);
            if ($sortBy == 'categories.name') {
                $query->leftJoin('categories', 'categories.id', 'sub_categories.category_id');
            }
            $query->where('status', 'active');

            $query->when($request->category_id, function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            });
            $query->when($request->sub_category_id, function ($query) use ($request) {
                $query->where('parent_sub_category_id', $request->sub_category_id);
            });
            $query->when($request->search, function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'like', "%$request->search%")
                        ->orWhere('slug', 'like', "%$request->search%")
                        ->orWhere('description', 'like', "%$request->search%")
                        ->orWhereHas('translates', function ($query) use ($request) {
                            $query->where('translation', 'like', "%$request->search%");
                        });
                });
            });
            $results = $query->select('sub_categories.*')->with('image:id,path,fileable_id', 'category', 'subCategories')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {
                $subCategory = SubCategory::create($data);
                //save name translation
                saveTranslation($subCategory, 'translates', @$data['translation_name'], 'name');
                //save description translation
                saveTranslation($subCategory, 'translates', @$data['translation_desc'], 'description');
                //saving image data
                saveFiles($subCategory, 'image', @$data['file']);
            });

            return response()->json([
                'messages' => ['SubCategory created successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function update($subCategory, array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($subCategory, $data) {
                $subCategory->update($data);
                //save name translation
                saveTranslation($subCategory, 'translates', @$data['translation_name'], 'name');
                //save description translation
                saveTranslation($subCategory, 'translates', @$data['translation_desc'], 'description');
                //saving image data
                saveFiles($subCategory, 'image', @$data['file']);
            });

            return response()->json([
                'messages' => ['SubCategory updated successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($subCategory): JsonResponse
    {
        try {
            $subCategory->delete();

            return response()->json([
                'messages' => ['SubCategory deleted successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
}
