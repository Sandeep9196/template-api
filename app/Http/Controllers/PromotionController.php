<?php

namespace App\Http\Controllers;

use App\Http\Requests\PromotionFormRequest;
use App\Models\Promotion;
use App\Services\PromotionService;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    public function __construct(private PromotionService $promotionService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->promotionService->paginate($request);
    }

    public function all()
    {
        return response()->json(Promotion::all(), 200);
    }

    public function store(PromotionFormRequest $request)
    {
        return $this->promotionService->store($request->all());
    }

    public function update(Request $request, Promotion $promotion)
    {
        return $this->promotionService->update($request->all(), $promotion);
    }

    public function delete(Promotion $promotion)
    {
        return $this->promotionService->delete($promotion);
    }
}
