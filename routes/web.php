<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PdfController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/admin', function () {
    return view('welcome');
});
////////////////////////////////
Route::get('/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__ . '/auth.php';


Route::group(['middleware' => 'auth'], function () {

    Route::get('/addcategory', [CategoryController::class, 'addcategory']);
    Route::get('/categories', [CategoryController::class, 'categories']);
    Route::post('/savecategory', [CategoryController::class, 'savecategory']);
    Route::get('/edit_category/{id}', [CategoryController::class, 'edit_category']);
    Route::post('/updatecategory', [CategoryController::class, 'updatecategory']);
    Route::get('/delete_category/{id}', [CategoryController::class, 'delete_category']);

    Route::get('/addslider', [SliderController::class, 'addslider']);
    Route::get('/sliders', [SliderController::class, 'sliders']);
    Route::post('/saveslider', [SliderController::class, 'saveslider']);
    Route::get('/edit_slider/{id}', [SliderController::class, 'edit_slider']);
    Route::post('/updateslider', [SliderController::class, 'updateslider']);
    Route::get('/delete_slider/{id}', [SliderController::class, 'delete_slider']);
    Route::get('/activate_slider/{id}', [SliderController::class, 'activate_slider']);
    Route::get('/unactivate_slider/{id}', [SliderController::class, 'unactivate_slider']);

    Route::get('/addproduct', [ProductController::class, 'addproduct']);
    Route::get('/products', [ProductController::class, 'products']);
    Route::post('/saveproduct', [ProductController::class, 'saveproduct']);
    Route::get('/edit_product/{id}', [ProductController::class, 'edit_product']);
    Route::post('/updateproduct', [ProductController::class, 'updateproduct']);
    Route::get('/delete_product/{id}', [ProductController::class, 'delete_product']);
    Route::get('/activate_product/{id}', [ProductController::class, 'activate_product']);
    Route::get('/unactivate_product/{id}', [ProductController::class, 'unactivate_product']);
    Route::get('/view_product_by_category/{category_name}', [ProductController::class, 'view_product_by_category']);
});

//Route::get('/admin', [AdminController::class, 'admin']);
Route::get('/', [ClientController::class, 'home']);
Route::get('/cart', [ClientController::class, 'cart']);
Route::get('/addtocart/{id}', [ClientController::class, 'addtocart']);
Route::post('/update_qty/{id}', [ClientController::class, 'update_qty']);
Route::get('/remove_from_cart/{id}', [ClientController::class, 'remove_from_cart']);
Route::get('/checkout', [ClientController::class, 'checkout']);
Route::get('/shop', [ClientController::class, 'shop']);
Route::get('/login1', [ClientController::class, 'login']);
Route::get('/logout', [ClientController::class, 'logout']);
Route::get('/signup', [ClientController::class, 'signup']);
Route::post('/create_client', [ClientController::class, 'create_client']);
Route::post('/access_client', [ClientController::class, 'access_client']);
Route::post('/postcheckout', [ClientController::class, 'postcheckout']);
Route::get('/orders', [ClientController::class, 'orders']);

Route::get('viewpdforder/{id}', [PdfController::class, 'view_pdf']);
