<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductFormRequest;
use App\Http\Requests\ProductImportRequest;
use App\Models\File;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\Request;



class ProductController extends Controller
{
    public function __construct(private ProductService $productService)
    {
    }

    public function paginate(Request $request)
    {
        return $this->productService->paginate($request);
    }
    public function paginateApi(Request $request)
    {
        return $this->productService->paginateApi($request);
    }

    public function index(Request $request)
    {
        return $this->productService->index($request);
    }

    public function all()
    {
        return response()->json(Product::all(), 200);
    }
    public function allApi()
    {
        return response()->json(Product::where('status', 'active')->get(), 200);
    }

    public function store(ProductFormRequest $request)
    {
        return $this->productService->store($request->all());
    }

    public function uploadImages(Request $request, Product $product)
    {
        $this->validate($request, [
            'files' => 'required|max:2048',
        ]);
        return $this->productService->uploadImagesMy($product, $request->file('files'), $request->id);
    }


    public function update(ProductFormRequest $request, Product $product)
    {
        return $this->productService->update($product, $request->all());
    }

    public function delete(Product $product)
    {
        return $this->productService->delete($product);
    }

    public function get(Request $request)
    {
        $product = Product::whereId($request->segment(3))->orWhere('slug', 'like', '%' . $request->segment(3) . '%')->first();
        $product->update(['views' => $product->views + 1]);
        return response()->json($product, 200);
    }
    public function upload(ProductImportRequest $request)
    {
        return $this->productService->import($request);
    }

    public function getByCategorySlug(Request $request, $slug)
    {
        return $this->productService->getByCategorySlug($request, $slug);
    }

    public function deleteImages(Request $request)
    {
        return $this->productService->deleteImages($request->id);
    }

    public function productSetting(Request $request)
    {
        return $this->productService->productSetting($request->all(), $request->id);
    }

    public function productSatus(Request $request)
    {
        return $this->productService->productSatus($request->all(), $request->id);
    }
}
