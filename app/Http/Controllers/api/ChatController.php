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

            return $this->sendResponseWithData($get_all_chat, "get all chat successfully");
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
           // $notification_array['type'] = "remainder";
           // $notification_array['value_id'] = $event->id;
           // $notification_array['notificationdata'] = $notification_arr;
           $notification_array['image'] = $user->profile_pic;

           sendPushNotificationcustomers($request->user_id,$notification_array);
           sleep($setting->message_duration_time);
        }
        return $this->sendResponseWithData($chat, "Add chat successfully");
       
    }
   
}
