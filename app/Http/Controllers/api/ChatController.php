<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\{Chat,User,Message,Setting};
use Illuminate\Support\Facades\DB;

class ChatController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function CreateChat(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'send_id' => 'required',
            'receiver_id' => 'required',
            'message_text' => 'required',
            'type' => 'required | in:text,image,video,content,audio,document,contact,location,lbl',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors(), "Validation Errors", []);
        } else {
            $data = $request->all();
            //$auth = Auth::guard()->user();
            $auth_id = (int)$data['send_id'];

            if ($auth_id != 0) {
                $data['user_id'] = $auth_id;
                if ((int)$data['receiver_id'] != $auth_id) {
                    $chat = Chat::create($data);
                    if ($chat != null) {
                        return $this->sendResponseWithData($chat, "Add chat successfully");
                    } else {
                        return $this->sendError("Something want wrong", "Something want wrong", []);
                    }
                } else {
                    return $this->sendError("User Not Found", "User Not Found", []);
                }
            } else {
                return $this->sendError("User Not Found", "User Not Found", []);
            }
        }
    }

    public function GetAllChat($id)
    {
        //$auth = Auth::guard()->user();
        $auth_id = $id;

        // if ($id == $auth_id) {
            $get_all_chat = Chat::with(['receiver' => function ($query) use ($auth_id) {
                $query->where('id', '!=', $auth_id);
            }, 'user' => function ($query1) use ($auth_id) {
                $query1->where('id', '!=', $auth_id);
            }])->where(function ($query) use ($auth_id) {
                $query->where('user_id', $auth_id)
                    ->orWhere('receiver_id', $auth_id);
            })
                ->orWhere(function ($query) use ($auth_id) {
                    $query->where('receiver_id', $auth_id)
                        ->orWhere('user_id', $auth_id);
                })
                ->where('is_deleted', 0)
                ->where('tick', '0')
                ->groupBy(
                    DB::raw(
                        'if (receiver_id = ' . $auth_id . ', user_id, receiver_id)'
                    )
                )
                ->orderBy('id', 'desc')
                ->get();
            $chat_arr = array();  
             foreach($get_all_chat as $all_chat){
            
                //foreach($all_chat->user as $user){
                    $image = "";
                    if($all_chat->user->images != ""){
                       $image_array = explode(",",$all_chat->user->images);
                       $image = $image_array[0]; 
                    }
                    $temp_user = array();
                    $temp_user['id'] = $all_chat->user->id;
                    $temp_user['device_id'] = $all_chat->user->device_id;
                    $temp_user['first_name'] = $all_chat->user->first_name;
                    $temp_user['last_name'] = $all_chat->user->last_name;
                    $temp_user['email'] = $all_chat->user->email;
                    $temp_user['mobile_no'] = $all_chat->user->mobile_no;
                    $temp_user['age'] = $all_chat->user->age;
                    $temp_user['gender'] = $all_chat->user->gender;
                    $temp_user['bio'] = $all_chat->user->bio;
                    $temp_user['location'] = $all_chat->user->location;
                    $temp_user['images'] = url($image);
                    $temp_user['video'] = url($all_chat->user->video);
                    $temp_user['shot_video'] = url($all_chat->user->shot_video);
                    $temp_user['rate_per_minite'] = $all_chat->user->rate_per_minite;
                    $temp_user['created_at'] = $all_chat->user->created_at;
                    $temp_user['updated_at'] = $all_chat->user->updated_at;
                //}

                $unreadcount = Chat::where('user_id', $all_chat->user_id)
                    ->where('receiver_id', $all_chat->receiver_id)
                    ->where('deleted_by', null)
                    ->where('is_deleted', '0')
                    ->where('deleted_by', null)
                    ->whereIn('tick', ['0', '1'])
                    ->count();

                $user_id    = $all_chat->user_id;

                $lastmessage = Chat::Where(function ($query) use ($auth_id) {
                    $query->where('receiver_id', $auth_id)
                        ->orWhere('user_id', $auth_id);
                })->Where(function ($query) use ($user_id) {
                    $query->where('receiver_id', $user_id)
                        ->orWhere('user_id', $user_id);
                })->orderBy('id', 'desc')->first();    
                
    
                $temp = array();
                $temp['id'] = $all_chat->id;
                $temp['user_id'] = $lastmessage->user_id;
                $temp['receiver_id'] = $lastmessage->receiver_id;
                $temp['type'] = $lastmessage->type;
                $temp['message_text'] = $lastmessage->message_text;
                $temp['is_deleted'] = $lastmessage->is_deleted;
                $temp['tick'] = $lastmessage->tick;
                $temp['unreadcount'] = $unreadcount;
                $temp['created_at'] = $lastmessage->created_at;
                $temp['updated_at'] = $lastmessage->updated_at;
                $temp['receiver'] = $all_chat->receiver;
                $temp['user'] = $temp_user;
            
                array_push($chat_arr,$temp);
             }    

            return $this->sendResponseWithData($chat_arr, "get all chat successfully");
        // } else {
        //     return $this->sendError("User Not Found", "User Not Found", []);
        // }
    }

    public function PersonalChat($user_id, $receiver_id)
    {
        $auth = Auth::guard()->user();
        $auth_id = (int)$user_id;

        //if($auth_id == (int)$user_id) {
        $get_all_chat = Chat::whereIn('user_id', [$user_id, $receiver_id])
            ->whereIn('receiver_id', [$user_id, $receiver_id])
            ->where('deleted_by', null)
            ->where('is_deleted', '0')
            ->orderBy('id', 'ASC')
            ->get();
        foreach ($get_all_chat as $key => $chat) {
            $chat->tick = '2';
            $chat->save();
        }
        return $this->sendResponseWithData($get_all_chat, "get all personal chat successfully");
        // } else {
        //     return $this->sendError("User Not Found", "User Not Found", []);
        // }
    }

    public function UnreadMessageCount()
    {
        $auth = Auth::guard()->user();
        $auth_id = $auth->id;

        if ($auth_id != null) {
            $get_all_chat = Chat::where('receiver_id', $auth_id)
                ->where('deleted_by', null)
                ->where('is_deleted', 0)
                ->where('deleted_by', null)
                ->whereIn('tick', ['0', '1'])
                ->count();
            return $this->sendResponseWithData($get_all_chat, "get whole chat count successfully");
        } else {
            return $this->sendError("User Not Found", "User Not Found", []);
        }
    }

    public function GetAllUnreadMessageCount()
    {
        $auth = Auth::guard()->user();
        $auth_id = $auth->id;
        $data = [];
        if ($auth_id != null) {
            $get_all_chat_msg = Chat::where('receiver_id', $auth_id)
                ->where('deleted_by', null)
                ->where('is_deleted', 0)
                ->where('deleted_by', null)
                ->whereIn('tick', ['0', '1'])
                ->get()->pluck('user_id')->toArray();
            $array = array_unique($get_all_chat_msg);

            foreach ($array as $key => $value) {
                $get_all_chat = Chat::where('user_id', $value)
                    ->where('receiver_id', $auth_id)
                    ->where('deleted_by', null)
                    ->where('is_deleted', 0)
                    ->where('deleted_by', null)
                    ->whereIn('tick', ['0', '1'])
                    ->count();

                $bike1 = [];
                $bike1['user_id'] = $value;
                $bike1['count'] = $get_all_chat;
                array_push($data, $bike1);
            }
            return $this->sendResponseWithData($data, "get all personal chat count successfully");
        } else {
            return $this->sendError("User Not Found", "User Not Found", []);
        }
    }

    public function AutoSendMessage(Request $request)
    {
        set_time_limit(0);
        $setting = Setting::first();
        $users = User::where('estatus',1)->where('role',4)->inRandomOrder()->limit($setting->number_of_users)->get();
        foreach($users as $user){
            $messages = Message::where('estatus',1)->inRandomOrder()->limit($setting->number_of_messages)->get(); 
            foreach($messages as $message){
                $auth_id = $user->id;
                $data['receiver_id'] = $request->user_id;
                $data['message_text'] = $message->message_text;
                $data['type'] = "text";
                if ($auth_id != 0) {
                    $data['user_id'] = $auth_id;
                    if ((int)$data['receiver_id'] != $auth_id) {
                        $chat = Chat::create($data);
                    } else {
                        return $this->sendError("User Not Found", "User Not Found", []);
                    }
                } else {
                    return $this->sendError("User Not Found", "User Not Found", []);
                }
           }

           $notification_array['title'] = $user->first_name .' '.$user->last_name;
           $notification_array['message'] = $chat->message_text;
           $notification_array['rate_per_minite'] = $user->rate_per_minite;
           $notification_array['video'] = isset($user->video)?url($user->video):"";
           // $notification_array['type'] = "remainder";
           // $notification_array['value_id'] = $event->id;
           // $notification_array['notificationdata'] = $notification_arr;
           $notification_array['image'] = $user->profile_pic;

           sendPushNotificationcustomers($request->user_id,$notification_array);
           sleep($setting->message_duration_time);
        }
        return $this->sendResponseWithData($chat, "Add chat successfully");
       
    }

    public function SendCallNotification(Request $request)
    {
        set_time_limit(0);
        $realusers = User::where('estatus',1)->where('role',3)->where('subscription_id',0)->get();
        foreach($realusers as $realuser){
            $user = User::where('estatus',1)->where('role',4)->inRandomOrder()->first();
            $images = explode(',',$user->images);
            $images_arr = array();
            foreach($images as $image){
                $images_arr[] = url($image);
            } 

            $notification_array['title'] = "Incoming call from ".$user->first_name .' '.$user->last_name;
            $notification_array['message'] = "Incoming call";
            $notification_array['type'] = "call";
            // $notification_array['value_id'] = $event->id;
            // $notification_array['notificationdata'] = $notification_arr;
            $notification_array['image'] = isset($images_arr[0])?$images_arr[0]:"";
            $notification_array['id'] = $user->id;
            $notification_array['first_name'] = $user->first_name;
            $notification_array['last_name'] = $user->last_name;
            $notification_array['email'] = $user->email;
            $notification_array['mobile_no'] = $user->mobile_no;
            $notification_array['age'] = $user->age;
            $notification_array['gender'] = $user->gender;
            $notification_array['bio'] = $user->bio;
            $notification_array['location'] = $user->location;
            $notification_array['rate_per_minite'] = $user->rate_per_minite;
            $notification_array['images'] = $images_arr;
            $notification_array['video'] = isset($user->video)?url($user->video):"";
            $notification_array['shot_video'] = isset($user->shot_video)?url($user->shot_video):"";
         
           sendPushNotificationcustomers($realuser->id,$notification_array);
        }

        return $this->sendResponseSuccess("Send call notification successfully");
       
    }
   
}
