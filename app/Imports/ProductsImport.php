<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductCurrency;
use App\Models\ProductPromotion;
use App\Models\ProductTag;
use App\Models\Promotion;
use App\Models\SubCategory;
use App\Models\Tag;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsImport implements ToCollection, WithBatchInserts, WithChunkReading
{
    private $rowNumber = 0;
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        $notUploadProducts = [];
        $totalFailRecords = 0;
        $totalSuccessRecords = 0;

        foreach ($rows as $row) {
            $result = $this->importProduct($row, $this->rowNumber);
            if (!empty($result) && $this->rowNumber != 0) {
                $notUploadProducts[] = $result;
                array_push($notUploadProducts, $row);
                $totalFailRecords++;
            } elseif ($this->rowNumber != 0)  {
                $totalSuccessRecords++;
            }
            $this->rowNumber++;
        }
        $this->notUploadProducts = $notUploadProducts;
        $this->totalFailRecords = $totalFailRecords;
        $this->totalSuccessRecords = $totalSuccessRecords;
    }
    public function importProduct($row, $rowNumber)
    {


        /*  Get all Columns Position from the Excel  */
        $this->getPositions($row);

        if ($rowNumber !== 0) { /*  Skipping the first row in Excel sheet  */
            if (!empty(session('cat_slug'))) {
                /*  Check Slug already exist in Category  */
                $category = Category::where('slug', $row[session('cat_slug')])->first();

                if (!empty($category)) {

                    $categoryId = $category->id;
                    $subCategory = SubCategory::where('slug', $row[session('sub_slug')])->first();

                    if (!empty($subCategory)) {
                        $subCategoryId = $subCategory->id;

                        $productData = [
                            'category_id' => $categoryId,
                            'sub_category_id' => $subCategoryId,
                            'sku' => $row[session('product_sku')],
                            'slug' => $row[session('product_slug')],
                            'quantity' => $row[session('product_quantity')],
                            'slots' => $row[session('product_slots')],
                            'meta_title' => $row[session('product_meta_title')],
                            'meta_description' => $row[session('product_meta_description')],
                            'meta_keywords' => $row[session('product_meta_keywords')],
                            'status' => !empty($row[session('product_status')]) ? $row[session('product_status')] : config('constants.STATUS_INACTIVE')
                        ];

                        /*  Check Product name  already exist in Product   */
                        $product = $this->validateProduct($productData);

                        if (empty($product)) {
                            /* Create New Product  */
                            $productId = Product::create($productData)->id;
                        } else { /*  Update Old Product */

                            $productId = $product->id;
                            $oldQuantity = $product->quantity;
                            $newQuantity = $productData['quantity'];
                            $productData['quantity'] = $oldQuantity + $newQuantity;

                            $oldSlots = $product->slots;
                            $newSlots = $productData['slots'];
                            $productData['slots'] = $oldSlots + $newSlots;

                            Product::where('id', $productId)->update($productData);
                        }

                        //  Currency
                        $currencyCode = !empty($row[session('product_currency')]) ? $row[session('product_currency')] : 'USD';
                        /*  Check Currency code  already exist in Currency   */
                        $currency = $this->validateCurrency($currencyCode);
                        if (empty($currency)) {
                            /* Create New Currency  */
                            $currency = Currency::create(['code' => $row[session('product_currency')], 'status' => config('constants.STATUS_INACTIVE')]);
                        }

                        $currencyData = [
                            'product_id' => $productId,
                            'currency_id' => $currency->id,
                            'price' => $row[session('product_price')],
                            'sale_price' => $row[session('product_sale_price')],
                            'purchase_price' => $row[session('product_purchase_price')]
                        ];
                        $productCurrency = $this->validateProductCurrency($currencyData);
                        if (!empty($productCurrency)) {
                            ProductCurrency::where(['currency_id' => $currency->id, 'product_id' => $productId])->update($currencyData);
                        } else {
                            ProductCurrency::create($currencyData);
                        }

                        /* Promotions  */
                        $promotionsData = [config('constants.PROMOTIONS.HOT_DEALS'), config('constants.PROMOTIONS.TOP_DEALS'), config('constants.PROMOTIONS.TRENDING_NOW'), config('constants.PROMOTIONS.LATEST')];
                        for ($i = 0; $i < count($promotionsData); $i++) {
                            $this->getPromotion($promotionsData[$i], $productId, $row);
                        }

                        /*  Tags  */

                        $tagIds = $this->getTags($row);

                        if (!empty($tagIds)) {

                            ProductTag::where(['product_id' => $productId])->delete();
                            foreach ($tagIds as $tagId) {
                                ProductTag::create(['product_id' => $productId, 'tag_id' => $tagId]);
                            }
                        }

                        /*   */
                    } else { /*  Skipping not matched Sub category Data  */
                        $row['fail_reason'] = 'Sub Category  Not matched in DB';
                        return $row;
                    }
                } else { /*  Skipping not matched category Data  */
                    $row['fail_reason'] = 'Category Not matched in DB';
                    return $row;
                }
            }
        }
    }
    public function getTags($row)
    {
        $tagIds = [];
        if (!empty($row[session('product_tag_slug')])) {
            $slugs = explode(',', $row[session('product_tag_slug')]);

            if (!empty($slugs)) {

                foreach ($slugs as $slug) {
                    $tags = [
                        'slug' =>  strtolower(str_replace(' ', '_', $slug)),
                        'name' => ucfirst($slug),
                        'status' => config('constants.STATUS_ACTIVE')
                    ];
                    $tag = Tag::where($tags)->first();
                    if (empty($tag)) {
                        $tag = Tag::create($tags);
                    }

                    $tagIds[] = $tag->id;
                }
            }
        }
        return $tagIds;
    }
    public function getPromotion($promotionSlug, $productId, $row)
    {
        $where = ['slug' => $promotionSlug, 'status' => config('constants.STATUS_ACTIVE')];
        $promotion =  Promotion::where($where)->first();
        if (!empty($promotion)) {

            if (!empty($row[session('product_' . $promotionSlug)]) && $row[session('product_' . $promotionSlug)] == 'Y') {
                $promotionData = [
                    'product_id' => $productId,
                    'promotion_id' => $promotion->id
                ];
                $promotions = ProductPromotion::where($promotionData)->first();
                if (empty($promotions)) {
                    ProductPromotion::insert($promotionData, 'promotion_id');
                }
            } else {
                $promotionData = [
                    'product_id' => $productId,
                    'promotion_id' => $promotion->id
                ];
                ProductPromotion::where($promotionData)->delete();
            }
        }
        return $promotion;
    }
    public function getPositions($row)
    {
        if (!empty($row)) {
            for ($key = 1; $key < count($row); $key++) {
                $arrayData =  $this->split($row[$key]);
                if (!empty($arrayData)) {
                    if ($arrayData[0] == 'cat') {
                        switch ($arrayData[1]) {
                            case  'slug':
                                session(['cat_slug' => $key]);
                                break;
                            default:
                                break;
                        }
                    } elseif ($arrayData[0] == 'sub') {
                        switch ($arrayData[1]) {
                            case  'slug':
                                session(['sub_slug' => $key]);
                                break;
                            default:
                                break;
                        }
                    } elseif ($arrayData[0] == 'product') {
                        switch ($arrayData[1]) {
                            case 'sku':
                                session(['product_sku' => $key]);
                                break;
                            case 'slug':
                                session(['product_slug' => $key]);
                                break;
                            case 'quantity':
                                session(['product_quantity' => $key]);
                                break;
                            case 'slots':
                                session(['product_slots' => $key]);
                                break;
                            case 'meta':
                                if ($arrayData[2] == 'title') {
                                    session(['product_meta_title' => $key]);
                                } elseif ($arrayData[2] == 'description') {
                                    session(['product_meta_description' => $key]);
                                } elseif ($arrayData[2] == 'keywords') {
                                    session(['product_meta_keywords' => $key]);
                                }
                                break;
                            case 'status':
                                session(['product_status' => $key]);
                                break;
                            case 'currency':
                                session(['product_currency' => $key]);
                                break;
                            case 'price':
                                session(['product_price' => $key]);
                                break;
                            case 'sale':
                                session(['product_sale_price' => $key]);
                                break;
                            case 'purchase':
                                session(['product_purchase_price' => $key]);
                                break;
                            case 'hot':
                                session(['product_hot_deals' => $key]);
                                break;
                            case 'top':
                                session(['product_top_deal' => $key]);
                                break;
                            case 'trending':
                                session(['product_trending_now' => $key]);
                                break;
                            case 'latest':
                                session(['product_latest' => $key]);
                                break;
                            case 'tag':
                                session(['product_tag_slug' => $key]);
                                break;
                            default:
                                break;
                        }
                    }
                }
            }
        }
        return true;
    }

    public function validateProductCurrency($productCurrency)
    {
        $where = [
            'product_id' => $productCurrency['product_id'],
            'currency_id' => $productCurrency['currency_id']
        ];
        return  ProductCurrency::where($where)->first();
    }
    public function validateCurrency($currencyCode)
    {
        $where = [
            'code' => $currencyCode,
            'status' => 'active'
        ];
        return  Currency::where($where)->first();
    }
    public function validateProduct($product)
    {
        $where = [
            'sku' => $product['sku'],
            'status' => 'active'
        ];
        return  Product::where($where)->first();
    }

    public function split($data)
    {
        $arrayData = [];
        if (!empty($data)) {
            $arrayData = explode('_', $data);
        }
        return $arrayData;
    }


    public function batchSize(): int
    {
        return 1000;
    }
    public function chunkSize(): int
    {
        return 1000;
    }
}
