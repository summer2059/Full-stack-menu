<?php

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Dashboard\BlogController;
use App\Http\Controllers\Dashboard\ConfigurationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\InventoryController;
use App\Http\Controllers\Dashboard\MenuCategoryController;
use App\Http\Controllers\Dashboard\MenuController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\RecipeController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register' => false,
    'reset'    => false,
    'verify'   => false,
]);

Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'index')->name('home-page');
    Route::get('/{token}', 'index')->name('menu.table'); 
    Route::post('/submit-order', [FrontendController::class, 'submit'])->name('order.submit');
});

Route::get('/dashboard/index', [App\Http\Controllers\HomeController::class, 'index'])->name('/dashboard/index');

Route::group(['middleware' => ['auth'], 'prefix' => 'dashboard'], function () {

    // toggle status
    Route::post('/toggle-status/{model}/{id}', [CommonController::class, 'toggleStatus'])
        ->name('toggle-status');

    // dashboard
    Route::get('/index', [DashboardController::class, 'index'])->name('index');
    Route::get('/update-account', [DashboardController::class, 'account'])->name('update-account');
    Route::post('/update-profile', [DashboardController::class, 'update'])->name('profile.update');

    // menu category
    Route::resource('menu-category', MenuCategoryController::class);

    // menu
    Route::resource('menu', MenuController::class);

    // orders
    Route::prefix('orders')->name('order.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/table/{table_number}', [OrderController::class, 'showUnpaidOrdersByTable'])->name('byTable');
        Route::post('/mark-paid', [OrderController::class, 'markAllPaid'])->name('markAllPaid');
        Route::post('/update-status', [OrderController::class, 'updateStatus'])->name('updateStatus');
        Route::delete('/{id}', [OrderController::class, 'destroy'])->name('destroy');
        Route::get('/completed', [OrderController::class, 'completedOrders'])->name('completed');
    });
    Route::get('/qr-codes', [DashboardController::class, 'qrCodes'])->name('qr-codes');

    // inventory
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/create', [InventoryController::class, 'create'])->name('create');
        Route::post('/', [InventoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [InventoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [InventoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [InventoryController::class, 'destroy'])->name('destroy');
        Route::post('/restock', [InventoryController::class, 'restock'])->name('restock');
        Route::get('/forecast', [InventoryController::class, 'forecast'])->name('forecast');
    });

    Route::prefix('recipe')->name('recipe.')->group(function () {
        Route::get('/', [RecipeController::class, 'index'])->name('index');
        Route::get('/{menu_id}/edit', [RecipeController::class, 'edit'])->name('edit');
        Route::post('/{menu_id}', [RecipeController::class, 'update'])->name('update');
    });

    // users
    Route::resource('user', UserController::class);
    Route::post('user/role-permissions', [UserController::class, 'getPermissionsByRole'])
        ->name('user.role.permissions');

    // settings
    Route::get('/site-settings', [ConfigurationController::class, 'getConfiguration'])->name('settings');
    Route::post('/site-settings', [ConfigurationController::class, 'postConfiguration'])->name('settings.update');
});