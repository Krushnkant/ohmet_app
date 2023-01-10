<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Language;
use App\Models\PriceRange;
use App\Models\Subscription;

class UserController extends BaseController
{
    public function getUsers(Request $request){
        $users = User::where('role',4);
        if (isset($request->from_age) && $request->from_age!="" &&  $request->to_age==""){
            $users = $users->where("age",">", $request->from_age);
        }
        if ($request->from_age=="" && isset($request->to_age) && $request->to_age!=""){
            $users = $users->where("age","<", $request->to_age);
        }
        if (isset($request->from_age) && $request->from_age!="" && isset($request->to_age) && $request->to_age!=""){
            $users = $users->whereRaw("age between '".$request->from_age."' and '".$request->to_age."'");
        }
        if(isset($request->language_id) && $request->language_id > 0){
            $language_id = explode(',',$request->language_id);
            
            $users =  $users->WhereHas('user_language',function ($mainQuery) use($language_id) {
              
                $mainQuery->whereIn('language_id',$language_id);
            });  
        }
        $users =  $users->where('estatus',1)->get();
        $users_arr = array();
        foreach ($users as $user){
            $images = explode(',',$user->images);
            $images_arr = array();
            foreach($images as $image){
                $images_arr[] = url($image);
            } 
            $temp = array();
            $temp['id'] = $user->id;
            $temp['first_name'] = $user->first_name;
            $temp['last_name'] = $user->last_name;
            $temp['email'] = $user->email;
            $temp['mobile_no'] = $user->mobile_no;
            $temp['age'] = $user->age;
            $temp['gender'] = $user->gender;
            $temp['bio'] = $user->bio;
            $temp['location'] = $user->location;
            $temp['rate_per_minite'] = $user->rate_per_minite;
            $temp['images'] = $images_arr;
            $temp['video'] = isset($user->video)?url($user->video):"";
            $temp['shot_video'] = isset($user->shot_video)?url($user->shot_video):"";
            array_push($users_arr,$temp);
        }

        $languages = Language::where('estatus',1)->get(['id','title']);
        $pricerange = PriceRange::where('estatus',1)->get(['id','price','coin']);

        $data['users'] = $users_arr;
        $data['languages'] = $languages;
        $data['pricerange'] = $pricerange;
        return $this->sendResponseWithData($data,"Users Retrieved Successfully.");
    }

    public function getPrice(Request $request){
       
        $subscription = Subscription::where('estatus',1)->orderByRaw('CONVERT(price, SIGNED) asc')->get(['id','price','title','key']);
        $pricerange = PriceRange::where('estatus',1)->orderByRaw('CONVERT(price, SIGNED) asc')->get(['id','price','coin','key']);

        $data['subscriptionPrice'] = $subscription;
        $data['coinprice'] = $pricerange;
        return $this->sendResponseWithData($data,"Price Retrieved Successfully.");
    }

    


}
