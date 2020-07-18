<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();


// grouping route
// secara otomatis diawali dengan administrator
// contoh : /administrator/category , /administrator/product
Route::group(['prefix' => 'administrator', 'middleware' => 'auth'], function() {
    Route::get('/home', 'HomeController@index')->name('home');

    // ini adalah router baru
    Route::resource('category', 'CategoryController')->except(['create','show']);
    // Route di atas sama saja dengan ke 5 route di bawah ini , kecuali 2 route yang lain
    // Route::get('/category', 'CategoryController@index')->name('category.index');
    // Route::post('/category', 'CategoryController@store')->name('category.store');
    // Route::get('/category/{category_id}/edit', 'CategoryController@edit')->name('category.edit');
    // Route::put('/category/{category_id}', 'CategoryController@update')->name('category.update');
    // Route::delete('/category/{category_id}', 'CategoryController@destroy')->name('category.destroy');
    // kecuali
    // Route::get('/category/{category_id}', 'CategoryController@show')->name('category.show');
    // Route::get('/category/create', 'CategoryController@create')->name('category.create');

});
Route::get('/home', 'HomeController@index')->name('home');
