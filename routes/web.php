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
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/collections', 'CollectionController@list');

Route::get('/collection/{collection_id}', 'CollectionController@collection')->middleware('collection_view');
Route::get('/collection/{collection_id}/upload', 'UploadDocument@showForm')->middleware('collection_view');
Route::post('/collection/{collection_id}/upload','UploadDocument@upload');

Route::get('/collection/{collection_id}/document/{document_id}', 'DocumentController@loadDocument')->middleware('collection_view');


// admin routes
Route::get('/admin','AdminController@index')->name('adminhome');
Route::get('/admin/collectionmanagement', 'CollectionController@index')->middleware('admin');
Route::get('/admin/collection-form/{collection_id}', 'CollectionController@add_edit_collection')->middleware('admin');
Route::post('/admin/savecollection', 'CollectionController@save')->middleware('admin');
Route::get('/admin/usermanagement', 'UserController@index')->middleware('admin');
Route::post('/admin/saveuser', 'UserController@save')->middleware('admin');
Route::get('/admin/user/{user_id}/delete','UserController@delete')->middleware('admin');
