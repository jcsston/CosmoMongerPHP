<?php

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


    // $columns = Schema::getColumnListing('users'); // users table
    // dd($columns); // dump the result and die


    // $baseShip = new App\BaseShip;
    // $baseShip->name = 'Ship name';
    // $baseShip->save();

    // Log::info("BaseShips: $baseShips");
    return view('welcome');
});

Route::get('/login', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
