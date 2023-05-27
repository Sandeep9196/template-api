<?php

namespace App\Http\Controllers;

use App\Models\AddToCart;
use Illuminate\Http\Request;

use App\Services\UtilityService;

class UtilityController extends Controller
{
    public function __construct(private UtilityService $utilityService)
    {
    }

    public function getStorageFile(Request $request)
    {
        return $this->utilityService->getStorageFile($request);
    }

    public function getMedia(Request $request)
    {
        return $this->utilityService->getMedia($request);
    }

    public function logMissingKey(Request $request)
    {
        return $this->utilityService->logMissingKey($request);
    }
    public function callArtisan($command)
    {
        return $this->utilityService->callArtisan($command);
    }

    
}
