<?php

use Illuminate\Http\Request;
use App\Collection;
use App\Document;

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

Route::post('/authenticate','ApiAuthController@authenticate');
Route::middleware('auth:sanctum')->get('/collections','CollectionController@userCollections'); 
Route::middleware('auth:sanctum')->get('/collection/{collection_id}/search', 'CollectionController@search');
Route::middleware(['auth:sanctum', 'admin'])->post('/collections/subscribe-user', 'ApiCollectionSubscriptionController@subscribe');

// user details
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/collection/{collection_id}/document/{document_id}', 'DocumentController@loadDocument')->middleware(['auth:sanctum','document_view']);

Route::middleware('auth:sanctum')->get('/collection/{collection_id}/meta-information', function ($collection_id, Request $request){
	$collection = Collection::find($collection_id);
	return $collection->meta_fields()->orderby('display_order','ASC')->get();
}); 

Route::middleware('auth:sanctum')->get('/collection/{collection_id}/document/{document_id}/meta-values', function ($document_id, Request $request){
	$document = Document::find($document_id);
	return $document->meta;
}); 

Route::middleware('auth:api')->post('/collection/{collection_id}/upload', 'DocumentController@uploadFile');
Route::middleware('auth:api')->get('/user/permissions', function (Request $request){
    return $request->user()->accessPermissions();
});

// master list of available permissions within the system
Route::middleware('auth:sanctum')->get('/permissions', function (Request $request){
    return \App\Permission::all();
});



// revisions of a document
Route::middleware('auth:api')->get('/document/{document_id}/revisions', function ($document_id){
	return \App\DocumentRevision::where('document_id','=', $document_id)
    ->orderBy('id','DESC')->get();
});
