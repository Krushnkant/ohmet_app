<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Stevebauman\Location\Facades\Location;
use Jenssegers\Agent\Facades\Agent;
use App\Models\User;
use App\Models\UserLogin;
use App\Models\Setting;


class AuthController extends Controller
{
   

    public function index()
    {
        $setting = Setting::first('company_name');
        return view('admin.auth.login')->with('page',$setting->company_name);
    }

    public function invalid_page()
    {
        return view('admin.403_page');
    }

    public function postLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        $user = User::where('email',$request->email)->where('decrypted_password',$request->password)->whereNotIn('role',['3'])->first();
        if ($user) {
        if($user->estatus == 1){    
            $credentials = $request->only('email', 'password');
            if (Auth::attempt($credentials)) {
                    $position = Location::get($request->ip());
                    $browser = Agent::browser();
                    $user->last_login_date = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
                    $user->save(); 

                    $userlogin = New UserLogin();
                    $userlogin->user_id =  $user->id;
                    $userlogin->ip_address =  $request->ip();
                    $userlogin->country =  isset($position->countryName)?$position->countryName:"";
                    $userlogin->state =  isset($position->regionName)?$position->regionName:"";
                    $userlogin->city =  isset($position->cityName)?$position->cityName:"";
                    $userlogin->browser =  isset($browser)?$browser:"";
                    $userlogin->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
                    $userlogin->save();

                return response()->json(['status'=>200]);
       
            }
        }else{
            return response()->json(['status'=>300]);
        }    
        }
        return response()->json(['status'=>400]);
//        return redirect("admin")->withSuccess('Oppes! You have entered invalid credentials');
    }

    /*public function dashboard()
    {
        if(Auth::check()){
            return view('admin.dashboard');
        }

        return redirect("admin")->withSuccess('Opps! You do not have access');
    }*/

    public function logout() {
        Session::flush();
        Auth::logout();

        return Redirect('admin');
    }
}
