<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;


class AuthController extends BaseController
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $mobile_no = $request->mobile_no;
        $user = User::where('mobile_no',$mobile_no)->where('role',3)->first();
        if ($user){
            if($user->estatus != 1){
                return $this->sendError("Your account is de-activated by admin.", "Account De-active", []);
            }
            $data['otp'] =  mt_rand(100000,999999);
            $user->otp = $data['otp'];
            $user->otp_created_at = Carbon::now();
            $user->save();
            if($user->first_name == ""){
                $data['user_status'] = 'new_user';
            }else{
                $data['user_status'] = 'exist_user';    
            }
            $final_data = array();
            array_push($final_data,$data);

            //send_sms($mobile_no, $data['otp']);
            return $this->sendResponseWithData($final_data, 'User login successfully.');
        }else{
            $data['otp'] =  mt_rand(100000,999999);
            
            
           // $user = User::create(['mobile_no'=>$mobile_no,'premiumuserid'=>$preserid,'role'=>3,'otp'=>$data['otp'],'otp_created_at'=>Carbon::now(),'referral_id'=>Str::random(5)]);

            $user = new User();
            $user->mobile_no = $mobile_no;
           
            $user->role = 3;
            $user->otp = $data['otp'];
            $user->otp_created_at = Carbon::now();
            $user->save();
            $data['user_status'] = 'new_user';
            $final_data = array();
            array_push($final_data,$data);

            //send_sms($mobile_no, $data['otp']);
            return $this->sendResponseWithData($final_data, 'User registered successfully.');
        }
    }

    public function verify_otp(Request $request){

        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required',
            'otp' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('mobile_no',$request->mobile_no)->where('otp',$request->otp)->where('estatus',1)->first();

        if ($user && isset($user['otp_created_at']))
        {
            $user->otp = null;
            $user->otp_created_at = null;
            //$user->is_verify = 1;
            $user->save();
            $user['token'] =  $user->createToken('P00j@13579WebV#d@n%p')->accessToken;
            //$data =  new UserResource($user);
            // $final_data = array();
            // array_push($final_data,$data);
            return $this->sendResponseWithData($user,'OTP verified successfully.');
        }
        else{
            return $this->sendError('OTP verification Failed.', "verification Failed", []);
        }
    }

    public function user_login_log(Request $request){

        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if($validator->fails()){
            return $this->sendError($validator->errors(), "Validation Errors", []);
        }

        $user = User::where('id',$request->user_id)->where('estatus',1)->first();
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
