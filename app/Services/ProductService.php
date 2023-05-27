<?php

namespace App\Services;

use App\Imports\ProductsImport;
use App\Models\Configure;
use App\Models\Deal;
use App\Models\File;
use App\Models\Inventory;
use App\Models\Language;
use App\Models\Product;
use App\Models\ProductCurrency;
use App\Models\Promotion;
use App\Models\Rating;
use App\Models\Slot;
use App\Models\Tag;
use App\Models\Translation;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use stdClass;

class ProductService
{
    public function index($request): JsonResponse
    {
        try {
            Session::put("query_promotions_session", true);
            $perPage = $request->rowsPerPage ?: 100;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';  //pice,most_view,popular
            $sortOrder = $request->descending == 'true' ? 'desc' : 'asc';
            $isSortByPrice = false;
            $query = Product::distinct('products.id')->where('status','active');

            if ($request->tag) {
                if ($request->tag == 'latest') {
                    $query = Product::orderBy('created_at', 'desc');
                } else {
                    $query->whereHas('promotion', function ($query) use ($request) {
                        $query->where('promotions.slug', $request->tag);
                    });
                }
            }
            if (in_array($sortBy, ['created_at', 'sale_price', 'price', 'most_view', 'popular', 'latest'])) {

                if ($sortBy == 'popular')
                    $sortBy = 'views';
                else if ($sortBy == 'most_view')
                    $sortBy = 'views';
                else if ($sortBy == 'latest')
                    $sortBy = 'created_at';

                if ($request->descending) {
                    $isSortByPrice =  true;
                }
                // $query->orderBy('products.' . $sortBy, 'desc');
            }
            $query->leftJoin('product_currencies', 'product_currencies.product_id', 'products.id');
            $query->when($request->category_id, function ($query) use ($request) {
                $query->whereHas('category', function ($query) use ($request) {
                    $query->whereId($request->category_id);
                });
            });
            $query->when($request->sub_category_id, function ($query) use ($request) {
                $query->whereHas('subCategory', function ($query) use ($request) {
                    $query->whereId($request->sub_category_id);
                });
            });
            $query->when($request->priceMin || $request->priceMax, function ($query) use ($request) {
                $query->whereHas('prices', function ($query) use ($request) {
                    if ($request->priceMin && $request->priceMax)
                        $query->whereBetween('product_currencies.sale_price', [(int)$request->priceMin, (int)$request->priceMax])->where('product_currencies.currency_id', $request->cur_id);
                    else if ($request->priceMin)
                        $query->where('product_currencies.sale_price', '>=', (int)$request->priceMin)->where('product_currencies.currency_id', $request->cur_id);
                    else if ($request->priceMax)
                        $query->where('product_currencies.sale_price', '<=', (int)$request->priceMax)->where('product_currencies.currency_id', $request->cur_id);
                });
            });
            $rawQuery = 'CASE WHEN products.discount = 0
                            THEN product_currencies.sale_price
                            WHEN products.discount is null
                            THEN product_currencies.sale_price
                            ELSE product_currencies.sale_price - ((product_currencies.sale_price*products.discount)/100)
                            END AS price_after_discount';
            if ($isSortByPrice)
                $query->select('products.*', 'product_currencies.sale_price', DB::raw($rawQuery))->distinct('products.id')->orderBy('price_after_discount',$sortOrder)->orderBy($sortBy, 'desc') ;
            else
                $query->select('products.*')->distinct('products.id')->orderBy($sortBy, 'desc');

            $data = $query->with(['image:id,path,fileable_id'])->paginate($perPage, ['*'], 'page', $page);
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }


    public function paginate($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'asc' : 'desc';

            $query = (new Product())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->dates, function ($query) use ($request) {
                if ($request->dates[0] == $request->dates[1]) {
                    $query->whereDate('created_at', Carbon::parse($request->dates[0])->format('Y-m-d'));
                } else {
                    $query->whereBetween('created_at', [
                        Carbon::parse($request->dates[0])->startOfDay(),
                        Carbon::parse($request->dates[1])->endOfDay(),
                    ]);
                }
            });

            $query->when($request->status, function ($query) use ($request) {
                $query->where('status', $request->status);
            });

            $query->when($request->category_id, function ($q) use ($request) {
                $q->whereHas('category', function ($q) use ($request) {
                    $q->where('id', $request->category_id);
                });
            });

            $query->when($request->promotion_id, function ($q) use ($request) {
                $q->whereHas('promotion', function ($q) use ($request) {
                    $q->where('promotions.id', $request->promotion_id);
                });
            });

            $query->when($request->sub_category_id, function ($q) use ($request) {
                $q->whereHas('subCategory', function ($q) use ($request) {
                    $q->where('id', $request->sub_category_id);
                });
            });

            $query->when($request->search, function ($q) use ($request) {
                $q->whereHas('translation', function ($q) use ($request) {
                    $q->where('field_name', 'name')
                        ->where('translation', 'like', "%$request->search%");
                });
            });
            if (request()->segment(2) == ADMIN) {
                $results = $query->select('products.*')->with('category', 'subCategory', 'prices', 'image', 'tags', 'promotion')->paginate($perPage, ['*'], 'page', $page);
                return response()->json($results, 200);
            } else {
                $itemsPaginated = $query->select('products.*')->with('category', 'subCategory', 'prices', 'image:id,path,fileable_id', 'tags', 'inventory', 'promotion')->paginate($perPage, ['*'], 'page', $page);



                $itemsTransformed = $itemsPaginated
                    ->getCollection()
                    ->map(function ($item) {
                        $datas = new stdClass();
                        $datas->id = $item->id;
                        $datas->name = $item->name;
                        $datas->category_id = $item->category_id;
                        $datas->sub_category_id = $item->sub_category_id;
                        $datas->sku = $item->sku;
                        $datas->slug = $item->slug;
                        $datas->quantity = $item->quantity;
                        $datas->discount = $item->discount;
                        $datas->meta_title = $item->meta_title;
                        $datas->meta_description = $item->meta_description;
                        $datas->meta_keywords = $item->meta_keywords;
                        $datas->views = $item->views;
                        $datas->status = $item->status;
                        $datas->created_at = $item->created_at;
                        $datas->updated_at = $item->updated_at;
                        $datas->deleted_at = $item->deleted_at;
                        $price = $item->prices->pluck('sale_price');
                        $datas->prices = !empty($price->toArray()) ? number_format($price->toArray()[0], 2) : '0.00';
                        $datas->image = $item->image;
                        $datas->tags = $item->tags;
                        $datas->inventory = $item->inventory;
                        $datas->promotion = $item->promotion;
                        $datas->category = $item->category;
                        $datas->subCategory = $item->subCategory;
                        $rating = Rating::selectRaw('ROUND(AVG(rate)) as rate')->whereProductId($item->id)->get();
                        $datas->rating = !empty($rating[0]->rate) ? $rating[0]->rate : 0;
                        return  $datas;
                    })->toArray();


                $itemsTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                    $itemsTransformed,
                    $itemsPaginated->total(),
                    $itemsPaginated->perPage(),
                    $itemsPaginated->currentPage(),
                    [
                        'path' => \Request::url(),
                        'query' => [
                            'page' => $itemsPaginated->currentPage()
                        ]
                    ]
                );
                return response()->json($itemsTransformedAndPaginated, 200);
            }
        } catch (\Exception $e) {
            //  \Log::debug($e);
            return generalErrorResponse($e);
        }
    }
    public function paginateApi($request): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;
            $sortBy = $request->sortBy ?: 'created_at';
            $sortOrder = $request->descending == 'true' ? 'asc' : 'desc';

            $query = (new Product())->newQuery()->orderBy($sortBy, $sortOrder);

            $query->when($request->dates, function ($query) use ($request) {
                if ($request->dates[0] == $request->dates[1]) {
                    $query->whereDate('created_at', Carbon::parse($request->dates[0])->format('Y-m-d'));
                } else {
                    $query->whereBetween('created_at', [
                        Carbon::parse($request->dates[0])->startOfDay(),
                        Carbon::parse($request->dates[1])->endOfDay(),
                    ]);
                }
            });


                $query->where('status','active');

            $query->when($request->category_id, function ($q) use ($request) {
                $q->whereHas('category', function ($q) use ($request) {
                    $q->where('id', $request->category_id);
                });
            });

            $query->when($request->promotion_id, function ($q) use ($request) {
                $q->whereHas('promotion', function ($q) use ($request) {
                    $q->where('promotions.id', $request->promotion_id);
                });
            });

            $query->when($request->sub_category_id, function ($q) use ($request) {
                $q->whereHas('subCategory', function ($q) use ($request) {
                    $q->where('id', $request->sub_category_id);
                });
            });

            $query->when($request->search, function ($q) use ($request) {
                $q->whereHas('translation', function ($q) use ($request) {
                    $q->where('field_name', 'name')
                        ->where('translation', 'like', "%$request->search%");
                });
            });
            if (request()->segment(2) == ADMIN) {
                $results = $query->select('products.*')->with('category', 'subCategory', 'prices', 'image', 'tags', 'promotion')->paginate($perPage, ['*'], 'page', $page);
                return response()->json($results, 200);
            } else {
                $itemsPaginated = $query->select('products.*')->with('category', 'subCategory', 'prices', 'image:id,path,fileable_id', 'tags', 'inventory', 'promotion')->paginate($perPage, ['*'], 'page', $page);



                $itemsTransformed = $itemsPaginated
                    ->getCollection()
                    ->map(function ($item) {
                        $datas = new stdClass();
                        $datas->id = $item->id;
                        $datas->name = $item->name;
                        $datas->category_id = $item->category_id;
                        $datas->sub_category_id = $item->sub_category_id;
                        $datas->sku = $item->sku;
                        $datas->slug = $item->slug;
                        $datas->quantity = $item->quantity;
                        $datas->discount = $item->discount;
                        $datas->meta_title = $item->meta_title;
                        $datas->meta_description = $item->meta_description;
                        $datas->meta_keywords = $item->meta_keywords;
                        $datas->views = $item->views;
                        $datas->status = $item->status;
                        $datas->created_at = $item->created_at;
                        $datas->updated_at = $item->updated_at;
                        $datas->deleted_at = $item->deleted_at;
                        $price = $item->prices->pluck('sale_price');
                        $datas->prices = !empty($price->toArray()) ? number_format($price->toArray()[0], 2) : '0.00';
                        $datas->image = $item->image;
                        $datas->tags = $item->tags;
                        $datas->inventory = $item->inventory;
                        $datas->promotion = $item->promotion;
                        $datas->category = $item->category;
                        $datas->subCategory = $item->subCategory;
                        $rating = Rating::selectRaw('ROUND(AVG(rate)) as rate')->whereProductId($item->id)->get();
                        $datas->rating = !empty($rating[0]->rate) ? $rating[0]->rate : 0;
                        return  $datas;
                    })->toArray();


                $itemsTransformedAndPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
                    $itemsTransformed,
                    $itemsPaginated->total(),
                    $itemsPaginated->perPage(),
                    $itemsPaginated->currentPage(),
                    [
                        'path' => \Request::url(),
                        'query' => [
                            'page' => $itemsPaginated->currentPage()
                        ]
                    ]
                );
                return response()->json($itemsTransformedAndPaginated, 200);
            }
        } catch (\Exception $e) {
            //  \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function store(array $data): JsonResponse
    {
        try {

            DB::transaction(function () use ($data) {
                $data['slug'] = generateUniqueSlug(new Product(), $data['slug']);
                $product = Product::create($data);
                $productId = $product->id;
                if (isset($data['images'])) {
                    foreach ($data['images'] as $image) {
                        $path = uploadImage($image, 'products');
                        $file = new File([
                            'path' => $path,
                            'type' => checkFileType($image)
                        ]);
                        $product->image()->save($file);
                    }
                }
                if (isset($data['price'])) {
                    $price['product_id'] = $productId;
                    $price['price'] = $data['price'];
                    $price['currency_id'] = $data['currency_id'];
                    $price['sale_price'] = $data['sale_price'];
                    $price['purchase_price'] = $data['purchase_price'];
                    ProductCurrency::create($price);
                }
                if (isset($data['translation'])) {
                    foreach ($data['translation'] as $translation) {
                        $transData = new Translation([
                            'language_id' => $translation['language_id'],
                            'field_name' => $translation['field_name'],
                            'translation' => $translation['value']
                        ]);
                        $product->translation()->save($transData);
                    }
                }

                if (isset($data['tags'])) {
                    foreach ($data['tags'] as $tag) {
                        DB::table('product_tag')->insert([
                            'product_id' => $productId,
                            'tag_id' =>  $tag,
                        ]);
                    }
                }
                if (isset($data['promotions'])) {
                    foreach ($data['promotions'] as $promotion) {
                        DB::table('product_promotion')->insert([
                            'product_id' => $productId,
                            'promotion_id' => $promotion,
                        ]);
                    }
                }
                //update inventory
                $inventory = Inventory::whereProductId($productId)->first();
                if ($inventory)
                    $inventory->update(['available_stock' => $inventory->available_stock + 1]);
                else
                    Inventory::create([
                        'product_id' => $productId,
                        'available_stock' => 1,
                        'sku' => $data['sku']
                    ]);
            });

            return response()->json([
                'messages' => ['Product created successfully'],
            ], 201);
        } catch (\Exception $e) {
            //  \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function update($product, array $data): JsonResponse
    {
        try {
            DB::transaction(function () use ($product, $data) {

                $product->update($data);
                $product->promotion()->sync($data['promotions']);
                $product->tags()->sync($data['tags']);

                if (isset($data['prices'])) {
                    if (!empty($data['prices'])) {
                        foreach ($data['prices'] as $p) {
                            if ($data['currency_id'] === $p['currency_id']) {
                                $pModel = ProductCurrency::find($p['id']);
                                $pModel->price = $data['price'];
                                $pModel->sale_price = $data['sale_price'];
                                $pModel->purchase_price = $data['purchase_price'];
                                $pModel->update();
                            }
                        }
                    } else {
                        if (isset($data['price'])) {
                            $price['product_id'] = $data['id'];
                            $price['price'] = $data['price'];
                            $price['currency_id'] = $data['currency_id'];
                            $price['sale_price'] = $data['sale_price'];
                            $price['purchase_price'] = $data['purchase_price'];
                            ProductCurrency::create($price);
                        }
                    }
                }


                foreach ($data['translation'] as $trans) {
                    if (isset($trans['id'])) {
                        $model = Translation::find($trans['id']);
                        $model->translation = $trans['value'];
                        $model->field_name = $trans['field_name'];
                        $model->update();
                    } else {
                        $transData = new Translation([
                            'language_id' => $trans['language_id'],
                            'field_name' => $trans['field_name'],
                            'translation' => $trans['value']
                        ]);
                        $product->translation()->save($transData);
                    }
                }
            });

            return response()->json([
                'messages' => ['Product updated successfully'],
            ], 200);
        } catch (\Exception $e) {
            return generalErrorResponse($e);
        }
    }

    public function uploadImagesMy($product, $files, $id): JsonResponse
    {
        try {
            DB::transaction(function () use ($product, $files, $id) {
                $product = Product::find($id);
                foreach ($files as $image) {
                    $path = $path = Storage::putFile('public/images/products', $image);
                    $file = new File([
                        'path' => $path,
                        'type' => checkFileType($image)
                    ]);
                    $product->image()->save($file);
                }
            });
            return response()->json([
                'messages' => ['upload successfully'],
            ], 200);
        } catch (\Exception $e) {
            //  \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function delete($product): JsonResponse
    {
        try {
            DB::transaction(function () use ($product) {
                $product->delete();
                $inventory = Inventory::whereProductId($product->id)->first();
                if ($inventory)
                    $inventory->update(['available_stock' => $inventory->available_stock ? $inventory->available_stock - 1 : $inventory->available_stock]);
            });

            return response()->json([
                'messages' => ['Product deleted successfully'],
            ], 200);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }

    public function getByCategorySlug($request, $slug): JsonResponse
    {
        try {
            $perPage = $request->rowsPerPage ?: 15;
            $page = $request->page ?: 1;

            $query = (new Product())->newQuery();

            $query->where(function ($query) use ($slug) {
                $query->whereHas('category', function ($query) use ($slug) {
                    $query->where('slug', $slug);
                })
                    ->orWhereHas('subCategory', function ($query) use ($slug) {
                        $query->where('slug', $slug);
                    });
            });

            $results = $query->select('products.*')->paginate($perPage, ['*'], 'page', $page);

            return response()->json($results, 200);
        } catch (\Exception $e) {

            return generalErrorResponse($e);
        }
    }

    public function import($request): JsonResponse
    {
        $import = new ProductsImport;
        Excel::import($import, $request->file('upload_file'));

        return response()->json([
            'messages' => ['Excel uploaded successfully'],

        ], 201);
    }

    public function deleteImages($id)
    {
        try {
            $file = File::find($id);
            $url = env('APP_URL') . 'api/media/';
            $path = str_replace($url, '', $file->path);
            // $file->delete();
            Storage::delete($path);
            $file->forceDelete();
            return response()->json([$path, $file->path,  $url], 201);
        } catch (\Exception $e) {
            //  \Log::debug($e);
            return generalErrorResponse($e);
        }
    }

    public function productSetting($data, $id)
    {

        $product = Product::find($id);
        $slot = Slot::where('product_id', $id)->where('status', 'active')->first();
        if ($product) {
            if (!empty($slot)) {
                $deals =  Deal::where('product_id', $id)->where('status', 'active')->first();
                $jsonData =
                    [
                        "slot_price" => $data['slot_price'],
                        "deal_price" => $data['deal_price'],
                        "time_period" => $data['time_period'],
                        "total_slots" => $data['total_slots'],
                        "product_id" => $id,
                        "slot_id" =>  $slot->id
                    ];
                $configurationData = new Configure([
                    'data' => json_encode($jsonData)
                ]);

                $result = Configure::where('configurable_type', 'App\Models\Product')->where('configurable_id', $id)->first();

                if (!$deals) {
                    $deals =  Deal::where('product_id', $id)->orderBy('id', 'desc')->first();
                    if (!empty($deals)) {
                        if ($deals->status == 'settled') {
                            $this->createDeals($data, $id, $slot->id);
                        }
                    } else {
                        $this->createDeals($data, $id, $slot->id);
                    }
                }
            } else {
                // new
                $slot =  Slot::create([
                    "total_slots" => $data['total_slots'],
                    "product_id" => $id,
                    "booked_slots" => 0,
                    "status" => 'active'
                ]);
                $timePeriod = $data["time_period"];
                $currentDateTime = Carbon::now();
                $newDateTime = $currentDateTime->addHours($timePeriod)->format('Y-m-d H:i:s');
                Deal::create([
                    "slot_price" => $data['slot_price'],
                    "deal_price" => $data['deal_price'],
                    "time_period" => $timePeriod,
                    "product_id" => $id,
                    "slot_id" =>  $slot->id,
                    "deal_id" => getRandomIdGenerator("DL"),
                    "deal_end_at" => $newDateTime,
                    "status" => 'active',
                ]);
                $jsonData =
                    [
                        "slot_price" => $data['slot_price'],
                        "deal_price" => $data['deal_price'],
                        "time_period" => $timePeriod,
                        "total_slots" => $data['total_slots'],
                        "product_id" => $id,
                        "slot_id" =>  $slot->id
                    ];
                $configurationData = new Configure([
                    'data' => json_encode($jsonData)
                ]);
            }

            Product::whereId($product->id)->update(['status' => 'active']);

            ProductCurrency::whereProductId($product->id)->whereCurrencyId(1)->update(['sale_price' => $data['sale_price']]);
        }
        return response()->json([
            'messages' => ['add successfully'],
        ], 200);
    }

    public function createDeals($data, $id, $slotId)
    {
        Deal::create([
            "slot_price" => $data['slot_price'],
            "deal_price" => $data['deal_price'],
            "time_period" => $data['time_period'],
            "product_id" => $id,
            "slot_id" =>  $slotId,
            "deal_id" => getRandomIdGenerator("DL"),
            "status" => 'active'
        ]);
    }

    public function productSatus($data, $id)
    {
        try {
            $product = Product::find($id);
            if ($data['status'] != 'active' && !empty(($product->deal))) {
                $activeDeal = $product->deal->where('deals.status', 'active')->first();
                if ($activeDeal)
                    return response()->json([
                        'messages' => ['Product can not be updated while deal is active'],
                        'status' => false,
                    ], 201);
            }
            $product->status = $data['status'];
            $product->update();
            return response()->json([
                'messages' => ['update successfully'],
            ], 200);
        } catch (\Exception $e) {
            \Log::debug($e);
            return generalErrorResponse($e);
        }
    }
}
