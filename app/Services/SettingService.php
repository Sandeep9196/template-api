<?php

namespace App\Services;

use App\Models\Bot;
use App\Models\Configure;
use App\Models\File;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SettingService
{
    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $query = (new Setting())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->name, function ($query) use ($request) {
                $query->where('name', 'like', "%$request->name%");
            });

            $query->where('status', 'Active');

            $results = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }


    public function store(array $data): JsonResponse
    {
        try {
            $isUpdate = false;
            DB::transaction(function () use ($data, &$isUpdate) {
                $setting = $existing = Setting::first();
                if ($existing) {
                    $existing->update($data);
                    $fileFields = [
                        'logo',
                        'instagram_icon',
                        'youtube_icon',
                        'facebook_icon',
                        'pinterest_icon',
                        'twitter_icon',
                        'qq_icon',
                        'skype_icon',
                        'telegram_icon',
                        'whatsapp_icon'
                    ];
                    $isUpdate = true;
                } else
                    $setting = Setting::create($data);

                //add translations
                saveTranslation($setting, 'translations', @$data['translation']);
            });
            if ($isUpdate)
                return response()->json([
                    'messages' => ['Setting updated successfully'],
                ], 201);
            return response()->json([
                'messages' => ['Setting created successfully'],
            ], 201);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function update($setting, array $data): JsonResponse
    {
        try {
            $setting->update($data);

            return response()->json([
                'messages' => ['Setting updated successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function delete($setting): JsonResponse
    {
        try {
            $setting->delete();

            return response()->json([
                'messages' => ['Setting deleted successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
}
