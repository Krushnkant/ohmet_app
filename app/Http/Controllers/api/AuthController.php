<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\CustomerDeviceToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class AuthController extends BaseController
{
    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'email' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }
        $email = $request->email;
        $user = User::where('email',$email)->where('id',$request->user_id)->where('role',3)->first();
        if($user){
            if($user->estatus != 1){
                return $this->sendError("Your account is de-activated by admin.", "Account De-active", []);
            }

            $user = User::find($request->user_id);
            $user->gmail_key = $request->gmail_key;
            $user->save();
            
            $data['token'] =  $user->createToken('Ohmet@13579WebV#d@n%p')->accessToken;
            $data['user_status'] = 'exist_user';    
            $final_data = array();
            array_push($final_data,$data);

            return $this->sendResponseWithData($final_data, 'User login successfully.');
        }else{
        
            $user = User::find($request->user_id);
            $user->email = $email;
            $user->gmail_key = $request->gmail_key;
            $user->save();
            $data['token'] =  $user->createToken('Ohmet@13579WebV#d@n%p')->accessToken;
            $data['user_status'] = 'new_user';
            $final_data = array();
            array_push($final_data,$data);
            return $this->sendResponseWithData($final_data, 'User registered successfully.');
        }
    }


    public function update_token(Request $request){
        $validator = Validator::make($request->all(), [
            'device_id' => 'required',
            'token' => 'required',
            'device_type' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('device_id',$request->device_id)->where('estatus',1)->where('role',3)->first();
        if (!$user){
            $user = New User();
            $user->device_id = $request->device_id;
            $user->role = 3;
            $user->save();
            //$data['token'] =  $user->createToken('Ohmet@13579WebV#d@n%p')->accessToken;
        }

        $device = CustomerDeviceToken::where('device_id',$request->device_id)->first();
        if ($device){
            $device->token = $request->token;
            $device->device_type = $request->device_type;
        }
        else{
            $device = new CustomerDeviceToken();
            $device->device_id = $request->device_id;
            $device->token = $request->token;
            $device->device_type = $request->device_type;
        }
        $device->save();
        $this->user_login_log($user->id);
        return $this->sendResponseWithData($user,"Device Token updated.");
    }

  
    public function user_login_log($id){

    
        $user = User::where('id',$id)->where('estatus',1)->first();
        if ($user)
        {
            $user->last_login_date = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $user->save(); 

            $userlogin = New UserLogin();
            $userlogin->user_id =  $user->id;
            $userlogin->country =  isset($request->countryName)?$request->countryName:"";
            $userlogin->state =  isset($request->regionName)?$request->regionName:"";
            $userlogin->city =  isset($request->cityName)?$request->cityName:"";
            $userlogin->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
            $userlogin->save();
            return $this->sendResponseSuccess('log create successfully.');
        }
        else{
            return $this->sendError('User Not Found.', "verification Failed", []);
        }
    }
}
