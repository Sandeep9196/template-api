<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhitelistIPController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\CountryController;
use App\Http\Controllers\Google2FAController;
use App\Http\Controllers\StateController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CheckBalanceController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ConfigureController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MenuControlller;
use App\Http\Controllers\NotFoundController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettlementController;
use App\Http\Controllers\WinningController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\TemplateDetailController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UtilityController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::controller(UtilityController::class)->group(function () {
    Route::get('storage/{path}', 'getStorageFile')->where('path', '.*');
    Route::get('media/{path}', 'getMedia')->where('path', '.*');
    Route::post('admin/log-admin-dashboard-missing-key', 'logMissingKey')->where('path', '.*');
    Route::get('call-artisan/{command}', 'callArtisan')->where('path', '.*');
});
Route::group(['middleware' => 'whitelist_ip', 'prefix' => 'admin'], function () {
    // Sanctum endpoint to generate cookie
    Route::get('sanctum/csrf-cookie', function () {
        return response('OK', 204);
    });

    Route::controller(AuthController::class)->group(function () {
        Route::post('auth/login', 'login');
    });

    //  2FA  and generate QR code
    Route::controller(Google2FAController::class)->group(function () {
        Route::post('auth/2fa/verify-user', 'verifyUser');
        Route::post('auth/2fa/enable-ga', 'enableGa');
        Route::post('auth/2fa/verify-code', 'verifyCode');
    });
});



// Customer routes
Route::controller(CustomerController::class)->group(function () {
    Route::get('customers-earning', 'getCalculations');
    Route::post('customers/send-otp', 'sendOTP');
    Route::post('customers/verify-otp', 'verifyOTP');
    Route::post('customers/register', 'register');
    Route::post('customers/login', 'login');
    Route::get('customers/{customer}', 'get')->where(['customer' => '[0-9]+']);
    Route::post('customers/forget-password', 'forgetPassword');
    Route::post('customers/set-new-password', 'setNewPassword');
    Route::get('customers/user-details', 'userDetails');
});


// unauthenticated admin routes
Route::group(['prefix' => 'admin'], function () {

    // Language Routes
    Route::controller(LanguageController::class)->group(function () {
        Route::get('languages/all', 'all')->name('Language: View Language');
    });
});

// Authenticated routes
Route::group(['middleware' => ['auth:sanctum']], function () {

    // Customer routes
    Route::controller(CustomerController::class)->group(function () {
        Route::get('customers/mlm-earning', 'getCalculationsCustomers');
        Route::post('customers/update-account', 'updateAccount');
        Route::get('customers/user-details', 'userDetails');
        Route::post('customers/create-bot-customer', 'createBotCustomer');
        Route::get('customers/get-transactions', 'getTransactions');
    });


    // Authenticated admin routes
    Route::group(['prefix' => 'admin'], function () {

        Route::controller(CheckBalanceController::class)->group(function () {
            Route::get('get/otp-balance', 'getBalance');
        });

        Route::controller(CustomerController::class)->group(function () {
            Route::get('customers-earning', 'getCalculations');

            Route::post('customers/send-otp', 'sendOTP');
            Route::post('customers/verify-otp', 'verifyOTP');
            Route::post('customers/register', 'register');
            Route::post('customers/login', 'login');
            Route::get('customers/{customer}', 'get')->where(['customer' => '[0-9]+']);
            Route::post('customers/forget-password', 'forgetPassword');
            Route::post('customers/set-new-password/{customer}', 'setCustomerNewPassword');
        });


        // Auth routes
        Route::controller(CustomerController::class)->group(function () {
            Route::post('customers/create-bot-customer', 'createBotCustomer');
        });

        Route::controller(AuthController::class)->group(function () {
            Route::get('auth/user', 'user');
            Route::post('auth/logout', 'logout');
        });

        Route::controller(CustomerController::class)->group(function () {
            Route::get('customers/earning/{customer}', 'getCalculations');
        });

        Route::controller(CustomerController::class)->group(function () {
            Route::get('customers/count', 'getCounts');
        });

        // for product managerment route
        Route::controller(ProductController::class)->group(function () {
            Route::get('products/paginate/{params?}', 'paginate')->name('Product: View Product');
            Route::get('products/view-all', 'index')->name('Product: View All Product');
            Route::get('products/{product}', 'get')->name('Product: View Product')->where(['product' => '[0-9]+']);
            Route::post('products', 'store')->name('Product: Create Product');
            Route::post('products-images/{id}', 'uploadImages')->name('Product: Upload Images');
            Route::post('products-setting/{id}', 'productSetting')->name('Product: Product Setting');
            Route::post('products-status/{id}', 'productSatus')->name('Product: Product Setting');
            Route::delete('products-image-delete/{id}', 'deleteImages')->name('Product: Delete file');
            Route::patch('products/{product}', 'update')->name('Product: Edit/Update Product')->where(['product' => '[0-9]+']);
            Route::delete('products/{product}', 'delete')->name('Product: Delete Product')->where(['product' => '[0-9]+']);
            Route::post('products/upload', 'upload')->name('Product: Upload Product');
            Route::get('products/get-by-category-slug/{slug}', 'getByCategorySlug')->name('Product: get By Category Slug Product');
        });

        // Category Routes
        Route::controller(CategoryController::class)->group(function () {
            Route::get('categories/paginate/{params?}', 'paginate')->name('Category: View Pagination Category');
            Route::get('categories/all', 'all')->name('Category: Views Category');
            Route::get('categories/using/{id}', 'usingInProduct')->name('Category: Views Category');
            Route::get('categories/treeView', 'treeView')->name('Category: TreeView Category');
            Route::post('categories', 'store')->name('Category: Create Category');
            Route::post('categories/upload', 'upload')->name('Category: BulkUpload Category');
            Route::post('categories/restore/{categoryId}', 'restore')->name('Category: Edit/Update Category');
            Route::patch('categories/{category}', 'update')->name('Category: Edit/Update Category')->where(['category' => '[0-9]+']);
            Route::delete('categories/{category}', 'delete')->name('Category: Delete Category')->where(['category' => '[0-9]+']);
            Route::get('categories/{category}', 'get')->name('Category: View Category')->where(['category' => '[0-9]+']);
        });

        // Sub Category Routes
        Route::controller(SubCategoryController::class)->group(function () {
            Route::get('sub-categories/paginate/{params?}', 'paginate')->name('SubCategory: View SubCategory');
            Route::get('sub-categories/using/{id}', 'usingInProduct')->name('Category: Views SubCategory');
            Route::get('sub-categories/all', 'all')->name('SubCategory: View All SubCategory');
            Route::get('sub-by-categories/{id}', 'getByCategoryID')->name('SubCategory: View SubCategory')->where(['id' => '[0-9]+']);
            Route::get('sub-categories/{subCategory}', 'get')->name('SubCategory: View SubCategory')->where(['subCategory' => '[0-9]+']);
            Route::post('sub-categories', 'store')->name('SubCategory: Create SubCategory');
            Route::patch('sub-categories/{subCategory}', 'update')->name('SubCategory: Edit/Update SubCategory')->where(['subCategory' => '[0-9]+']);
            Route::delete('sub-categories/{subCategory}', 'delete')->name('SubCategory: Delete SubCategory')->where(['subCategory' => '[0-9]+']);
        });

        // Tag Routes
        Route::controller(TagController::class)->group(function () {
            Route::get('tags/paginate/{params?}', 'paginate')->name('Tag: View Tag');
            Route::get('tags/all', 'all')->name('Tag: View Tag');
            Route::post('tags', 'store')->name('Tag: Create Tag');
            Route::patch('tags/{tag}', 'update')->name('Tag: Edit/Update Tag')->where(['tag' => '[0-9]+']);
            Route::delete('tags-delete-image/{id}', 'deleteImage')->name('Tag: Edit/Update Tag');
            Route::delete('tags/{tag}', 'delete')->name('Tag: Delete Tag')->where(['tag' => '[0-9]+']);
        });

        // Template Routes
        Route::controller(TemplateController::class)->group(function () {
            Route::get('templates/paginate/{params?}', 'paginate')->name('Template: View Template');
            Route::get('templates/all', 'all')->name('Template: View Template');
            Route::post('templates', 'store')->name('Template: Create Template');
            Route::post('templates/{template}', 'update')->name('Template: Edit/Update Template');
        });

        // Template Details
        Route::controller(TemplateDetailController::class)->group(function () {
            Route::get('template-details', 'getData')->name('Template: View Template Details');
            Route::post('template-details', 'store')->name('Template: Create Template Details');
            Route::post('template-details/{templateDetail}', 'update')->name('Template: Edit/Update Template Details');

        });

        // Member routes
        Route::controller(MemberController::class)->group(function () {
            Route::get('members/all', 'all')->name('Members: View All Members');
            Route::get('members/paginate/{params?}', 'paginate')->name('Members: Views Members');
            Route::post('members/store', 'store')->name('Members: Create Members');
            Route::patch('members/{member}', 'update')->name('Members: Edit/Update Members')->where(['member' => '[0-9]+']);
            Route::get('members/{member}', 'getCalculations')->name('Members: View Members')->where(['member' => '[0-9]+']);
            Route::delete('members/{member}', 'delete')->name('Members: Delete Members')->where(['member' => '[0-9]+']);
        });


        // Promotion Routes
        Route::controller(PromotionController::class)->group(function () {
            Route::get('promotions/paginate/{params?}', 'paginate')->name('Promotion: View Promotion');
            Route::get('promotions/all', 'all')->name('Promotion: View Promotion');
            Route::post('promotions', 'store')->name('Promotion: Create Promotion');
            Route::patch('promotions/{id}', 'update')->name('Promotion: Edit/Update Promotion')->where(['promotion' => '[0-9]+']);
            Route::delete('promotions/{promotion}', 'delete')->name('Promotion: Delete Promotion')->where(['promotion' => '[0-9]+']);
        });

        Route::controller(RoleController::class)->group(function () {
            Route::get('roles/paginate/{params?}', 'paginate')->name('Role: View Role');
            Route::get('roles/{role}/users', 'users')->name('Role: View Role');
            Route::get('roles/all', 'all')->name('Role: View Role');
            Route::get('roles/{role}', 'get')->name('Role: View Role')->where(['role' => '[0-9]+']);
            Route::get('roles', 'roles')->name('Role: View Role');
            Route::post('roles', 'store')->name('Role: Create Role');
            Route::patch('roles/{role}', 'update')->name('Role: Edit/Update Role')->where(['role' => '[0-9]+']);
            Route::delete('roles/{role}/{params?}', 'delete')->name('Role: Delete Role');

            Route::get('permissions/paginate/{params?}', 'paginatePermissions')->name('Permission: View Permission');
            Route::get('permissions/all', 'permissions')->name('Permission: View Permission');
        });

        // User routes
        Route::controller(UserController::class)->group(function () {
            Route::get('users/paginate/{params?}', 'paginate')->name('User: View User');
            Route::get('users/all', 'all')->name('User: View User');
            Route::get('users/{user}', 'get')->name('User: View User')->where(['user' => '[0-9]+']);
            Route::post('users', 'store')->name('User: Create User');
            Route::post('users/register', 'register')->name('User: Register User');
            Route::patch('users/{user}', 'update')->name('User: Edit/Update User')->where(['user' => '[0-9]+']);
            Route::post('users-password/{id}', 'updatePassword');
            Route::delete('users/{user}', 'delete')->name('User: Delete User')->where(['user' => '[0-9]+']);
        });

        // Language Routes
        Route::controller(LanguageController::class)->group(function () {
            Route::get('languages/paginate/{params?}', 'paginate')->name('Language: View Paginated Language');
            Route::post('languages', 'store')->name('Language: Create Language');
            Route::patch('languages/{language}', 'update')->name('Language: Edit/Update Language')->where(['language' => '[0-9]+']);
            Route::delete('languages/{language}', 'delete')->name('Language: Delete Language')->where(['language' => '[0-9]+']);
            Route::get('languages/{language}', 'get')->name('Language: View Language By Id')->where(['language' => '[0-9]+']);
        });

        // Settinga Routes
        Route::controller(SettingController::class)->group(function () {
            Route::get('settings/paginate/{params?}', 'paginate')->name('Setting: View Setting');
            Route::get('settings/key/{key}', 'getByKey')->name('Setting: View Setting');
            Route::get('settings/all', 'all')->name('Setting: View Setting');
            Route::get('settings', 'get')->name('Setting: View Setting')->where(['setting' => '[0-9]+']);
            Route::post('settings', 'store')->name('Setting: Create Setting');
            Route::patch('settings/{setting}', 'update')->name('Setting: Edit/Update Setting')->where(['setting' => '[0-9]+']);
            Route::delete('settings/{setting}', 'delete')->name('Setting: Delete Setting')->where(['setting' => '[0-9]+']);
        });

        // // Audit Log
        // Route::controller(AuditLogController::class)->group(function () {
        //     Route::get('audits/paginate/{params?}', 'paginate')->name('Report: View Audit Logs');
        //     Route::get('audits/models', 'getModels');
        // });

        // Audit Log
        Route::controller(AuditLogController::class)->group(function () {
            Route::get('audits/paginate/{params?}', 'paginate')->name('Report: View Audit Logs');
            Route::get('login-logs/paginate/admins/{params?}', 'paginateAdmins')->name('Report: View Admin Login');
            Route::get('login-logs/paginate/members/{params?}', 'paginateMembers')->name('Report: View Member Login');
            Route::get('audits/models', 'getModels');
        });

        // Invoice
        Route::controller(InvoiceController::class)->group(function () {
            Route::get('invoices/all', 'all')->name('Setting: View All Invoices');
            Route::get('invoices/pdfInvoiceDownload/{orderId}', 'pdfInvoicesDownload')->name('Setting: Downloaded Invoice');
        });

        // Whitelist IP
        Route::controller(WhitelistIPController::class)->group(function () {
            Route::get('whitelist-ips/paginate/{params?}', 'paginate')->name('Whitelist IP: View Whitelist IP');
            Route::get('whitelist-ips/all', 'all')->name('Whitelist IP: View Whitelist IP');
            Route::get('whitelist-ips/{ip}', 'get')->name('Whitelist IP: View Whitelist IP')->where(['ip' => '[0-9]+']);
            Route::post('whitelist-ips', 'store')->name('Whitelist IP: Create Whitelist IP');
            Route::patch('whitelist-ips/{ip}', 'update')->name('Whitelist IP: Edit/Update Whitelist IP')->where(['ip' => '[0-9]+']);
            Route::delete('whitelist-ips/{ip}', 'delete')->name('Whitelist IP: Delete Whitelist IP')->where(['ip' => '[0-9]+']);
        });

        // Currency Routes
        Route::controller(CurrencyController::class)->group(function () {
            Route::get('currencies/paginate/{params?}', 'paginate')->name('Currency: View Currency');
            Route::get('currencies/all', 'all')->name('Currency: View Currency');
            Route::post('currencies', 'store')->name('Currency: Create Currency');
            Route::patch('currencies/{currency}', 'update')->name('Currency: Edit/Update Currency');
            Route::delete('currencies/{currency}', 'delete')->name('Currency: Delete Currency');
        });

        // Country Routes
        Route::controller(CountryController::class)->group(function () {
            Route::get('countries/paginate/{params?}', 'paginate')->name('Country: View Country');
            Route::get('countries/all', 'all')->name('Country: View Country');
            Route::post('countries', 'store')->name('Country: Create Country');
            Route::patch('countries/{country}', 'update')->name('Country: Edit/Update Country')->where(['country' => '[0-9]+']);
            Route::delete('countries/{country}', 'delete')->name('Country: Delete Country')->where(['country' => '[0-9]+']);
            Route::get('countries/{country}', 'get')->name('Country: View Country By Id')->where(['country' => '[0-9]+']);
        });

        // State Routes
        Route::controller(StateController::class)->group(function () {
            Route::get('states/paginate/{params?}', 'paginate')->name('State: View State');
            Route::get('states/all', 'all')->name('State: View State');
            Route::post('states', 'store')->name('State: Create State');
            Route::get('country-state/{id}', 'getByCountryId')->name('State: View State');
            Route::patch('states/{state}', 'update')->name('State: Edit/Update State')->where(['state' => '[0-9]+']);
            Route::delete('states/{state}', 'delete')->name('State: Delete State')->where(['state' => '[0-9]+']);
        });

        // City Routes
        Route::controller(CityController::class)->group(function () {
            Route::get('cities/paginate/{params?}', 'paginate')->name('City: View City');
            Route::get('cities/all', 'all')->name('City: View City');
            Route::post('cities', 'store')->name('City: Create City');
            Route::patch('cities/{city}', 'update')->name('City: Edit/Update City')->where(['city' => '[0-9]+']);
            Route::delete('cities/{city}', 'delete')->name('City: Delete City')->where(['city' => '[0-9]+']);
            Route::get('cities/getById/{stateId}', 'getByStateId');
        });


        Route::controller(WinningController::class)->group(function () {
            Route::post('winners/create', 'createWinner')->name('Winner: Create Winner');
            Route::post('winners/generate-winner', 'getPredicted')->name('Winner: Predicted Winner');
            Route::get('winners/paginate/{params?}', 'paginate')->name('Winner: View Winners');
            Route::post('winners/generate-deal', 'generateDeal')->name('Winner: Generate Deal');
            Route::get('winners/list', 'winnerList')->name('Winner: Winner List');
        });

        Route::controller(OrderController::class)->group(function () {
            Route::get('orders/paginate/{params?}', 'paginate')->name('Order: Views Order');
            Route::patch('orders/{order}', 'update')->name('Order: Edit/Update Order');
            Route::delete('orders/{order}', 'delete')->name('Order: Delete Order');
            Route::post('orders', 'store')->name('Order: Create Order');
            Route::get('orders/{orderId}', 'orderGetById')->name('Order: View Order')->where(['orderId' => '[0-9]+']);
            Route::patch('order-status/{order}', 'updateStatus')->name('Order: UpdateStatus Order');
            Route::patch('orders-update', 'updateOrders')->name('Order: UpdateOrders Order');
            Route::get('orders/cancel', 'cancelOrder')->name('Order: Cancel Order');
            Route::get('orders/purchase-history', 'purchaseHistory')->name('Order: Purchase Order History');
        });

        // Add To Payment
        Route::controller(PaymentController::class)->group(function () {
            Route::post('order-payment/{order}', 'store')->name('Payment: Create Payment');
            Route::get('payments/all', 'all');
            Route::get('payments/paginate/{id?}', 'paginate');
            Route::get('payments/unpaid/{search?}', 'getUnpaidPayments');
        });

        // Banner Routes
        Route::controller(BannerController::class)->group(function () {
            Route::get('banners/paginate/{params?}', 'paginate')->name('Banner: View Banner');
            Route::get('banners/all', 'all');
            Route::post('banners', 'store')->name('Banner: Create Banner');
            Route::patch('banners/{banner}', 'update')->name('Banner: Edit/Update Banner')->where(['banner' => '[0-9]+']);
            Route::post('banners/restore/{bannerId}', 'restore')->name('Banner: Edit/Update Banner');
            Route::delete('banners/{banner}', 'delete')->name('Banner: Delete Banner')->where(['banner' => '[0-9]+']);
        });

              // Menu Routes
        Route::controller(MenuControlller::class)->group(function () {
            Route::get('menus/paginate/{params?}', 'paginate')->name('Menu: View Menu');
            Route::get('menus/all', 'all');
            Route::get('menus/type/all', 'getType')->name('Menu: View Type');;
            Route::get('menus/group/all', 'getGroup')->name('Menu: View Group');
            Route::post('menus/group', 'storeGroup')->name('Menu: Create Group');
            Route::post('menus/type', 'storeType')->name('Menu: Create Type');
            Route::post('menus', 'store')->name('Menu: Create Menu');
            Route::patch('menus/{menu}', 'update')->name('Menu: Edit/Update Menu');
            Route::post('menus/restore/{menuId}', 'restore')->name('Menu: Edit/Update Menu');
            Route::delete('menus/{menu}', 'delete')->name('Menu: Delete Menu');
        });

        // Configuration Routes
        Route::controller(ConfigureController::class)->group(function () {
            Route::get('config/paginate/{params?}', 'paginate')->name('Configuration: View Paginate Configuration');

            Route::post('config/bot-master', 'botGlobalSave')->name('Configuration: Create/Update BotGlobal Configuration');
            Route::get('config/bot-master', 'botGlobal')->name('Configuration: View BotGlobal Configuration');

            Route::post('config/mlm-master', 'mlmGlobalSave')->name('Configuration: Create/Update MLMGlobal Configuration');
            Route::get('config/mlm-master', 'mlmGlobal')->name('Configuration: View MLMGlobal Configuration');

            Route::post('config/order-master', 'orderGlobalSave')->name('Configuration: Create/Update OrderGlobal Configuration');
            Route::get('config/order-master', 'orderGlobal')->name('Configuration: View OrderGlobal Configuration');

            // payment master settings
            Route::post('config/payment-master', 'paymentGlobalSave')->name('Configuration: Create/Update PaymentGlobal Configuration');
            Route::get('config/payment-master', 'paymentGlobal')->name('Configuration: View PaymentGlobal Configuration');

            Route::delete('config/{configure}', 'delete')->name('Configuration: Delete Configuration');
            Route::get('config/{configure}', 'get')->name('Configuration: View Configuration By Id');
        });

        Route::controller(NotificationController::class)->group(function () {
            Route::get('notifications/paginate/{params?}', 'paginate')->name('Notification: View Notifications');
            Route::patch('notifications/{notification}', 'update')->name('Notification: Read Notification');
        });

        // Reporting
        Route::controller(ReportController::class)->group(function () {
            Route::get('reports/paginate/{params?}', 'paginate')->name('Report: View Report');
            Route::get('reports/dashboard', 'dashboard')->name('Report: View Report');
            Route::post('reports', 'store')->name('Report: Create Report');
            Route::patch('reports/{report}', 'update')->name('Report: Edit/Update Report');
            Route::delete('reports/{report}', 'delete')->name('Report: Delete Report');
        });

        // Dashboard Statistics
        Route::controller(StatsController::class)->group(function () {
            Route::get('stats/this-month-online-members', 'getThisMonthOnlineUsers')->name('Stats: View Monthly Online Users');
            Route::get('stats/this-month-new-members', 'getThisMonthNewMembers')->name('Stats: View this Month New Members');
            Route::get('stats/recent-member-online', 'getRecentMemberOnline')->name('Stats: View Recent Member Online');
            Route::get('stats/recent-order', 'getRecentTransactions')->name('Stats: View Recent Member Online');
            Route::get('stats/dashboard', 'dashboard')->name('Stats: View Dashboard');
            Route::get('stats/slot-purchase', 'slotPurchases')->name('Stats: View Slot Purchases');
            Route::get('stats/tranfer-amount', 'tranferAmount')->name('Stats: View Tranfer Amount');
            Route::get('stats/monthly-transaction', 'getThisMontTransaction')->name('Stats: View Monthly Transaction');
        });

        // Transaction Statistics
        Route::controller(TransactionController::class)->group(function () {
            Route::get('transactions/paginate/{params?}', 'paginate')->name('Transaction: View Transaction');
            Route::get('transactions/all', 'dashboard')->name('Transaction: View Transaction');
            Route::post('transactions', 'store')->name('Transaction: Create Transaction');
            Route::patch('transactions/{transaction}', 'update')->name('Transaction: Edit/Update Transaction')->where(['transaction' => '[0-9]+']);
            Route::delete('transactions/{transaction}', 'delete')->name('Transaction: Delete Transaction')->where(['transaction' => '[0-9]+']);
            Route::post('transactions/update-status', 'updateStatus')->name('Transaction: Update Status Transaction');
            Route::get('transactions/transfer', 'tranferAmount')->name('Transaction: View Transfer Amount');
            Route::post('transactions/withdraw', 'withdraw')->name('Transaction: withdraw Amount');
        });

        // Payment Gateway
        Route::controller(PaymentGatewayController::class)->group(function () {
            Route::get('payment-gateway/paginate/{params?}', 'paginate')->name('Payment Gateway: View Payment Gateway');
            Route::get('payment-gateway/all', 'all')->name('Payment Gateway: View Payment Gateway');
            Route::post('payment-gateway', 'store')->name('Payment Gateway: Create Payment Gateway');
            Route::patch('payment-gateway/{payment}', 'update')->name('Payment Gateway: Update Payment Gateway');
            Route::delete('payment-gateway/{payment}', 'delete')->name('Payment Gateway: Delete Payment Gateway');
        });

        // Bank Account Route
        Route::controller(BankAccountController::class)->group(function () {
            Route::get('bank-accounts/paginate/{params?}', 'paginate')->name('BankAccount: Views BankAccount');
            Route::get('bank-accounts/all', 'all')->name('BankAccount: All BankAccount');
            Route::post('bank-accounts', 'store')->name('BankAccount: Create BankAccount');
            Route::patch('bank-accounts/{bankAccount}', 'update')->name('BankAccount: Edit/Update BankAccount');
            Route::delete('bank-accounts/{bankAccount}', 'delete')->name('BankAccount: Delete BankAccount');
        });

        // Settlement
        Route::controller(SettlementController::class)->group(function () {
            Route::get('settlements/paginate/{params?}', 'paginate')->name('Settlement: Views Settlement');
            Route::get('settlements/total', 'getTotalSettlement')->name('Settlement: Total Settlement');
        });
    });
});


// Transaction Statistics
Route::controller(TransactionController::class)->group(function () {
    Route::match(['get', 'post'], 'transactions/deposit-response', 'depositResponse')->name('Transaction: deposit Response');
});

/*  Api For Next */
Route::middleware(['whitelist_ip', 'language'])->group(function () {
    Route::name('')->group(__DIR__ . '/b_api.php');
});

Route::fallback([NotFoundController::class, 'index']);
