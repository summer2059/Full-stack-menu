<?php

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Dashboard\ConfigurationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\InventoryController;
use App\Http\Controllers\Dashboard\MenuCategoryController;
use App\Http\Controllers\Dashboard\MenuController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\Dashboard\RecipeController;
use App\Http\Controllers\Dashboard\RoleController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false, 'reset' => false, 'verify' => false]);

Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'index')->name('home-page');
    Route::get('/{token}', 'index')->name('menu.table');

    // Cart API routes
    Route::post('/api/customer/uuid', 'generateUUID')->name('customer.uuid');
    Route::post('/api/cart/add', 'addToCart')->name('cart.add');
    Route::get('/api/cart', 'getCart')->name('cart.get');
    Route::post('/api/cart/update', 'updateCart')->name('cart.update');
    Route::post('/api/cart/toggle-select', 'toggleCartSelect')->name('cart.toggle');
    Route::post('/api/cart/remove', 'removeFromCart')->name('cart.remove');

    // Order submission
    Route::post('/submit-order', 'submit')->name('order.submit');
});
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::post('/toggle-status/{model}/{id}', [CommonController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/index', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('index');
    Route::get('/update-account', [DashboardController::class, 'account'])->name('update-account');
    Route::post('/update-profile', [DashboardController::class, 'update'])->name('profile.update');

    Route::get('/qr-codes', [DashboardController::class, 'qrCodes'])->middleware('permission:qr_code.view')->name('qr-codes');
    Route::prefix('menu-category')->name('menu-category.')->group(function () {
        Route::get('/', [MenuCategoryController::class, 'index'])->middleware('permission:menu_category.view')->name('index');
        Route::get('/create', [MenuCategoryController::class, 'create'])->middleware('permission:menu_category.create')->name('create');
        Route::post('/', [MenuCategoryController::class, 'store'])->middleware('permission:menu_category.create')->name('store');
        Route::get('/{id}/edit', [MenuCategoryController::class, 'edit'])->middleware('permission:menu_category.update')->name('edit');
        Route::put('/{id}', [MenuCategoryController::class, 'update'])->middleware('permission:menu_category.update')->name('update');
        Route::delete('/{id}', [MenuCategoryController::class, 'destroy'])->middleware('permission:menu_category.delete')->name('destroy');
    });
    Route::prefix('menu')->name('menu.')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->middleware('permission:menu.view')->name('index');
        Route::get('/create', [MenuController::class, 'create'])->middleware('permission:menu.create')->name('create');
        Route::post('/', [MenuController::class, 'store'])->middleware('permission:menu.create')->name('store');
        Route::get('/{id}/edit', [MenuController::class, 'edit'])->middleware('permission:menu.update')->name('edit');
        Route::put('/{id}', [MenuController::class, 'update'])->middleware('permission:menu.update')->name('update');
        Route::delete('/{id}', [MenuController::class, 'destroy'])->middleware('permission:menu.delete')->name('destroy');
    });
    Route::prefix('orders')->name('order.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->middleware('permission:order.view')->name('index');
        Route::get('/completed', [OrderController::class, 'completedOrders'])->middleware('permission:order.view_completed')->name('completed');
        Route::get('/table/{table_number}', [OrderController::class, 'showUnpaidOrdersByTable'])->middleware('permission:order.view')->name('byTable');
        Route::post('/mark-paid', [OrderController::class, 'markAllPaid'])->middleware('permission:order.mark_paid')->name('markAllPaid');
        Route::post('/update-status', [OrderController::class, 'updateStatus'])->middleware('permission:order.update_status')->name('updateStatus');
        Route::delete('/{id}', [OrderController::class, 'destroy'])->middleware('permission:order.delete')->name('destroy');
    });
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->middleware('permission:inventory.view')->name('index');
        Route::get('/forecast', [InventoryController::class, 'forecast'])->middleware('permission:inventory.forecast')->name('forecast');
        Route::get('/create', [InventoryController::class, 'create'])->middleware('permission:inventory.create')->name('create');
        Route::post('/', [InventoryController::class, 'store'])->middleware('permission:inventory.create')->name('store');
        Route::get('/{id}/edit', [InventoryController::class, 'edit'])->middleware('permission:inventory.update')->name('edit');
        Route::put('/{id}', [InventoryController::class, 'update'])->middleware('permission:inventory.update')->name('update');
        Route::delete('/{id}', [InventoryController::class, 'destroy'])->middleware('permission:inventory.delete')->name('destroy');
        Route::post('/restock', [InventoryController::class, 'restock'])->middleware('permission:inventory.restock')->name('restock');
    });
    Route::prefix('recipe')->name('recipe.')->group(function () {
        Route::get('/', [RecipeController::class, 'index'])->middleware('permission:recipe.view')->name('index');
        Route::get('/{menu_id}/edit', [RecipeController::class, 'edit'])->middleware('permission:recipe.update')->name('edit');
        Route::post('/{menu_id}', [RecipeController::class, 'update'])->middleware('permission:recipe.update')->name('update');
    });
    Route::prefix('user')->name('user.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->middleware('permission:user.view')->name('index');
        Route::get('/create', [UserController::class, 'create'])->middleware('permission:user.create')->name('create');
        Route::post('/', [UserController::class, 'store'])->middleware('permission:user.create')->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->middleware('permission:user.update')->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->middleware('permission:user.update')->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->middleware('permission:user.delete')->name('destroy');
        Route::post('/role-permissions', [UserController::class, 'getPermissionsByRole'])->middleware('permission:user.view')->name('role.permissions');
    });

    Route::prefix('role')->name('role.')->group(function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:role.view')->name('index');
        Route::get('/create', [RoleController::class, 'create'])->middleware('permission:role.create')->name('create');
        Route::post('/', [RoleController::class, 'store'])->middleware('permission:role.create')->name('store');
        Route::get('/{role}/edit', [RoleController::class, 'edit'])->middleware('permission:role.update')->name('edit');
        Route::put('/{role}', [RoleController::class, 'update'])->middleware('permission:role.update')->name('update');
        Route::delete('/{role}', [RoleController::class, 'destroy'])->middleware('permission:role.delete')->name('destroy');
    });

    Route::prefix('site-settings')->group(function () {
        Route::get('/', [ConfigurationController::class, 'getConfiguration'])->middleware('permission:site_setting.view')->name('settings');
        Route::post('/', [ConfigurationController::class, 'postConfiguration'])->middleware('permission:site_setting.update')->name('settings.update');
    });
});