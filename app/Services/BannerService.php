<?php

namespace App\Services;

use App\Models\Banner;
use App\Models\File;
use App\Models\Language;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class BannerService
{
    public function paginate($request): JsonResponse
    {
        try {
            Session::put("query_promotions_session",true);
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Banner())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });
            $query->when($request->search, function ($query) use ($request) {
                $query->where('slug', 'like', "%$request->search%")
                        ->orWhereHas('translations',function($query) use ($request) {
                            $query->where('translation', 'like', "%$request->search%");
                        });
                $query->orWhere('type', 'like', "%$request->search%");
                $query->orWhere('link', 'like', "%$request->search%");
                $query->orWhere('position', 'like', "%$request->search%");

            });

            $results = $query->select('banners.*')->with('image')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }
    public function paginateApi($request): JsonResponse
    {
        try {
            Session::put("query_promotions_session",true);
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Banner())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });
            $query->when($request->search, function ($query) use ($request) {
                $query->where('slug', 'like', "%$request->search%")
                        ->orWhereHas('translations',function($query) use ($request) {
                            $query->where('translation', 'like', "%$request->search%");
                        });
            });

            $results = $query->select('banners.*')->with('images:id,path,fileable_id')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {

                $banner = Banner::create($data);
              //save name translation
              saveTranslation($banner,'translations',@$data['translation_name'],'name');
              //save description translation
              saveTranslation($banner,'translations',@$data['translation_desc'],'description');
              if(isset($data['files'])) {
                  foreach ($data['files'] as $key => $image) {
                      saveFiles($banner,'image', $image, ['purpose' => $key]);
                  }
              }

            });

            return response()->json([
                'messages' => ['Banner created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($banner, array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($banner, $data) {

                $banner->update($data);
                //save name translation
                saveTranslation($banner,'translations',@$data['translation_name'],'name');
                //save description translation
                saveTranslation($banner,'translations',@$data['translation_desc'],'description');
                if(isset($data['files'])) {
                    foreach ($data['files'] as $key => $image) {
                        saveFiles($banner,'image', $image, ['purpose' => $key]);
                    }
                }
            });

            return response()->json([
                'messages' => ['Banner updated successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($banner): JsonResponse
    {
        try {
            $banner->delete();

            return response()->json([
                'messages' => ['Banner deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function restore($bannerId): JsonResponse
    {
        try {
            $category = Banner::withTrashed()->find($bannerId);
            $category->restore();
            return response()->json([
                'messages' => ['Banner restored successfully'],
            ], 200);
        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

}
