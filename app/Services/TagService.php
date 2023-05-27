<?php

namespace App\Services;

use App\Models\File;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TagService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Tag())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });
            $query->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', "%$request->name%")
                        ->orWhere('slug', 'like', "%$request->name%");
            });

            $results = $query->select('tags.*')->with('translates')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {
                $tag = Tag::create($data);

                saveFiles($tag,'image',@$data['image']);
                saveTranslation($tag,'translates',@$data['translation_name'],'name');

            });

            return response()->json([
                'messages' => ['Tag created successfully'],
            ], 201);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function update($tag, array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($tag, $data) {
                $tag->update($data);
                 saveTranslation($tag,'translates',@$data['translation_name'],'name');
                //update image data
                saveFiles($tag,'image',@$data['image']);

            });

            return response()->json([
                'messages' => ['Tag updated successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($tag): JsonResponse
    {
        try {
            $tag->slug = time();
            $tag->update();
            $tag->delete();

            return response()->json([
                'messages' => ['Tag deleted successfully'],
            ], 200);
        } catch (\Exception$e) {
            return generalErrorResponse($e);
        }
    }

    public function deleteImage($id)
    {
        try {
            deleteImage($id);
            return response()->json('Delete Successful', 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

}
