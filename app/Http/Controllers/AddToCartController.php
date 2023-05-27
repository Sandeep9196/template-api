<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddToCartRequest;
use App\Models\AddToCart;
use Illuminate\Http\Request;

use App\Services\AddToCartService;

class AddToCartController extends Controller
{
    public function __construct(private AddToCartService $addToCartService)
    {
    }

    public function addToCart(AddToCartRequest $request)
    {
        return $this->addToCartService->addToCart($request->all());
    }

    public function update(Request $request, AddToCart $addToCart)
    {
       
    }
}
