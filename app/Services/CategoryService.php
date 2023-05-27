<?php

namespace App\Services;

use App\Imports\CategoriesImport;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class CategoryService
{
    public function paginate($request): JsonResponse
    {
        try {
            Session::put("query_promotions_session", true);
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Category())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
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

            $results = $query->select('categories.*')->with('image:id,path,fileable_id', 'translates')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }
    public function paginateApi($request): JsonResponse
    {
        try {
            Session::put("query_promotions_session", true);
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Category())->newQuery()->orderBy($sortBy, $sortOrder);
            $query->where('status', 'active');
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

            $results = $query->select('categories.*')->with('image:id,path,fileable_id', 'translates')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }
    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {

                $translateModels = array();
                $category = Category::create($data);
                //save name translation
                saveTranslation($category, 'translates', @$data['translation_name'], 'name');

                //save description translation
                saveTranslation($category, 'translates', @$data['translation_desc'], 'description');

                //saving image data
                saveFiles($category, 'image', @$data['file']);
            });

            return response()->json([
                'messages' => ['Category created successfully'],
            ], 201);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }

    public function update($category, array $data): JsonResponse
    {
        try {

            if ($data['status'] == 'inactive') {
                $product = Product::whereCategoryId($category->id)->whereStatus('active')->count();
                if ($product != 0) {
                    return response()->json([
                        'messages' => ['Category already assigned to product'],
                    ], 200);
                }
            }

            DB::transaction(function () use ($category, $data) {
                $category->update($data);
                //save name translation
                saveTranslation($category, 'translates', @$data['translation_name'], 'name');

                //save description translation
                saveTranslation($category, 'translates', @$data['translation_desc'], 'description');
                //update image data
                saveFiles($category, 'image', @$data['file']);
            });

            return response()->json([
                'messages' => ['Category updated successfully'],
            ], 200);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }

    public function delete($category): JsonResponse
    {
        try {
            $category->delete();

            return response()->json([
                'messages' => ['Category deleted successfully'],
            ], 200);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }

    public function restore($categoryId): JsonResponse
    {
        try {
            $category = Category::withTrashed()->find($categoryId);
            $category->restore();
            return response()->json([
                'messages' => ['Category restored successfully'],
            ], 200);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }

    public function import($request): JsonResponse
    {
        $import = new CategoriesImport;
        Excel::import($import, $request->file('upload_file'));
        return response()->json([
            'messages' => ['Excel uploaded successfully'],
            'total_success_upload' => $import->totalSuccessRecords,
            'total_fail_upload' => $import->totalFailRecords,
            'not_upload_data' => $import->notUploadData,
        ], 201);
    }
}
