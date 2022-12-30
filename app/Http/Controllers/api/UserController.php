<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Language;

class UserController extends BaseController
{
    public function getUsers(Request $request){
        $users = User::where('role',4);
        if (isset($request->to_age) && $request->to_age!="" &&  $request->from_age==""){
            $users = $users->where("age",">", $request->to_age);
        }
        if ($request->to_age=="" && isset($request->from_age) && $request->from_age!=""){
            $users = $users->where("age","<", $request->from_age);
        }
        if (isset($request->to_age) && $request->to_age!="" && isset($request->from_age) && $request->from_age!=""){
            $users = $users->whereRaw("age between '".$request->to_age."' and '".$request->from_age."'");
        }
        if(isset($request->language_id) && $request->language_id > 0){
            $language_id = $request->language_id;
            $users =  $users->WhereHas('user_language',function ($mainQuery) use($language_id) {
                $mainQuery->where('language_id', '=',$language_id);
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

        $data['users'] = $users_arr;
        $data['languages'] = $languages;
        return $this->sendResponseWithData($data,"Users Retrieved Successfully.");
    }
}