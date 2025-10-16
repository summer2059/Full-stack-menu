<?php

use App\Http\Controllers\CommonController;
use App\Http\Controllers\Dashboard\BlogController;
use App\Http\Controllers\Dashboard\ConfigurationController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\MenuCategoryController;
use App\Http\Controllers\Dashboard\MenuController;
use App\Http\Controllers\Dashboard\OrderController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes([
    'register' => false,
    'reset' => false,
    'verify' => false,
]);
Route::controller(FrontendController::class)->group(function () {
    Route::get('/', 'index')->name('home-page');
    Route::post('/submit-order', [FrontendController::class, 'submit'])->name('order.submit');
});

Route::get('/dashboard/index', [App\Http\Controllers\HomeController::class, 'index'])->name('/dashboard/index');

Route::group(['middleware' => ['auth'], 'prefix' => 'dashboard'], function () {

    //toogle status
    Route::post('/toggle-status/{model}/{id}', [CommonController::class, 'toggleStatus'])->name('toggle-status');

    //index
    Route::get('/index', [DashboardController::class, 'index'])->name('index');
    Route::get('/update-account', [DashboardController::class, 'account'])->name('update-account');
    Route::post('/update-profile', [DashboardController::class, 'update'])->name('profile.update');

    Route::resource('/blog', BlogController::class);
    Route::get('blog-image/{id}', [BlogController::class,'getBlogImage'])->name('blog-image');
    Route::post('/blog/upload-images', [BlogController::class, 'uploadImages'])->name('blog.uploadImages');
    Route::post('/blog/{id}/delete-image', [BlogController::class, 'deleteImage'])->name('blog.deleteImage');
    Route::post('/blog/{id}/toggle-status', [BlogController::class, 'toggleStatus'])->name('blog.toggle-status');
    //site settings

    //menu category start
    Route::resource('/menu-category', MenuCategoryController::class);
    //menu category end

    //menu start
    Route::resource('/menu', MenuController::class);
    //menu end
    //order start
    // Route::resource('/order', OrderController::class);
    // Route::post('/order/status', [OrderController::class, 'updateStatus'])->name('order.updateStatus');
    Route::get('/orders', [OrderController::class, 'index'])->name('order.index');
    Route::get('/orders/table/{table_number}', [OrderController::class, 'showUnpaidOrdersByTable'])->name('order.byTable');
    Route::post('/orders/mark-paid', [OrderController::class, 'markAllPaid'])->name('order.markAllPaid');
    Route::post('/orders/update-status', [OrderController::class, 'updateStatus'])->name('order.updateStatus');
    Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('order.destroy');
    Route::get('/orders/completed', [OrderController::class, 'completedOrders'])->name('order.completed');

    //order end

    Route::get('/site-settings', [ConfigurationController::class, 'getConfiguration'])->name('settings');
    Route::post('/site-settings', [ConfigurationController::class, 'postConfiguration'])->name('settings.update');
});