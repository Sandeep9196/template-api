<?php

namespace App\Http\Controllers;
use App\Services\HomepageService;
use Illuminate\Http\Request;

class HomepageController extends Controller
{
    public function __construct(private HomepageService $homepageService)
    {
    }

    public function index(Request $request)
    {
        return $this->homepageService->index($request);
    }


    public function getSearchResult(Request $request)
    {
        return $this->homepageService->getSearchResult($request);
    }
    public function getPromotional(Request $request)
    {
        return $this->homepageService->getPromotional($request);
    }

}
