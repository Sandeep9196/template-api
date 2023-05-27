<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryFormRequest;
use App\Http\Requests\CategoryImportRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use App\Imports\CategoriesImport;
use App\Models\Product;
use Maatwebsite\Excel\Facades\Excel;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->categoryService->paginate($request);
    }
    public function paginateApi(Request $request)
    {
        return $this->categoryService->paginateApi($request);
    }

    public function all()
    {
        return response()->json(Category::all(), 200);
    }
    public function allApi()
    {
        return response()->json(Category::where('status','active')->get(), 200);
    }
    public function get(Category $category)
    {
        return response()->json($category, 200);
    }

    public function treeView()
    {
        return response()->json(Category::where('status','active')->with('subCategories')->get(), 200);
    }

    public function store(CategoryFormRequest $request)
    {

        return $this->categoryService->store($request->all());
    }

    public function update(CategoryFormRequest $request, Category $category)
    {
        return $this->categoryService->update($category, $request->all());
    }

    public function delete(Category $category)
    {
        return $this->categoryService->delete($category);
    }

    public function upload(CategoryImportRequest $request)
    {
        return $this->categoryService->import($request);
    }

    public function restore($categoryId)
    {
        return $this->categoryService->restore($categoryId);
    }

    public function usingInProduct(Request $request)
    {
        $cat = Product::where('category_id', $request->id)->first();
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
