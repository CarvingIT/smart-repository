<?php

use Illuminate\Http\Request;
use App\Collection;

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

Route::middleware('auth:api')->get('/collections','CollectionController@userCollections'); 
Route::middleware('auth:api')->get('/collection/{collection_id}/meta-information', function ($collection_id, Request $request){
	$collection = Collection::find($collection_id);
	return $collection->meta_fields()->orderby('display_order','ASC')->get();
}); 

Route::middleware('auth:api')->post('/collection/{collection_id}/upload', 'DocumentController@uploadFile');
Route::middleware('auth:api')->get('/collection/{collection_id}/search', 'CollectionController@search');
Route::middleware('auth:api')->get('/user/permissions', function (Request $request){
    return $request->user()->accessPermissions();
});
Route::middleware('auth:api')->get('/permissions', function (Request $request){
    return \App\Permission::all();
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// revisions of a document
Route::middleware('auth:api')->get('/document/{document_id}/revisions', function ($document_id){
	return \App\DocumentRevision::where('document_id','=', $document_id)
    ->orderBy('id','DESC')->get();
});
