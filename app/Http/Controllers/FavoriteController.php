<?php

namespace App\Http\Controllers;

use App\Http\Requests\FavoriteFormRequest;
use App\Services\FavoriteService;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(private FavoriteService $favoriteService)
    {
    }

    public function list(Request $request, $customer_id=null)
    {
        return $this->favoriteService->list($request,$customer_id);
    }

    public function addToFavorites(Request $request)
    {
        return $this->favoriteService->addToFavorites($request->all());
    }
    public function removeFromFavorites(FavoriteFormRequest $request)
    {
        return $this->favoriteService->removeFromFavorites($request->all());
    }
}
