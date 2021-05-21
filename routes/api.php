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


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
