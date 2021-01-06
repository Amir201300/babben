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

Route::middleware('auth:api-Admin')->group(function () {
    //Auth_private
    Route::prefix('Auth_private')->group(function()
    {
        Route::post('/change_password', 'AuthController@change_password')->name('Auth.change_password');
        Route::post('/edit_profile', 'AuthController@edit_profile')->name('Auth.edit_profile');
        Route::get('/my_info', 'AuthController@my_info')->name('Auth.my_info');
        Route::post('/logout', 'AuthController@logout')->name('user.logout');
    });

    /** Setting Route */
    Route::prefix('Setting')->group(function()
    {
        Route::get('/getSetting', 'SettingController@getSetting')->name('Setting.getSetting');
        Route::get('/search', 'SettingController@search')->name('Setting.search');
        Route::post('/updateSetting', 'SettingController@updateSetting')->name('Setting.updateSetting');
    });

    /** Product Routes */
    Route::prefix('Product')->group(function()
    {
        Route::get('/all', 'ProductController@all')->name('Product.single');
        Route::get('/single/{product_id}', 'ProductController@single')->name('Product.single');
        Route::post('/store', 'ProductController@store')->name('Product.store');
        Route::post('/update/{product_id}', 'ProductController@update')->name('Product.update');
    });

    /** Slider Routes */
    Route::prefix('Slider')->group(function()
    {
        Route::get('/all', 'SliderController@all')->name('Slider.single');
        Route::get('/single/{id}', 'SliderController@single')->name('Slider.single');
        Route::post('/store', 'SliderController@store')->name('Slider.store');
        Route::post('/update/{id}', 'SliderController@update')->name('Slider.update');
        Route::post('/delete/{id}', 'SliderController@delete')->name('Slider.delete');
    });

    /** notifications Routes */
    Route::prefix('Notifications')->group(function()
    {
        Route::post('/send', 'NotificationsController@send')->name('Notifications.send');
        Route::get('/all', 'NotificationsController@all')->name('Notifications.all');
        Route::post('/delete/{id}', 'NotificationsController@delete')->name('Notifications.delete');
        Route::post('/resend/{id}', 'NotificationsController@resend')->name('Notifications.resend');
    });

    /** Reports Routes */
    Route::prefix('Reports')->group(function()
    {
        Route::get('/Driver/{userId}', 'ReportsController@Driver')->name('Reports.Driver');
        Route::get('/paymentType', 'ReportsController@paymentType')->name('Reports.paymentType');
        Route::get('/quantityOfProduct/{productId}', 'ReportsController@quantityOfProduct')->name('Reports.quantityOfProduct');
        Route::get('/counts', 'ReportsController@counts')->name('Reports.counts');
    });


    /** Order Routes */
    Route::prefix('Order')->group(function()
    {
        Route::get('/all', 'OrderController@all')->name('Order.all');
        Route::get('/single/{order_id}', 'OrderController@single')->name('Order.single');
        Route::post('/delete/{order_id}', 'OrderController@delete')->name('Order.delete');
    });

    /** Users Routes */
    Route::prefix('User')->group(function()
    {
        Route::get('/all', 'UserController@all')->name('User.all');
        Route::get('/single/{order_id}', 'UserController@single')->name('User.single');
        Route::post('/delete/{order_id}', 'UserController@delete')->name('User.delete');
        Route::post('/activeDriver/{order_id}', 'UserController@activeDriver')->name('User.activeDriver');
    });

    /** Codes Routes */
    Route::prefix('Codes')->group(function()
    {
        Route::get('/all', 'CodesController@all')->name('Codes.all');
        Route::get('/single/{order_id}', 'CodesController@single')->name('Codes.single');
        Route::post('/delete/{order_id}', 'CodesController@delete')->name('Codes.delete');
        Route::post('/store', 'CodesController@store')->name('Codes.store');
        Route::post('/update/{code_id}', 'CodesController@update')->name('Codes.update');
        Route::post('/addUserToCode/{code_id}', 'CodesController@addUserToCode')->name('Codes.addUserToCode');
        Route::post('/updateStatus/{code_id}', 'CodesController@updateStatus')->name('Codes.updateStatus');
    });

    /** ProductDetails Routes */
    Route::prefix('ProductDetails')->group(function()
    {
        Route::post('/addColors/{product_id}', 'ProductColorController@addColors')->name('ProductDetails.addColors');
        Route::post('/removeColor/{product_id}', 'ProductColorController@removeColor')->name('ProductDetails.removeColor');
        Route::post('/addSize/{product_id}', 'ProductSizeController@addSize')->name('ProductDetails.addSize');
        Route::post('/removeSize/{product_id}', 'ProductSizeController@removeSize')->name('ProductDetails.removeSize');
        Route::post('/addImage/{product_id}', 'ProductImagesController@addImage')->name('ProductDetails.addImage');
        Route::post('/removeImage/{product_id}', 'ProductImagesController@removeImage')->name('ProductDetails.removeImage');
    });

    /** Category Routes */
    Route::prefix('Category')->group(function()
    {
        Route::get('/all', 'CategoryController@all')->name('Category.single');
        Route::get('/single/{cat_id}', 'CategoryController@single')->name('Category.single');
        Route::post('/store', 'CategoryController@store')->name('Category.store');
        Route::post('/update/{id}', 'CategoryController@update')->name('Category.update');
        Route::post('/delete/{id}', 'CategoryController@delete')->name('Category.delete');
    });

    /** Admin Routes */
    Route::prefix('Admin')->group(function()
    {
        Route::get('/all', 'AdminController@all')->name('Admin.single');
        Route::get('/single/{cat_id}', 'AdminController@single')->name('Admin.single');
        Route::post('/store', 'AdminController@store')->name('Admin.store');
        Route::post('/update/{id}', 'AdminController@update')->name('Admin.update');
        Route::post('/delete/{id}', 'AdminController@delete')->name('Admin.delete');
    });



    /** Color Routes */
    Route::prefix('Color')->group(function()
    {
        Route::get('/all', 'ColorController@all')->name('Color.all');
        Route::get('/single/{id}', 'ColorController@single')->name('Color.single');
        Route::post('/store', 'ColorController@store')->name('Color.store');
        Route::post('/update/{id}', 'ColorController@update')->name('Color.update');
        Route::post('/delete/{id}', 'ColorController@delete')->name('Color.delete');
    });

    /** Size Routes */
    Route::prefix('Size')->group(function()
    {
        Route::get('/all', 'SizeController@all')->name('Size.all');
        Route::get('/single/{id}', 'SizeController@single')->name('Size.single');
        Route::post('/store', 'SizeController@store')->name('Size.store');
        Route::post('/update/{id}', 'SizeController@update')->name('Size.update');
        Route::post('/delete/{id}', 'SizeController@delete')->name('Size.delete');
    });


});
/** End Auth Route **/

/** Auth_general */

Route::prefix('Auth_general')->group(function()
{
    Route::post('/login', 'AuthController@login')->name('Auth.login');
    Route::post('/forget_password', 'AuthController@forget_password')->name('Auth.forget_password');
    Route::post('/reset_password', 'AuthController@reset_password')->name('Auth.reset_password');
});
