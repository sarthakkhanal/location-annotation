<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| 
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware' => ['web']], function() {

  // Authentication Routes...
  Route::get('login', ['as'=>'login','uses'=>'Auth\AuthController@showLoginForm']);
  Route::post('login', 'Auth\AuthController@login');
  Route::get('logout', ['as' => 'logout','uses'=>'Auth\AuthController@logout']);
  
  //location annotation routes
  Route::get('/manual/tweets/location', ['as'=>'manual_tweets_location','uses'=>'Location\LocationController@index'])->middleware(['auth']);
  Route::post('/manual/tweets/location', ['as'=>'save_manual_tweets_location','uses'=>'Location\LocationController@save'])->middleware(['auth']);
  
});
