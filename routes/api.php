<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/signup', 'UserController@SignUp');
Route::post('/signup_admin', 'UserController@SignUp_Admin');
Route::post('/signin', 'UserController@SignIn');
Route::post('/recovery', 'UserController@Password_Recovery');
Route::post('/logout','UserController@Logout');
Route::post('/logout_alt','UserController@Logout_alt');
Route::post('/createProduct', 'ProductController@Create_Product');
Route::post('/editProduct', 'ProductController@Edit_Product');
Route::post('/deleteProduct', 'ProductController@Delete_Product');
Route::post('/searchProducts', 'ProductController@Search_Products');
Route::get('/token', function () {
        return csrf_token(); 
    });
