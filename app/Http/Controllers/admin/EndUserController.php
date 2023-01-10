<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\User;
use App\Models\UserLanguage;
use App\Models\UserLevel;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EndUserController extends Controller
{
    private $page = "Users";

    public function index(){
        $action = "list";
        $users = User::where('role',3)->get();
        return view('admin.end_users.list',compact('users','action'))->with('page',$this->page);
    }

    public function create(){
        $action = "create";
        $languages = Language::where('estatus',1)->get();
        return view('admin.end_users.list',compact('action','languages'))->with('page',$this->page);
    }

    

    public function allEnduserlist(Request $request){
        if ($request->ajax()) {
            $tab_type = $request->tab_type;
            if ($tab_type == "active_end_user_tab"){
                $estatus = 1;
            }
            elseif ($tab_type == "deactive_end_user_tab"){
                $estatus = 2;
            }

            $columns = array(
                0=>'id',
                1=>'profile_pic',
                2=> 'contact_info',
                3=> 'other_info',
                4=> 'user',
                5=> 'estatus',
                6=> 'created_at',
                7=> 'action',
            );

            $totalData = User::whereIn('role', ['3','4'])->WhereNotNull('first_name');
            if (isset($estatus)){
                $totalData = $totalData->where('estatus',$estatus);
            }
            
            $totalData = $totalData->count();

            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');

            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order = "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')))
            {
                $users = User::whereIn('role', ['3','4'])->WhereNotNull('first_name');
                if (isset($estatus)){
                    $users = $users->where('estatus',$estatus);
                }
               
                $users = $users->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $users =  User::whereIn('role', ['3','4'])->WhereNotNull('first_name');
                if (isset($estatus)){
                    $users = $users->where('estatus',$estatus);
                }
               
                $users = $users->where(function($query) use($search){
                    $query->where('first_name','LIKE',"%{$search}%")
                        ->orWhere('last_name', 'LIKE',"%{$search}%")
                        ->orWhere('email', 'LIKE',"%{$search}%")
                        ->orWhere('mobile_no', 'LIKE',"%{$search}%")
                        ->orWhere('created_at', 'LIKE',"%{$search}%");
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = count($users->toArray());
            }

            $data = array();
    
            if(!empty($users))
            {
                foreach ($users as $user)
                {
                    $page_id = ProjectPage::where('route_url','admin.end_users.list')->pluck('id')->first();

                    if( $user->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="EndUserstatuscheck_'. $user->id .'" onchange="changeEndUserStatus('. $user->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($user->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="EndUserstatuscheck_'. $user->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $user->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="EndUserstatuscheck_'. $user->id .'" onchange="changeEndUserStatus('. $user->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($user->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="EndUserstatuscheck_'. $user->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    if(isset($user->images) && $user->images!=null){
                        $images=explode(',',$user->images);
                        $profile_pic = url($images[0]);
                    }
                    else{
                        $profile_pic = url('images/default_avatar.jpg');
                    }

                    $contact_info = '';
                    if (isset($user->email)){
                        $contact_info .= '<span><i class="fa fa-envelope" aria-hidden="true"></i> ' .$user->email .'</span>';
                    }
                    if (isset($user->mobile_no)){
                        $contact_info .= '<span><i class="fa fa-phone" aria-hidden="true"></i> ' .$user->mobile_no .'</span>';
                    }

                    $other_info = '';
                    if ($user->gender == 1){
                        $other_info = '<span><i class="fa fa-male" aria-hidden="true"  style="font-size: 20px; margin-right: 5px"></i> Male</span>';
                    } else if($user->gender == 2){
                        $other_info = '<span><i class="fa fa-female" aria-hidden="true" style="font-size: 20px; margin-right: 5px"></i> Female</span>';
                    } else {
                        $other_info = '<span><i class="fa fa-male" aria-hidden="true" style="font-size: 20px; margin-right: 5px"></i> Other</span>';
                    }
                    if($user->age != NULL){
                        $other_info .= '<span><i class="fa fa-birthday-cake" aria-hidden="true"></i> '.$user->age.'</span>';
                    }
                    if($user->location != NULL){
                        $other_info .= '<span><i class="fa fa-map-marker" aria-hidden="true"></i> '.$user->location.'</span>';
                    }

                    $full_name = "";
                    if(isset($user->first_name)){
                        $full_name = $user->first_name;
                    }
                    if(isset($user->last_name) && !empty($user->last_name)){
                        $full_name .= ' '.$user->last_name;
                    }

                    $action='';
                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editEndUserBtn" class="btn btn-gray text-blue btn-sm" data-id="' .$user->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deleteEndUserBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeleteEndUserModal" onclick="" data-id="' .$user->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $role = '';
                    if ($user->role == 3){
                        $role = 'Real User';
                    }else{
                        $role = 'Fake User';
                    }

                    $nestedData['profile_pic'] = '<img src="'. $profile_pic .'" width="50px" height="50px" alt="Profile Pic"><span>'.$full_name.'</span>';
                    $nestedData['contact_info'] = $contact_info;
                    $nestedData['login_info'] = $other_info;
                    $nestedData['user'] = $role;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($user->created_at));
                    $nestedData['action'] = $action;
                    $data[] = $nestedData;
                }
            }

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data" => $data,
            );


            echo json_encode($json_data);
        }
    }

    public function addorupdateEnduser(Request $request){
       
        $messages = [
            'first_name.required' =>'Please provide a First Name',
            'last_name.required' =>'Please provide a Last Name',
            'mobile_no.required' =>'Please provide a Mobile No.',
            'age.required' =>'Please provide a Age.',
            'email.required' =>'Please provide a valid E-mail address.',
            'location.required' =>'Please provide a valid location.',
            'rate_per_minite.required' =>'Please provide a rate per minite.',
        ];

        if(isset($request->action) && $request->action=="update"){
            
        $validator = Validator::make($request->all(), [
         
            'first_name' => 'required',
            'last_name' => 'required',
            'age' => 'required',
            'rate_per_minite' => 'required',
            'location' => 'required',
            'email' => ['required', 'string', 'email', 'max:191',Rule::unique('users')->where(function ($query) use ($request) {
                return $query->whereIn('role', ['3','4'])->where('id','!=',$request->user_id)->where('estatus','!=',3);
            })],
            'mobile_no' => ['required', 'numeric', 'digits:10',Rule::unique('users')->where(function ($query) use ($request) {
                return $query->whereIn('role', ['3','4'])->where('id','!=',$request->user_id)->where('estatus','!=',3);
            })],
        ], $messages);
       }else{

        $validator = Validator::make($request->all(), [
         
            'first_name' => 'required',
            'last_name' => 'required',
            'age' => 'required',
            'rate_per_minite' => 'required',
            'location' => 'required',
            'email' => ['required', 'string', 'email', 'max:191',Rule::unique('users')->where(function ($query) use ($request) {
                return $query->whereIn('role', ['3','4'])->where('estatus','!=',3);
            })],
            'mobile_no' => ['required', 'numeric', 'digits:10',Rule::unique('users')->where(function ($query) use ($request) {
                return $query->whereIn('role', ['3','4'])->where('estatus','!=',3);
            })],
        ], $messages);

       }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        if(isset($request->action) && $request->action=="update"){
            $action = "update";
            $user = User::find($request->user_id);

            if(!$user){
                return response()->json(['status' => '400']);
            }

            
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->mobile_no = $request->mobile_no;
            $user->gender = $request->gender;
            $user->age = $request->age;
            $user->bio = $request->bio;
            $user->location = $request->location;
            $user->rate_per_minite = isset($request->rate_per_minite)?$request->rate_per_minite:"";
            $user->images = isset($request->userImg)?$request->userImg:$user->images;
            $user->video = isset($request->userVideo)?$request->userVideo:$user->video;
            $user->shot_video = isset($request->userShotVideo)?$request->userShotVideo:$user->shot_video;
            

        }else{
            $action = "add";
            $user = new User();
            
            $user->first_name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->mobile_no = $request->mobile_no;
            $user->gender = $request->gender;
            $user->age = $request->age;
            $user->location = $request->location;
            $user->rate_per_minite = isset($request->rate_per_minite)?$request->rate_per_minite:"";
            $user->images = isset($request->userImg)?$request->userImg:"";
            $user->video = isset($request->userVideo)?$request->userVideo:"";
            $user->shot_video = isset($request->userShotVideo)?$request->userShotVideo:"";
            $user->bio = $request->bio;
            $user->role = 4;
            $user->email = $request->email;
            $user->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
           
        }
        $user->save();

        if($user){

            $oldlanguageids = UserLanguage::where('user_id',$user->id)->get()->pluck('language_id')->toArray();

            if(isset($request->language_id) && count($request->language_id) > 0){
                foreach($request->language_id as $language){
                    if(!in_array($language,$oldlanguageids)){
                        $UserLanguage = new UserLanguage();
                        $UserLanguage->user_id = $user->id;
                        $UserLanguage->language_id = $language;
                        $UserLanguage->save();
                    }
                }
            }else{
              $languages = Language::get()->pluck('id')->toArray();
              foreach($languages as $language){
                if(!in_array($language,$oldlanguageids)){
                    $UserLanguage = new UserLanguage();
                    $UserLanguage->user_id = $user->id;
                    $UserLanguage->language_id = $language;
                    $UserLanguage->save();
                }
              }
            }
           
            foreach($oldlanguageids as $oldlanguageid){
                if(!in_array($oldlanguageid,$request->language_id)){
                    $UserLanguagedelete =UserLanguage::where('language_id',$oldlanguageid)->where('user_id',$user->id)->first();
                    $UserLanguagedelete->delete();
                }
            }
        }
       
        return response()->json(['status' => '200', 'action' => $action]);
    }

  
    public function edit($id){
        $action = "edit";
        $user = User::find($id);
        $languages = Language::where('estatus',1)->get();
        return view('admin.end_users.list',compact('action','user','languages'))->with('page',$this->page);
    }

    public function deleteEnduser($id){
        $user = User::find($id);
        if ($user){
            $user->estatus = 3;
            $user->save();

            $user->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function changeEnduserstatus($id){
        $user = User::find($id);
        if ($user->estatus==1){
            $user->estatus = 2;
            $user->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($user->estatus==2){
            $user->estatus = 1;
            $user->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function levelEnduser($userid){
        return view('admin.end_users.levellist',compact('userid'))->with('page',$this->page);
    }

    public function uploadfile(Request $request){
        if(isset($request->action) && $request->action == 'uploadUserIcon'){
            // if ($request->hasFile('images')) {
            //     $image = $request->file('images')[0];
            //     $image_name = 'userThumb_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            //     $destinationPath = public_path('images/userThumb');
            //     $image->move($destinationPath, $image_name);
            //     return response()->json(['data' => 'images/userThumb/'.$image_name]);
            // }

            if ($request->hasFile('images')) {
                $images = $request->file('images');

                foreach ($images as $key => $image) {
                    $image_name = 'userThumb_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                    $destinationPath = public_path('images/userThumb');
                    $image->move($destinationPath, $image_name);  
                    return response()->json(['data' => 'images/userThumb/'.$image_name]);
                }

            }
        }
    }

    public function uploadvideofile(Request $request){
        if(isset($request->action) && $request->action == 'uploadUserIcon'){
            if ($request->hasFile('video')) {
                $image = $request->file('video')[0];
                $image_name = 'userVideo_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images/userVideo');
                $image->move($destinationPath, $image_name);
                return response()->json(['data' => 'images/userVideo/'.$image_name]);
            }
        }
    }

    public function uploadshotvideofile(Request $request){
        if(isset($request->action) && $request->action == 'uploadUserIcon'){
            if ($request->hasFile('shot_video')) {
                $image = $request->file('shot_video')[0];
                $image_name = 'userShotVideo_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
                $destinationPath = public_path('images/userShotVideo');
                $image->move($destinationPath, $image_name);
                return response()->json(['data' => 'images/userShotVideo/'.$image_name]);
            }
        }
    }

    public function removefile(Request $request){
        if(isset($request->action) && $request->action == 'removeCatIcon'){
            $image = $request->file;
            if(isset($image)) {
                $image = public_path($request->file);
                if (file_exists($image)) {
                    unlink($image);
                    return response()->json(['status' => '200']);
                }
            }
        }
    }


}
