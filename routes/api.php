<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\AuthController; 
use App\Http\Controllers\api\UserController;
use App\Http\Controllers\api\ChatController;

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



Route::post('login', [AuthController::class, 'login']);
Route::post('verify_otp', [AuthController::class, 'verify_otp']);
Route::post('user_login_log', [AuthController::class, 'user_login_log']);
Route::post('getUsers',[UserController::class,'getUsers']);
Route::get('getPrice',[UserController::class,'getPrice']);
Route::post('update_token',[AuthController::class,'update_token']);

Route::post('update_subscription',[UserController::class,'update_subscription']);

Route::get('SendCallNotification',[ChatController::class,'SendCallNotification']);
Route::post('AutoSendMessage',[ChatController::class,'AutoSendMessage']);
Route::post('create-chat', [ChatController::class, 'CreateChat']);
Route::get('get-all-chat/{id}', [ChatController::class, 'GetAllChat']);
Route::get('personal-chat/{user_id}/{receiver_id}', [ChatController::class, 'PersonalChat']);
Route::get('unread-msg-count', [ChatController::class, 'UnreadMessageCount']);
Route::get('get_all_unread_msg_count', [ChatController::class, 'GetAllUnreadMessageCount']);

Route::group(['middleware' => 'auth:api'], function () {

});
