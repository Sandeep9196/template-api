<?php

namespace App\Services;

use App\Jobs\UpdateAdminDashboardMissingKey;
use App\Models\AddToCart;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

class UtilityService
{
    public function getStorageFile($request)
    {
        try {
            $path = storage_path().'/'.$request->path;
            $content = file_get_contents($path);

            return $content;

        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }
    public function getMedia($request)
    {
        try {
            $path = storage_path() . '/app/' . $request->path;
            $resolution = 100;

            if (is_numeric($request->resolution))
                $resolution = $request->resolution;

            if ($resolution < 30)
                $resolution = 30;
            else if ($resolution > 100)
                $resolution = 100;

            if ($request->segment(4) == 'banners'){
                $resolution = 100;
            }
            if (file_exists($path)) {
                if ($resolution == 100) {
                    $file = File::get($path);
                    $type = File::mimeType($path);
                    $response = Response::make($file, 200);
                    $response->header("Content-Type", $type);
                    return $response;
                }
                return resizeImage($path, $resolution);
            }

        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function logMissingKey($request)
    {
        try {
            $locale = 'en';
            if (in_array($request->locale, ['en', 'ch', 'kh']))
                $locale = $request->locale;
            $newData = json_decode($request->data) ?? [];

            $file = storage_path("app/missing-$locale.json");
            $job = new UpdateAdminDashboardMissingKey($locale, $newData, $file);
            $job->delay(Carbon::now()->addSecond(2))->dispatch($locale, $newData, $file);

            return response()->json("updated missing key: " . json_encode($request->data), 200);

        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function callArtisan($command)
    {
        try {
            Artisan::call($command);
            print_r('runned '.$command);
        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }


}
