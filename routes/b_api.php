<?php

use App\Http\Controllers\AddToCartController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MenuControlller;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TemplateDetailController;
use Illuminate\Support\Facades\Route;


// Homepage
Route::controller(HomepageController::class)->group(function () {
    Route::get('homepage', 'index');
    Route::get('search', 'getSearchResult');
    Route::get('promotional', 'getPromotional');
});

// Language Routes
Route::controller(LanguageController::class)->group(function () {
    Route::get('languages/paginate/{params?}', 'paginate')->name('Language: View Paginated Language API');
    Route::get('languages/all', 'all')->name('Language: View All Language API');
});

// Category Routes
Route::controller(CategoryController::class)->group(function () {
    Route::get('categories/paginate/{params?}', 'paginateApi');
    Route::get('categories/all', 'allApi');
    Route::get('categories/treeView', 'treeView');
    Route::get('categories/{category}', 'get')->name('Category: View Category')->where(['category' => '[0-9]+']);
});

// Sub Category Routes
Route::controller(SubCategoryController::class)->group(function () {
    Route::get('sub-categories/paginate/{params?}', 'paginateApi')->name('SubCategory: View SubCategory API');
    Route::get('sub-categories/all', 'allApi')->name('SubCategory: View All SubCategory API');
    Route::get('sub-categories/{subCategory}', 'get')->name('SubCategory: View SubCategory API')->where(['subCategory' => '[0-9]+']);
});

// Tag Routes
Route::controller(TagController::class)->group(function () {
    Route::get('tags/paginate/{params?}', 'paginate')->name('Tag: View Tag API');
    Route::get('tags/all', 'all')->name('Tag: View Tag API');
});

// Banner Routes
Route::controller(BannerController::class)->group(function () {
    Route::get('banners/paginate/{params?}', 'paginateApi')->name('Banner: View Banner API');
    Route::get('banners/all', 'all');
});

// Currency Routes
Route::controller(CurrencyController::class)->group(function () {
    Route::get('currencies/paginate/{params?}', 'paginate')->name('Currency: View Currency API');
    Route::get('currencies/all', 'all')->name('Currency: View Currency');
});

// Country Routes
Route::controller(CountryController::class)->group(function () {
    Route::get('countries/all', 'all');
});

// State Routes
Route::controller(StateController::class)->group(function () {
    Route::get('states/getById/{countryId}', 'getById');
});

// City Routes
Route::controller(CityController::class)->group(function () {
    Route::get('cities/getById/{stateId}', 'all');
});


Route::controller(ProductController::class)->group(function () {
    Route::get('products/paginate/{params?}', 'paginateApi');
    Route::get('products/view-all', 'index');
    Route::get('products/{product}', 'get');
    Route::get('products/get-by-category-slug/{slug}', 'getByCategorySlug');
});

// Template Details
Route::controller(TemplateDetailController::class)->group(function () {
    Route::get('template-details', 'getData')->name('Template: View Template Details API');
});



Route::group(['middleware' => ['auth:sanctum']], function () {

    // Add To favorites
    Route::controller(FavoriteController::class)->group(function () {
        Route::post('favorites/add-to-favorites', 'addToFavorites');
        Route::post('favorites/remove-from-favorites', 'removeFromFavorites');
        Route::get('favorites/list', 'list');
    });

    // Add To cart
    Route::controller(AddToCartController::class)->group(function () {
        Route::post('add-to-cart', 'addToCart');
    });

    // Add To order
    Route::controller(OrderController::class)->group(function () {
        Route::get('orders/paginate/{params?}', 'paginateCustomer');
        Route::post('customer/orders', 'store');
        Route::post('customer/orders-cancel', 'cancelOrder');
        Route::get('customer/checkout/{slug}', 'orderApi');
        Route::get('customer/order-details/{orderId}', 'orderGetByIdApi');
    });

    // Add To Payment
    Route::controller(PaymentController::class)->group(function () {
        Route::post('customer/order-payment', 'storePayment');
        Route::get('payments/unpaid/{search?}', 'getUnpaidPaymentsApi');
    });

});
Route::controller(MenuControlller::class)->group(function () {
    Route::get('menus/paginate/{params?}', 'paginateApi')->name('Menu: View Menu');
    Route::get('menus/all', 'allApi');
    Route::get('menus', 'get');
});

