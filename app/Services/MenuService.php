<?php

namespace App\Services;

use App\Models\Menu;
use App\Models\File;
use App\Models\Group;
use App\Models\Language;
use App\Models\Translation;
use App\Models\Type;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MenuService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Menu())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });
            $query->when($request->search, function ($query) use ($request) {
                $query->where('slug', 'like', "%$request->search%")
                    ->orWhereHas('translations', function ($query) use ($request) {
                        $query->where('translation', 'like', "%$request->search%");
                    });
            });

            $results = $query->select('menus.*')->with('type','group')->paginate($perPage, ['*'], 'page', $page);

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

            $query = (new Menu())->newQuery()->orderBy($sortBy, $sortOrder);
                $query->where('status', 'active');

            $query->when($request->search, function ($query) use ($request) {
                $query->where('slug', 'like', "%$request->search%")
                    ->orWhereHas('translations', function ($query) use ($request) {
                        $query->where('translation', 'like', "%$request->search%");
                    });
            });

            $results = $query->select('menus.*')->with('type','group')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {

                $menu = Menu::create($data);
                //saving transaction
                saveTranslation($menu, 'translations', @$data['title'], 'title');
                saveTranslation($menu, 'translations', @$data['description'], 'description');
                saveTranslation($menu, 'translations', @$data['content'], 'content');

                //saving image data

                saveFiles($menu, 'image', @$data['file']);
            });

            return response()->json([
                'messages' => ['Menu created successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function update($menu, array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($menu, $data) {
                $menu->update($data);
                saveTranslation($menu, 'translations', @$data['title'], 'title');
                saveTranslation($menu, 'translations', @$data['description'], 'description');
                saveTranslation($menu, 'translations', @$data['content'], 'content');
                if (isset($data['files'])) {
                    foreach ($data['files'] as $key => $image) {
                        saveFiles($menu, 'image', $image, ['purpose' => $key]);
                    }
                }
            });

            return response()->json([
                'messages' => ['Menu updated successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($banner): JsonResponse
    {
        try {
            $banner->delete();

            return response()->json([
                'messages' => ['Menu deleted successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function restore($bannerId): JsonResponse
    {
        try {
            $category = Menu::withTrashed()->find($bannerId);
            $category->restore();
            return response()->json([
                'messages' => ['Menu restored successfully'],
            ], 200);
        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function storeType(array $data): JsonResponse
    {
        try {
            if (isset($data['id'])) {
                $type = Type::find($data['id']);
                $type->name = $data['name'];
                $type->save();
                saveTranslation($type, 'translations', @$data['name_translations'], 'name');
            } else {
                $type = Type::create($data);
                saveTranslation($type, 'translations', @$data['name_translations'], 'name');
            }
            return response()->json([
                'messages' => [$type],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function storeGroup(array $data): JsonResponse
    {
        // $data += ['type_id' => 1];
        try {

            if (isset($data['id'])) {
                $group = Group::find($data['id']);
                $group->name = $data['name'];
                $group->type_id = $data['type_id'];
                $group->save();
                saveTranslation($group,'translates',@$data['translation_name'],'name');

                return response()->json([
                    'messages' => [$group],
                ], 200);
            } else {
                $group = Group::create($data);
                saveTranslation($group,'translates',@$data['translation_name'],'name');
                return response()->json([
                    'messages' => [$group],
                ], 201);
            }
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }

    }

    public function get($request)
    {
        try {
            $query = Menu::where('status','active');

            $query->when($request->search, function ($query) use ($request) {
                $query->where('slug', 'like', "%$request->search%")
                    ->orWhereHas('translations', function ($query) use ($request) {
                        $query->where('translation', 'like', "%$request->search%");

                    });
            });

            $results = $query->select('menus.*')->with('type','group')->first();

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }

    }
}
