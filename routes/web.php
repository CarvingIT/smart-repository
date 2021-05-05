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
Auth::routes(['verify'=>true]);

Route::view('/','welcome');

Route::get('/lang/{locale}', function ($locale) {
    App::setLocale($locale);
    return redirect('/');
});

Route::get('/contact', function () {
    return view('contact');
});
Route::get('/features', function () {
    return view('features');
});
Route::get('/faq', function () {
    return view('faq');
});


Route::get('/dashboard', 'HomeController@index')->name('dashboard');
Route::get('/collections', 'CollectionController@list');
Route::get('/documents', 'DocumentController@list');

Route::get('/collection/{collection_id}', 'CollectionController@collection')->middleware('collection_view');
Route::get('/collection/{collection_id}/export', 'CollectionController@export')->middleware('maintainer');

// Document upload/edit
Route::get('/collection/{collection_id}/upload', 'DocumentController@showUploadForm')->middleware('document_add');
Route::post('/collection/{collection_id}/upload','DocumentController@upload')->middleware('document_add');


// Collection-user management
Route::get('/collection/{collection_id}/users', 'CollectionController@collectionUsers')->middleware('maintainer');
Route::get('/collection/{collection_id}/user', 'CollectionController@showCollectionUserForm')->middleware('maintainer');

// Collection-user management
Route::get('/collection/{collection_id}/save_exclude_sites', 'CollectionController@collectionUrls')->middleware('maintainer');
Route::post('/collection/{collection_id}/savecollectionurls', 'CollectionController@saveCollectionUrls')->middleware('maintainer');

Route::get('autocomplete', 'UserController@autoComplete')->name('autocomplete');

Route::get('/collection/{collection_id}/user/{user_id}', 'CollectionController@showCollectionUserForm');
Route::post('/collection/{collection_id}/savecollectionuser', 'CollectionController@saveUser');
Route::get('/collection/{collection_id}/remove-user/{user_id}', 'CollectionController@removeUser');

//search within a collection
Route::get('/collection/{collection_id}/search', 'CollectionController@search')->middleware('collection_view');
// search all accessible documents
Route::get('/documents/search', 'CollectionController@search');

// Meta information
Route::get('/collection/{collection_id}/meta', 'CollectionController@metaInformation')->middleware('maintainer');
Route::get('/collection/{collection_id}/meta/{meta_field_id}', 'CollectionController@metaInformation')->middleware('maintainer');
Route::post('/collection/{collection_id}/meta', 'CollectionController@saveMeta')->middleware('maintainer');
Route::get('/collection/{collection_id}/meta/{meta_field_id}/delete', 'CollectionController@deleteMetaField')->middleware('maintainer');
// column config
Route::get('/collection/{collection_id}/settings', 'CollectionController@showSettingsForm')->middleware('maintainer');
Route::post('/collection/{collection_id}/settings', 'CollectionController@saveSettings')->middleware('maintainer');

Route::get('/collection/{collection_id}/metafilters', 'CollectionController@metaFiltersForm');
Route::post('/collection/{collection_id}/metafilters', 'CollectionController@addMetaFilter');
Route::post('/collection/{collection_id}/quickmetafilters', 'CollectionController@replaceMetaFilter');
Route::post('/collection/{collection_id}/quicktitlefilter', 'CollectionController@replaceTitleFilter');

Route::get('/collection/{collection_id}/removefilter/{field_id}', 'CollectionController@removeMetaFilter');
Route::get('/collection/{collection_id}/removeallfilters', 'CollectionController@removeAllMetaFilters');
Route::get('/collection/{collection_id}/removetitlefilter', 'CollectionController@removeTitleFilter');
Route::get('/collection/{collection_id}/removeallfilters', 'CollectionController@removeAllFilters');
// Document routes
Route::get('/collection/{collection_id}/document/{document_id}', 'DocumentController@loadDocument')->middleware('document_view');
Route::get('/document/{document_id}/edit', 'DocumentController@showEditForm')->middleware('document_edit');
Route::post('/document/delete', 'DocumentController@deleteDocument')->middleware('document_delete');
Route::get('/document/{document_id}/revisions', 'DocumentController@documentRevisions')->middleware('document_view');
Route::get('/document-revision/{revision_id}', 'DocumentController@loadRevision');//->middleware('revision_view');
// Document details (meta)
Route::get('/collection/{collection_id}/document/{document_id}/details', 'DocumentController@showDetails')->middleware('document_view');
Route::get('/collection/{collection_id}/document/{document_id}/proofread', 'DocumentController@proofRead')->middleware('document_view');
// See Diff in revisions
Route::get('/document/{document_id}/revision-diff/{rev1_id}/{rev2_id}', 'DocumentController@showRevisionDiff')->middleware('document_view');

// reports
Route::get('/reports', 'ReportsController@index')->middleware('admin');
Route::get('/reports/downloads', 'ReportsController@downloads')->middleware('admin');
Route::get('/reports/uploads', 'ReportsController@uploads')->middleware('admin');

// admin routes
Route::get('/admin','AdminController@index')->name('adminhome');
Route::get('/admin/collectionmanagement', 'CollectionController@index')->middleware('admin');
Route::get('/admin/collection-form/{collection_id}', 'CollectionController@add_edit_collection')->middleware('admin');
Route::post('/admin/collection-form/delete', 'CollectionController@deleteCollection')->middleware('admin');
Route::post('/admin/savecollection', 'CollectionController@save')->middleware('admin');
Route::get('/admin/usermanagement', 'UserController@index')->middleware('admin');
Route::post('/admin/user/delete','UserController@destroy')->middleware('admin');
// system config
Route::get('/admin/sysconfig','SysConfigController@index')->middleware('admin');
Route::post('/admin/sysconfig','SysConfigController@save')->middleware('admin');

Route::get('/home', 'HomeController@index')->name('home')->middleware('auth');
//Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => 'auth'], function () {
	Route::get('table-list', function () {
		return view('pages.table_list');
	})->name('table');

	Route::get('typography', function () {
		return view('pages.typography');
	})->name('typography');

	Route::get('icons', function () {
		return view('pages.icons');
	})->name('icons');

	Route::get('map', function () {
		return view('pages.map');
	})->name('map');

	Route::get('notifications', function () {
		return view('pages.notifications');
	})->name('notifications');

	Route::get('rtl-support', function () {
		return view('pages.language');
	})->name('language');

	Route::get('upgrade', function () {
		return view('pages.upgrade');
	})->name('upgrade');
});

Route::group(['middleware' => ['auth', 'admin']], function () {
	Route::resource('user', 'UserController', ['except' => ['show']]);
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
});

// redirect registration to login if registration is disabled
if(env('ENABLE_REGISTRATION') != 1){
	Route::redirect('register', 'login', 301);
}
