<?php

namespace App\Services;

use App\Models\File;
use App\Models\Template;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TemplateService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Template())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });
            $query->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', "%$request->name%");

            });
            $results = $query->with(['image:id,path,fileable_id'])->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($data) {
                $template = Template::create($data);
                if (!empty($data['image'])) {

                    saveFiles($template, 'image', $data['image']);
                }
                if (!empty($data['translation_name'])) {
                    saveTranslation($template, 'translates', $data['translation_name'], 'name');
                }
            });

            return response()->json([
                'messages' => ['Template created successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function update($template, array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($template, $data) {
                $template->update($data);
                if (!empty($data['image'])) {
                    File::where(['fileable_id' => $template->id,'fileable_type'=>Template::class])->delete();
                    saveFiles($template, 'image', $data['image']);
                }
                if (!empty($data['translation_name'])) {
                    saveTranslation($template, 'translates', $data['translation_name'], 'name');
                }
            });

            return response()->json([
                'messages' => ['Template updated successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($template): JsonResponse
    {
        try {
            $template->delete();

            return response()->json([
                'messages' => ['Template deleted successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
}
