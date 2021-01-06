<?php


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

use Illuminate\Http\Request;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json; charset=UTF-8', true);


/** Start Auth Route **/

Route::middleware('auth:api')->group(function () {
    //Auth_private
    Route::prefix('Auth_private')->group(function () {
        Route::post('/edit_profile', 'Api\UserController@edit_profile')->name('user.edit_profile');
        Route::post('/check_active_code', 'Api\UserController@check_active_code')->name('user.check_active_code');
        Route::post('/logout', 'Api\UserController@logout')->name('user.logout');
        Route::post('/change_lang', 'Api\UserController@change_lang')->name('user.change_lang');
        Route::get('/my_info', 'Api\UserController@my_info')->name('user.my_info');
        Route::get('/my_favorite', 'Api\UserController@my_favorite')->name('user.my_favorite');
        Route::post('/orderMarket', 'Api\UserController@orderMarket')->name('user.orderMarket');
    });
    /** Locations */
    Route::prefix('Locations')->group(function () {
        Route::post('/add_address', 'Api\LocationsController@add_address')->name('Locations.add_address');
        Route::post('/edit_address/{id}', 'Api\LocationsController@edit_address')->name('Locations.edit_address');
        Route::post('/delete_address/{id}', 'Api\LocationsController@delete_address')->name('Locations.delete_address');
        Route::get('/single_address/{id}', 'Api\LocationsController@single_address')->name('Locations.single_address');
        Route::get('/my_addresses', 'Api\LocationsController@my_addresses')->name('Locations.my_addresses');
    });


    /** Home */
    Route::prefix('Home')->group(function () {
        Route::get('/home', 'Api\HomeController@home')->name('Home.home');
        Route::get('/filterProducts', 'Api\HomeController@filterProducts')->name('Home.filterProducts');
    });

    /** Product */
    Route::prefix('Product')->group(function () {
        Route::post('/favorite/{product_id}', 'Api\ProductController@favorite')->name('Product.favorite');
        Route::get('/singleProduct/{product_id}', 'Api\ProductController@singleProduct')->name('Product.singleProduct');
        Route::get('/ProductByCat', 'Api\ProductController@ProductByCat')->name('Product.ProductByCat');
        Route::post('/orderProduct', 'Api\ProductController@orderProduct')->name('Product.orderProduct');
    });

    /** Cart */
    Route::prefix('Cart')->group(function () {
        Route::post('/addToCart', 'Api\CartController@addToCart')->name('Cart.addToCart');
        Route::get('/myCart', 'Api\CartController@myCart')->name('Cart.myCart');
        Route::post('/deleteMyCart', 'Api\CartController@deleteMyCart')->name('Cart.deleteMyCart');
        Route::post('/deleteFromCart', 'Api\CartController@deleteFromCart')->name('Cart.deleteFromCart');
        Route::post('/updateCart', 'Api\CartController@updateCart')->name('Cart.updateCart');
    });

    /** Order */
    Route::prefix('Order')->group(function () {
        Route::post('/makeOrder', 'Api\OrderController@makeOrder')->name('Order.makeOrder');
        Route::post('/checkDiscountCode', 'Api\OrderController@checkDiscountCode')->name('Order.checkDiscountCode');
        Route::post('/removeDiscountCode', 'Api\OrderController@removeDiscountCode')->name('Order.removeDiscountCode');
        Route::get('/myOrders', 'Api\OrderController@myOrders')->name('Order.myOrders');
        Route::get('/singleOrder', 'Api\OrderController@singleOrder')->name('Order.singleOrder');
    });

});
/** End Auth Route **/

/** Auth_general */
Route::prefix('Auth_general')->group(function () {
    Route::post('/login', 'Api\UserController@login')->name('user.login');
});

/** Category Route*/
Route::prefix('Category')->group(function()
{
    Route::get('/mainCat', 'Api\CategoryController@mainCat')->name('Category.mainCat');
    Route::get('/subCat/{mainCat_id}', 'Api\CategoryController@subCat')->name('Category.subCat');
});

Route::get('/getSettings', 'Api\SettingController@getSettings')->name('setting.getSettings');

/** Supplier */
Route::prefix('Supplier')->group(function () {
    Route::post('/orderSupplier', 'Api\SupplierController@orderSupplier')->name('Supplier.orderSupplier');
});
/** ً WebSite Routes */
Route::get('/Home/homeWebSite', 'Api\HomeController@homeWebSite')->name('Home.homeWebSite');
Route::get('/Slider/subSlider', 'Api\HomeController@subSlider')->name('Home.subSlider');
Route::get('/Product/ProductBySubCat', 'Api\ProductController@ProductBySubCat')->name('Product.ProductByCat');
Route::get('/Product/filterProduct', 'Api\ProductController@filterProduct')->name('Product.filterProduct');
/** ً End WebSite Routes */