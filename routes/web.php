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

// Document upload/edit
Route::get('/collection/{collection_id}/upload', 'DocumentController@showUploadForm')->middleware('document_add');
Route::post('/collection/{collection_id}/upload','DocumentController@upload')->middleware('document_add');


// Collection-user management
Route::get('/collection/{collection_id}/users', 'CollectionController@collectionUsers')->middleware('collection_view');
Route::get('/collection/{collection_id}/user', 'CollectionController@showCollectionUserForm');
Route::get('/collection/{collection_id}/user/{user_id}', 'CollectionController@showCollectionUserForm');
Route::post('/collection/{collection_id}/savecollectionuser', 'CollectionController@saveUser');
Route::get('/collection/{collection_id}/remove-user/{user_id}', 'CollectionController@removeUser');

//search within a collection
Route::get('/collection/{collection_id}/search', 'CollectionController@search')->middleware('collection_view');

// Meta information
Route::get('/collection/{collection_id}/meta', 'CollectionController@metaInformation');
Route::get('/collection/{collection_id}/meta/{meta_field_id}', 'CollectionController@metaInformation');
Route::post('/collection/{collection_id}/meta', 'CollectionController@saveMeta');
Route::get('/meta/{meta_field_id}/delete', 'CollectionController@deleteMetaField');

Route::get('/collection/{collection_id}/metasearch', 'CollectionController@metaSearch');

// Document routes
Route::get('/document/{document_id}', 'DocumentController@loadDocument')->middleware('document_view');
Route::get('/document/{document_id}/edit', 'DocumentController@showEditForm')->middleware('document_edit');
Route::get('/document/{document_id}/delete', 'DocumentController@deleteDocument')->middleware('document_delete');
Route::get('/document/{document_id}/revisions', 'DocumentController@documentRevisions')->middleware('document_view');
Route::get('/document-revision/{revision_id}', 'DocumentController@loadRevision');//->middleware('revision_view');

// reports
Route::get('/reports', 'ReportsController@index');
Route::get('/reports/downloads', 'ReportsController@downloads');
Route::get('/reports/uploads', 'ReportsController@uploads');

// admin routes
Route::get('/admin','AdminController@index')->name('adminhome');
Route::get('/admin/collectionmanagement', 'CollectionController@index')->middleware('admin');
Route::get('/admin/collection-form/{collection_id}', 'CollectionController@add_edit_collection')->middleware('admin');
Route::post('/admin/savecollection', 'CollectionController@save')->middleware('admin');
Route::get('/admin/usermanagement', 'UserController@index')->middleware('admin');
Route::post('/admin/saveuser', 'UserController@save')->middleware('admin');
Route::get('/admin/user/{user_id}/delete','UserController@delete')->middleware('admin');
