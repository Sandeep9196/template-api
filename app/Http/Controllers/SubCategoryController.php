<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubCategoryFormRequest;
use App\Models\Product;
use App\Models\SubCategory;
use App\Services\SubCategoryService;
use Illuminate\Http\Request;

class SubCategoryController extends Controller
{
    public function __construct(private SubCategoryService $subCategoryService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->subCategoryService->paginate($request);
    }
    public function paginateApi(Request $request)
    {
        return $this->subCategoryService->paginateApi($request);
    }

    public function all()
    {
        return response()->json(SubCategory::all(), 200);
    }
    public function allApi()
    {
        return response()->json(SubCategory::where('status','active')->get(), 200);
    }

    public function store(SubCategoryFormRequest $request)
    {

        return $this->subCategoryService->store($request->all());
    }

    public function get(Request $request, SubCategory $subCategory)
    {
        return response()->json(SubCategory::whereId($subCategory->id)->with('subCategories')->firstOrFail(), 200);
    }

    public function getByCategoryID(Request $request, SubCategory $subCategory)
    {
        return response()->json(SubCategory::where('category_id', $request->id)->get(), 200);
    }

    public function update(SubCategoryFormRequest $request, SubCategory $subCategory)
    {
        return $this->subCategoryService->update($subCategory, $request->all());
    }

    public function delete(SubCategory $subCategory)
    {
        return $this->subCategoryService->delete($subCategory);
    }

    public function usingInProduct(Request $request)
    {
        $cat = Product::where('sub_category_id', $request->id)->first();
        if($cat){
            return response()->json([
                'status' => true,
            ], 200);
        } else {
            return response()->json([
                'status' => false,
            ], 200);
        }

    }
}
