<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Currency;
use App\Models\homePage;
use App\Models\Language;
use App\Models\Product;
use App\Models\Promotion;
use App\Models\SubCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use stdClass;

class HomepageService
{
    public function index($request): JsonResponse
    {
        try {
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';

            $data['category'] = Category::with('subCategories')->orderBy('name', 'asc')->get();
            $data['languages'] = Language::orderBy('name', 'asc')->get();
            $data['currency'] = Currency::orderBy('id', 'asc')->get();
            $data['top_deals'] = Product::whereHas('promotion', function ($query) {
                $query->where('product_promotion.id', 2);
            })->select('products.*')->inRandomOrder()->limit(15)->orderBy($sortBy, $sortOrder)->get();
            $data['hot_deals'] = Product::leftJoin('product_promotion', 'products.id', 'product_promotion.product_id')
                ->where('product_promotion.id', '1')->select('products.*')->inRandomOrder()->limit(15)->orderBy($sortBy, $sortOrder)->get();
            $data['trending_now'] = Product::leftJoin('product_promotion', 'products.id', 'product_promotion.product_id')
                ->where('product_promotion.id', '3')->select('products.*')->inRandomOrder()->limit(15)->orderBy($sortBy, $sortOrder)->get();
            $data['latest'] = Product::orderBy('created_at', 'desc')->select('products.*')->limit(15)->orderBy($sortBy, $sortOrder)->get();

            return response()->json($data, 200);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }

    public function getSearchResult($request): JsonResponse
    {
        try {
            if (!isset($request->type)) {
                $request->type = "product";
            }
            $data = [];
            $categories = [];
            switch ($request->type) {
                case 'category':
                    $query = (new Category())->newQuery();
                    $query->when($request->search_key, function ($q) use ($request) {
                        $q->leftJoin('translations', 'translations.translationable_id', '=', 'categories.id')
                            ->leftJoin('sub_categories', 'sub_categories.category_id', '=', 'categories.id')
                            ->where(function ($q) {
                                $q->where('translations.translationable_type', 'App\Models\Category')
                                    ->orWhere('translations.translationable_type', 'App\Models\SubCategory');
                            })
                            ->where(function ($q) use ($request) {
                                $q->orWhere('translations.translation', 'like', "%$request->search_key%")
                                    ->orWhere('categories.name', 'like', "%$request->search_key%")
                                    ->orWhere('categories.slug', 'like', "%$request->search_key%")
                                    ->orWhere('categories.description', 'like', "%$request->search_key%");
                            })->groupBy('categories.id');
                    });
                    $data = $query->with('subCategories')->select('categories.*')->get();

                    break;

                case 'product':
                    $query = (new Product())->newQuery();
                    $query->when($request->search_key, function ($q) use ($request) {
                        $q->leftJoin('translations', 'translations.translationable_id', '=', 'products.id')
                            ->where('translations.translationable_type', 'App\Models\Product')
                            ->where(function ($q) use ($request) {
                                $q->orWhere('translations.translation', 'like', "%$request->search_key%")
                                    ->orWhere('products.sku', 'like', "%$request->search_key%");
                            })->groupBy('products.id');
                    });
                    $data = $query->select('products.*')->get();
                     //query category and sub category
                     if ($request->search_key) {
                        $query = (new Category())->newQuery();
                        $query->where(function ($q) use ($request) {
                            $q->whereHas('translates', function ($q) use ($request) {
                                $q->where('translation', 'like', "%$request->search_key%");
                            });
                        });
                        $categories = $query->select('categories.*')->where('status','active')->get()->toArray();

                        $query = (new SubCategory())->newQuery();
                        $query->where(function ($q) use ($request) {
                            $q->whereHas('translates', function ($q) use ($request) {
                                $q->where('translation', 'like', "%$request->search_key%");
                            });
                        });
                        $subCategories = $query->select('sub_categories.*')->where('status','active')->get()->toArray();
                        $categories = array_merge($categories, $subCategories);
                    }
                    break;
            }

            return response()->json([
                'data' => $data,
                'category_data' => $categories,
            ], 200);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }
    public function getPromotional($request)
    {
        try {
            $data = [];
            Session::put("filter_slot_by_status", true);
            Session::put("promotional_query_session", true);
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';
            $promotions = Promotion::with('translation')->whereStatus('active')->paginate($perPage, ['*'], 'page', $page);
            foreach ($promotions as $promotion) {
                $langData = !empty($promotion->translation) ? $this->getTranslation($promotion->translation->toArray(), ['name']) : new stdClass();
                $data[] = [
                    'name'      =>     $promotion->name,
                    'products'  =>     $this->getData($sortBy, $sortOrder, $promotion->slug),
                    'slug'      =>     $promotion->slug,
                    'image'      =>    $promotion->image,
                    'translation' =>   $langData
                ];
            }
            return response()->json(['promotional' => $data], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }
    public function getTranslation($data, $reqData)
    {
        $lanuageData = array();
        foreach ($reqData as $n) {
            foreach ($data as $dt) {
                if (array_key_exists('translation', $dt));
                $lanuageData[$n] = $dt['translation'];
            }
        }


        return  !empty($lanuageData) ? $lanuageData : new stdClass();
    }
    public function getData($sortBy, $sortOrder, $slug)
    {
        Session::put("query_promotions_session", true);
        $products =  Product::whereStatus('active')->with(['productImage:id,path,fileable_id'])->whereHas('promotion', function ($query) use ($slug) {
            $query->where('promotions.slug', $slug);
        })->select('products.*')->inRandomOrder()->limit(8)->orderBy($sortBy, $sortOrder)->get();
        $promotionalQuery = Session::get("promotional_query_session");
        if ($promotionalQuery)
            $products->makeHidden(['image','deleted_at', "category", "category_id", "subCategory", "sub_category_id", 'favourite_count', 'promotion', 'favouriteCount', "translation.description",'prices']);
        return $products;
    }
}
