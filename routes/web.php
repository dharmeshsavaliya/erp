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

//Route::get('/', function () {
//    return view('welcome');
//});

Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');

Route::get('/productselection/list','ProductSelectionController@sList')->name('productselection.list');
Route::get('/productsearcher/list','ProductSearcherController@sList')->name('productsearcher.list');
// adding chat contro

Route::get('/message', 'MessageController@index')->name('message');
Route::post('/message', 'MessageController@store')->name('message.store');
Route::post('/message/{message}', 'MessageController@update')->name('message.update');
Route::get('/chat/getnew', 'ChatController@checkfornew')->name('checkfornew');
Route::get('/chat/updatenew', 'ChatController@updatefornew')->name('updatefornew');
	//Route::resource('/chat','ChatController@getmessages');

Route::group(['middleware'  => ['auth'] ], function (){

	Route::resource('roles','RoleController');
	Route::resource('users','UserController');
	Route::resource('products','ProductController');
	Route::resource('productselection','ProductSelectionController');
	Route::resource('productattribute','ProductAttributeController');
	Route::resource('productsearcher','ProductSearcherController');
	Route::resource('productimagecropper','ProductCropperController');
	Route::resource('productsupervisor','ProductSupervisorController');
	Route::resource('productlister','ProductListerController');
	Route::resource('productapprover','ProductApproverController');
	Route::resource('productinventory','ProductInventoryController');
	Route::resource('sales','SaleController');
//	Route::resource('activity','ActivityConroller');
	Route::resource('brand','BrandController');

	Route::resource('settings','SettingController');
	Route::resource('category','CategoryController');
	Route::resource('benchmark','BenchmarkController');

	// addding lead routes
	Route::resource('leads','LeadsController');
	Route::post('leads/{id}/changestatus', 'LeadsController@updateStatus');
	Route::delete('leads/permanentDelete/{leads}','LeadsController@permanentDelete')->name('leads.permanentDelete');
	Route::resource('chat','ChatController');
//	Route::resource('task','TaskController');

	Route::resource('order','OrderController');
	Route::post('order/{id}/changestatus', 'OrderController@updateStatus');
	Route::delete('order/permanentDelete/{order}','OrderController@permanentDelete')->name('order.permanentDelete');

	Route::resource('task','TaskModuleController');
	Route::resource('task_category','TaskCategoryController');
	Route::get('/', 'TaskModuleController@index')->name('home');

});

Route::middleware('auth')->group(function (){

	Route::get('/notifications' , 'NotificaitonContoller@index')->name('notifications');
	Route::get('/notificaitonsJson','NotificaitonContoller@json')->name('notificationJson');
	Route::get('/salesNotificaitonsJson','NotificaitonContoller@salesJson')->name('salesNotificationJson');
	Route::post('/notificationMarkRead/{notificaion}','NotificaitonContoller@markRead')->name('notificationMarkRead');

	Route::post('/productsupervisor/approve/{product}','ProductSupervisorController@approve')->name('productsupervisor.approve');
	Route::post('/productsupervisor/reject/{product}','ProductSupervisorController@reject')->name('productsupervisor.reject');
	Route::post('/productlister/isUploaded/{product}','ProductListerController@isUploaded')->name('productlister.isuploaded');
	Route::post('/productapprover/isFinal/{product}','ProductApproverController@isFinal')->name('productapprover.isfinal');

	Route::post('/productinventory/stock/{product}','ProductInventoryController@stock')->name('productinventory.stock');

	Route::get('category','CategoryController@manageCategory')->name('category');
	Route::post('add-category','CategoryController@addCategory')->name('add.category');
	Route::post('category/{category}/edit','CategoryController@edit')->name('category.edit');
	Route::post('category/remove','CategoryController@remove')->name('category.remove');

	Route::get('productSearch/','SaleController@searchProduct');
	Route::post('productSearch/','SaleController@searchProduct');

	Route::get('activity/','ActivityConroller@showActivity')->name('activity');
	Route::get('graph/','ActivityConroller@showGraph')->name('graph');
	Route::get('graph/user','ActivityConroller@showUserGraph')->name('graph_user');

	Route::get('search/','SearchController@search')->name('search');
	Route::get('pending/{roletype}','SearchController@getPendingProducts')->name('pending');

//	Route::post('productAttachToSale/{sale}/{product_id}','SaleController@attachProduct');
//	Route::get('productSelectionGrid/{sale}','SaleController@selectionGrid')->name('productSelectionGrid');

	//Attach Products
	Route::get('attachProducts/{model_type}/{model_id}','ProductController@attachProducts')->name('attachProducts');
	Route::post('attachProductToModel/{model_type}/{model_id}/{product_id}','ProductController@attachProductToModel')->name('attachProductToModel');
	Route::post('deleteOrderProduct/{order_product}','OrderController@deleteOrderProduct')->name('deleteOrderProduct');

	Route::get('attachImages/{model_type}/{model_id}/{status}/{assigned_user}','ProductController@attachImages')->name('attachImages');
	Route::post('download', 'MessageController@downloadImages')->name('download.images');

	//Comments
	Route::post('doComment','CommentController@store')->name('doComment');
	Route::post('deleteComment/{comment}','CommentController@destroy')->name('deleteComment');
	Route::get('message/updatestatus','MessageController@updatestatus')->name('message.updatestatus');
	Route::get('message/loadmore','MessageController@loadmore')->name('message.loadmore');

	//Push Notifications new
	Route::get('/pushNotifications','PushNotificationController@getJson')->name('pushNotifications');
	Route::post('/pushNotificationMarkRead/{push_notification}','PushNotificationController@markRead')->name('pushNotificationMarkRead');
	Route::post('/pushNotification/status/{push_notification}','PushNotificationController@changeStatus')->name('pushNotificationStatus');

	Route::post('dailyActivity/store','DailyActivityController@store')->name('dailyActivity.store');
	Route::get('dailyActivity/get','DailyActivityController@get')->name('dailyActivity.get');

	// Complete the task
	Route::get('/task/complete/{taskid}','TaskModuleController@complete')->name('task.complete');
	Route::get('/statutory-task/complete/{taskid}','TaskModuleController@statutoryComplete')->name('task.statutory.complete');
	Route::post('/task/addremark','TaskModuleController@addRemark')->name('task.addRemark');
	Route::get('tasks/getremark','TaskModuleController@getremark')->name('task.getremark');
	Route::get('tasks/gettaskremark','TaskModuleController@getTaskRemark')->name('task.gettaskremark');

	Route::post('tasks/deleteTask','TaskModuleController@deleteTask');
	Route::post('tasks/{id}/delete','TaskModuleController@archiveTask')->name('task.archive');
//	Route::get('task/completeStatutory/{satutory_task}','TaskModuleController@completeStatutory');
	Route::post('task/deleteStatutoryTask','TaskModuleController@deleteStatutoryTask');

	Route::post('task/export','TaskModuleController@exportTask')->name('task.export');
	Route::post('/task/addRemarkStatutory','TaskModuleController@addRemark')->name('task.addRemarkStatutory');

});


//Route::get('deQueueNotfication/','NotificationQueueController@deQueueNotfication');
Route::get('deQueueNotfication/','NotificationQueueController@deQueueNotficationNew');
Route::get('mageOrders/','MagentoController@get_magento_orders');

Route::get('perHourActivityNotification','NotificationQueueController@perHourActivityNotification');
Route::get('recurringTask','TaskModuleController@recurringTask');

Route::get('twilio/token', 'TwilioController@createToken');
Route::post('twilio/incoming', 'TwilioController@incomingCall');
Route::post('twilio/outgoing', 'TwilioController@outgoingCall');
Route::get('twilio/getLeadByNumber', 'TwilioController@getLeadByNumber');
Route::post('twilio/recordingStatusCallback', 'TwilioController@recordingStatusCallback');

Route::post('whatsapp/incoming', 'WhatsAppController@incomingMessage');
Route::post('whatsapp/sendMessage', 'WhatsAppController@sendMessage');
Route::get('whatsapp/pollMessages', 'WhatsAppController@pollMessages');




/*Routes For Social */

	// post creating routes define's here

Route::get('social/post/page','SocialController@index')->name('social.post.page');
Route::post('social/post/page/create','SocialController@createPost')->name('social.post.page.create');

	// End to Routes creating routes here


	// Ad reports routes 

Route::get('social/ad/report','SocialController@report')->name('social.report');
Route::get('social/ad/report/{ad_id}/{status}/','SocialController@changeAdStatus')->name('social.report.ad.status');

	// end to ad reports routes


// Creating Ad Campaign Routes defines here


Route::get('social/ad/campaign/create','SocialController@createCampaign')->name('social.ad.campaign.create');

Route::post('social/ad/campaign/store','SocialController@storeCampaign')->name('social.ad.campaign.store');



// Creating Adset Routes define here

Route::get('social/ad/adset/create','SocialController@createAdset')->name('social.ad.adset.create');
Route::post('social/ad/adset/store','SocialController@storeAdset')->name('social.ad.adset.store');


// Creating Ad Routes define here

Route::get('social/ad/create','SocialController@createAd')->name('social.ad.create');
Route::post('social/ad/store','SocialController@storeAd')->name('social.ad.store');


// End of Routes for social 