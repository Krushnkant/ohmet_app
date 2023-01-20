<?php

use Illuminate\Support\Facades\Route;

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

Route::get('admin',[\App\Http\Controllers\admin\AuthController::class,'index'])->name('admin.login');
Route::post('adminpostlogin', [\App\Http\Controllers\admin\AuthController::class, 'postLogin'])->name('admin.postlogin');
Route::get('logout', [\App\Http\Controllers\admin\AuthController::class, 'logout'])->name('admin.logout');
Route::get('admin/403_page',[\App\Http\Controllers\admin\AuthController::class,'invalid_page'])->name('admin.403_page');

Route::group(['prefix'=>'admin','middleware'=>['auth','userpermission'],'as'=>'admin.'],function () {
        Route::get('dashboard', [\App\Http\Controllers\admin\DashboardController::class, 'index'])->name('dashboard');

        Route::get('users',[\App\Http\Controllers\admin\UserController::class,'index'])->name('users.list');
        Route::post('addorupdateuser',[\App\Http\Controllers\admin\UserController::class,'addorupdateuser'])->name('users.addorupdate');
        Route::post('alluserslist',[\App\Http\Controllers\admin\UserController::class,'alluserslist'])->name('alluserslist');
        Route::get('changeuserstatus/{id}',[\App\Http\Controllers\admin\UserController::class,'changeuserstatus'])->name('users.changeuserstatus');
        Route::get('users/{id}/edit',[\App\Http\Controllers\admin\UserController::class,'edituser'])->name('users.edit');
        Route::get('users/{id}/delete',[\App\Http\Controllers\admin\UserController::class,'deleteuser'])->name('users.delete');
        Route::get('users/{id}/permission',[\App\Http\Controllers\admin\UserController::class,'permissionuser'])->name('users.permission');
        Route::post('savepermission',[\App\Http\Controllers\admin\UserController::class,'savepermission'])->name('users.savepermission');

        Route::get('end_users',[\App\Http\Controllers\admin\EndUserController::class,'index'])->name('end_users.list');
        Route::get('enduser/create',[\App\Http\Controllers\admin\EndUserController::class,'create'])->name('enduser.add');
        Route::post('addorupdateEnduser',[\App\Http\Controllers\admin\EndUserController::class,'addorupdateEnduser'])->name('end_users.addorupdate');
        Route::post('allEnduserlist',[\App\Http\Controllers\admin\EndUserController::class,'allEnduserlist'])->name('allEnduserlist');
        Route::get('changeEnduserstatus/{id}',[\App\Http\Controllers\admin\EndUserController::class,'changeEnduserstatus'])->name('end_users.changeEnduserstatus');
        Route::get('end_users/{id}/delete',[\App\Http\Controllers\admin\EndUserController::class,'deleteEnduser'])->name('end_users.delete');
        Route::get('end_users/{id}/edit',[\App\Http\Controllers\admin\EndUserController::class,'edit'])->name('end_users.edit');
        Route::post('enduser/uploadfile',[\App\Http\Controllers\admin\EndUserController::class,'uploadfile'])->name('enduser.uploadfile');
        Route::post('enduser/uploadvideofile',[\App\Http\Controllers\admin\EndUserController::class,'uploadvideofile'])->name('enduser.uploadvideofile');
        Route::post('enduser/uploadshotvideofile',[\App\Http\Controllers\admin\EndUserController::class,'uploadshotvideofile'])->name('enduser.uploadshotvideofile');
        Route::post('enduser/removefile',[\App\Http\Controllers\admin\EndUserController::class,'removefile'])->name('enduser.removefile');

        Route::get('languages',[\App\Http\Controllers\admin\LanguageController::class,'index'])->name('languages.list');
        Route::post('addorupdatelanguage',[\App\Http\Controllers\admin\LanguageController::class,'addorupdatelanguage'])->name('languages.addorupdate');
        Route::post('alllanguagelist',[\App\Http\Controllers\admin\LanguageController::class,'alllanguagelist'])->name('alllanguagelist');
        Route::get('languages/{id}/edit',[\App\Http\Controllers\admin\LanguageController::class,'editlanguage'])->name('languages.edit');
        Route::get('languages/{id}/delete',[\App\Http\Controllers\admin\LanguageController::class,'deletelanguage'])->name('languages.delete');
        Route::get('chagelanguagestatus/{id}',[\App\Http\Controllers\admin\LanguageController::class,'chagelanguagestatus'])->name('languages.chagelanguagestatus');

        Route::get('settings',[\App\Http\Controllers\admin\SettingsController::class,'index'])->name('settings.list');
        Route::post('updateInvoiceSetting',[\App\Http\Controllers\admin\SettingsController::class,'updateInvoiceSetting'])->name('settings.updateInvoiceSetting');
        Route::get('settings/edit',[\App\Http\Controllers\admin\SettingsController::class,'editSettings'])->name('settings.edit');

        Route::get('pricerange',[\App\Http\Controllers\admin\PriceRangeController::class,'index'])->name('pricerange.list');
        Route::post('addorupdatepricerange',[\App\Http\Controllers\admin\PriceRangeController::class,'addorupdatepricerange'])->name('pricerange.addorupdate');
        Route::post('allpricerangeslist',[\App\Http\Controllers\admin\PriceRangeController::class,'allpricerangeslist'])->name('allpricerangeslist');
        Route::get('changepricerangestatus/{id}',[\App\Http\Controllers\admin\PriceRangeController::class,'changepricerangestatus'])->name('pricerange.changepricerangestatus');
        Route::get('pricerange/{id}/edit',[\App\Http\Controllers\admin\PriceRangeController::class,'editpricerange'])->name('pricerange.edit');
        Route::get('pricerange/{id}/delete',[\App\Http\Controllers\admin\PriceRangeController::class,'deletepricerange'])->name('pricerange.delete');

        Route::get('subscription',[\App\Http\Controllers\admin\SubscriptionController::class,'index'])->name('subscription.list');
        Route::post('addorupdatesubscription',[\App\Http\Controllers\admin\SubscriptionController::class,'addorupdatesubscription'])->name('subscription.addorupdate');
        Route::post('allsubscriptionslist',[\App\Http\Controllers\admin\SubscriptionController::class,'allsubscriptionslist'])->name('allsubscriptionslist');
        Route::get('changesubscriptionstatus/{id}',[\App\Http\Controllers\admin\SubscriptionController::class,'changesubscriptionstatus'])->name('subscription.changesubscriptionstatus');
        Route::get('subscription/{id}/edit',[\App\Http\Controllers\admin\SubscriptionController::class,'editsubscription'])->name('subscription.edit');
        Route::get('subscription/{id}/delete',[\App\Http\Controllers\admin\SubscriptionController::class,'deletesubscription'])->name('subscription.delete');

        Route::get('messages',[\App\Http\Controllers\admin\MessageController::class,'index'])->name('messages.list');
        Route::post('addorupdatemessage',[\App\Http\Controllers\admin\MessageController::class,'addorupdatemessage'])->name('messages.addorupdate');
        Route::post('allmessagelist',[\App\Http\Controllers\admin\MessageController::class,'allmessagelist'])->name('allmessagelist');
        Route::get('messages/{id}/edit',[\App\Http\Controllers\admin\MessageController::class,'editmessage'])->name('messages.edit');
        Route::get('messages/{id}/delete',[\App\Http\Controllers\admin\MessageController::class,'deletemessage'])->name('messages.delete');
        Route::get('chagemessagestatus/{id}',[\App\Http\Controllers\admin\MessageController::class,'chagemessagestatus'])->name('messages.chagemessagestatus');

        Route::get('purchasecoin',[\App\Http\Controllers\admin\PriceRangeController::class,'purchasecoin'])->name('purchasecoin.list');
        Route::post('allpurchasecoinslist',[\App\Http\Controllers\admin\PriceRangeController::class,'allpurchasecoinslist'])->name('allpurchasecoinslist');
     

});

Route::group(['middleware'=>['auth']],function (){
    Route::get('profile',[\App\Http\Controllers\admin\ProfileController::class,'profile'])->name('profile');
    Route::get('profile/{id}/edit',[\App\Http\Controllers\admin\ProfileController::class,'edit'])->name('profile.edit');
    Route::post('profile/update',[\App\Http\Controllers\admin\ProfileController::class,'update'])->name('profile.update');

    
});