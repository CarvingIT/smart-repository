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
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

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

Route::get('/dashboard', 'HomeController@index')->name('dashboard')->middleware(['auth','verified']);
Route::get('/collections', 'CollectionController@list');
Route::get('/documents', 'DocumentController@list');

Route::get('/collection/{collection_id}', 'CollectionController@collection')->middleware('collection_view');
Route::get('/collection/{collection_id}/export', 'CollectionController@export')->middleware('maintainer');
Route::get('/collection/{collection_id}/exportxlsx', 'CollectionController@exportXlsx')->middleware('maintainer');

// Document upload/edit
Route::get('/collection/{collection_id}/upload', 'DocumentController@showUploadForm')->middleware('document_add');
Route::post('/collection/{collection_id}/upload','DocumentController@upload')->middleware('document_add');
Route::post('/collection/move_document', 'DocumentController@move');
Route::post('/approve-document', 'DocumentController@approveDocument');

//Document Comment
Route::post('/save-comment', 'CommentController@save');

// Document import queue via url
Route::get('/collection/{collection_id}/url-import', 'URLImportController@index')->middleware('document_add');
Route::post('/collection/{collection_id}/url-import', 'URLImportController@add')->middleware('document_add');
Route::get('/collection/{collection_id}/import-link/{link_id}/delete', 'URLImportController@delete')->middleware('document_add');

// Collection-user management
Route::get('/collection/{collection_id}/users', 'CollectionController@collectionUsers')->middleware('maintainer');
Route::get('/collection/{collection_id}/user', 'CollectionController@showCollectionUserForm')->middleware('maintainer');

// child-collection
Route::get('/collection/{collection_id}/child-collection/{child_collection_id}', 'CollectionController@showChildCollectionForm')->middleware('maintainer');
Route::post('/collection/{collection_id}/save-child-collection', 'CollectionController@saveChildCollection')->middleware('maintainer');

// Collection-user management
Route::get('/collection/{collection_id}/save_exclude_sites', 'CollectionController@collectionUrls')->middleware('maintainer');
Route::get('/collection/{collection_id}/remove-spidered-domain/{domain_id}', 'CollectionController@removeSpideredDomain')->middleware('maintainer');
Route::get('/collection/{collection_id}/remove-desired-link/{link_id}', 'CollectionController@removeDesiredLink')->middleware('maintainer');
Route::get('/collection/{collection_id}/remove-excluded-link/{link_id}', 'CollectionController@removeExcludedLink')->middleware('maintainer');
Route::post('/collection/{collection_id}/savecollectionurls', 'CollectionController@saveCollectionUrls')->middleware('maintainer');

Route::get('autocomplete', 'UserController@autoComplete')->name('autocomplete');
Route::get('autosuggest', 'CollectionController@autoSuggest')->name('autosuggest');
Route::get('titlesuggest', 'DocumentController@titleSuggest')->name('titlesuggest');

Route::get('/collection/{collection_id}/user/{user_id}', 'CollectionController@showCollectionUserForm');
Route::post('/collection/{collection_id}/savecollectionuser', 'CollectionController@saveUser');
Route::get('/collection/{collection_id}/remove-user/{user_id}', 'CollectionController@removeUser');

//search within a collection
Route::get('/collection/{collection_id}/search', 'CollectionController@search')->middleware('collection_view');
// search all accessible documents
Route::get('/documents/search', 'CollectionController@search');
Route::get('/documents/isa_document_search', 'CollectionController@isaCollectionDocumentSearch');
//Route::get('/documents/isa_document_search', 'CollectionController@searchDB');

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
// media route; just like the document download route
Route::get('/media/i/{filename}', 'MediaController@loadImage');


// Document routes
Route::get('/collection/{collection_id}/document/{document_id}', 'DocumentController@loadDocument')->middleware('document_view');
Route::get('/collection/{collection_id}/document/{document_id}/pdf-reader', 'DocumentController@pdfReader')->middleware('document_view');
Route::get('/collection/{collection_id}/document/{document_id}/media-player', 'DocumentController@mediaPlayer')->middleware('auth');

//Multiple file upload routes
Route::get('/collection/{collection_id}/document/{document_id}/details/{path_count}', 'DocumentController@loadDocument')->middleware('document_view');
Route::get('/collection/{collection_id}/document/{document_id}/pdf-reader/{path_count}', 'DocumentController@pdfReader')->middleware('document_view');
Route::get('/collection/{collection_id}/document/{document_id}/media-player/{path_count}', 'DocumentController@mediaPlayer')->middleware('auth');

Route::get('/document/{document_id}/edit', 'DocumentController@showEditForm')->middleware('document_edit');
Route::post('/document/{document_id}/lock-unlock', 'DocumentController@lockUnlockDocument')->middleware('document_edit');
Route::post('/document/delete', 'DocumentController@deleteDocument')->middleware('document_delete');
Route::get('/document/{document_id}/revisions', 'DocumentController@documentRevisions')->middleware('document_view');
Route::get('/document-revision/{revision_id}', 'DocumentController@loadRevision');//->middleware('revision_view');
Route::post('/document/delete', 'DocumentController@deleteDocument')->middleware('document_delete');
// Upload documents with same meta-data
Route::get('/collection/{collection_id}/document/{document_id}/same-meta-upload', 'DocumentController@sameMetaUpload')->middleware('document_add');
// Document details (meta)
Route::get('/collection/{collection_id}/document/{document_id}/details', 'DocumentController@showDetails')->middleware('document_view');
Route::get('/collection/{collection_id}/document/{document_id}/proofread', 'DocumentController@proofRead')->middleware('document_view');



// See Diff in revisions
Route::get('/document/{document_id}/revision-diff/{rev1_id}/{rev2_id}', 'DocumentController@showRevisionDiff')->middleware('document_view');
Route::get('/user/{user_id}/mydocs', 'DocumentController@listMyDocuments');

// user downloads
Route::get('/user/downloads','ReportsController@userDownloads')->middleware(['auth','verfified']);
// Approvals
Route::get('/document/{document_id}/approval', 'ApprovalsController@docApprovalForm');
Route::post('/approvals/{approvable}/{approvable_id}/save_status', 'ApprovalsController@saveApprovalStatus');
//Documents Approved by Me
Route::get('/approvals/{approvable}/{status}', 'ApprovalsController@listByStatus');

// reports
Route::get('/reports', 'ReportsController@index')->middleware('admin');
Route::get('/reports/downloads', 'ReportsController@downloads')->middleware('admin');
Route::get('/reports/uploads', 'ReportsController@uploads')->middleware('admin');
Route::get('/reports/search-queries', 'ReportsController@searchQueries')->middleware('admin');
Route::get('/reports/duplicates', 'ReportsController@duplicates')->middleware('admin');

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

// deleted documents
Route::get('/admin/deleted-documents','DocumentController@deletedDocuments')->middleware('admin');
Route::get('/admin/deleted-documents-data','DocumentController@deletedDocumentsData')->middleware('admin');
Route::get('/admin/recover/{document_id}','DocumentController@recoverDocument')->middleware('admin');

// storage/disk management
Route::get('/admin/storagemanagement', 'DisksController@index')->middleware('admin');
Route::get('/admin/disk-form/{disk_id}', 'DisksController@add_edit_disk')->middleware('admin');
Route::post('/admin/savedisk', 'DisksController@save')->middleware('admin');
Route::post('/admin/disk/delete','DisksController@delete')->middleware('admin');

Route::get('/home', 'HomeController@index')->name('home')->middleware(['auth','verified']);
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

Route::group(['middleware' => ['auth', 'verified']], function () {
	Route::resource('user', 'UserController', ['except' => ['show']]);
	Route::get('profile', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
	Route::put('profile', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
	Route::put('profile/password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
	Route::resource('template', 'SRTemplateController', ['except' => ['show']]);
});

Route::post('/user/regenerate-api-token', 'ApiTokenController@update')->middleware(['auth']);

// Oauth
Route::get('auth/social', '\App\Http\Controllers\Auth\LoginController@show')->name('social.login');
Route::get('oauth/{driver}', '\App\Http\Controllers\Auth\LoginController@redirectToProvider')->name('social.oauth');
Route::get('oauth/{driver}/callback', '\App\Http\Controllers\Auth\LoginController@handleProviderCallback')->name('social.callback');

// redirect registration to login if registration is disabled
if(env('ENABLE_REGISTRATION') != 1){
	Route::redirect('register', 'login', 301);
}

//Admin resources
Route::middleware('admin')->group(function () {
    Route::resource('synonyms', 'SynonymsController');
    Route::resource('taxonomies', 'TaxonomyController');
    Route::resource('botman-answers', 'BotmanAnswerController');
});

Route::get('/admin/synonymsmanagement', 'SynonymsController@index')->middleware('admin');
Route::post('/admin/synonyms/delete','SynonymsController@destroy')->middleware('admin');
Route::get('autocomplete', 'SynonymController@autoComplete')->name('autocomplete');

Route::get('/admin/taxonomiesmanagement', 'TaxonomyController@index')->middleware('admin');
Route::post('/admin/taxonomies/delete','TaxonomyController@destroy')->middleware('admin');
Route::get('autocomplete', 'TaxonomyController@autoComplete')->name('autocomplete');
Route::get('/taxonomies/{id}/add','TaxonomyController@add')->middleware('admin');
Route::post('/taxonomies/{id}/addstore','TaxonomyController@addstore')->middleware('admin')->name('taxonomies.addstore');   

// Templates Management
Route::get('/admin/srtemplatemanagement', 'SRTemplateController@index')->middleware('admin');
Route::post('/admin/template/delete','SRTemplateController@destroy')->middleware('admin');

//Role routes
Route::middleware('admin')->group(function () {
    Route::resource('roles', 'RoleController');
});

Route::get('/admin/rolesmanagement', 'RoleController@index')->middleware('admin');
Route::post('/admin/roles/delete','RoleController@destroy')->middleware('admin');
Route::get('autocomplete', 'RoleController@autoComplete')->name('autocomplete');

Route::match(['get', 'post'], '/botman', 'BotManController@handle');
Route::get('/admin/export-botman-data','BotManController@exportQuestionAnswers')->middleware('admin');

//Countries and Themes

Route::get('/countries', 'CountryController@index');
Route::get('/themes', 'ThemeController@index');

// contact-us, feedback, faqs
Route::view('/contact-us', 'contact-us');
Route::view('/faq', 'faq');

// feedback form
Route::get('/feedback', function(){ return view('feedback-form'); });
Route::post('/feedback', '\App\Http\Controllers\ContactController@contact');
Route::get('/feedback-thank-you', function(){ return view('feedback-thank-you'); });

// About Repository
Route::view('/about', 'about-repository');

Route::get('/collection/{collection_id}/search-results', 'CollectionController@searchResults');

// related documents
Route::post('/collection/{collection_id}/document/{document_id}/add-related-document','RelatedDocumentController@addRelatedDocument')->middleware('maintainer');

// laravel file manager
Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function (){
    \UniSharp\LaravelFilemanager\Lfm::routes();
}
);
