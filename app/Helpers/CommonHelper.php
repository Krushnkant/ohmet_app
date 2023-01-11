<?php


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ProjectPage;
use App\Models\UserPermission;
use App\Models\User;


function getLeftMenuPages(){
    $pages = ProjectPage::where('parent_menu',0)->orderBy('sr_no','ASC')->get()->toArray();
    return $pages;
}

function getUSerRole(){
    return  \Illuminate\Support\Facades\Auth::user()->role;
}

function is_write($page_id){
    $is_write = UserPermission::where('user_id',\Illuminate\Support\Facades\Auth::user()->id)->where('project_page_id',$page_id)->where('can_write',1)->first();
    if ($is_write){
        return true;
    }
    return false;
}

function is_delete($page_id){
    $is_delete = UserPermission::where('user_id',\Illuminate\Support\Facades\Auth::user()->id)->where('project_page_id',$page_id)->where('can_delete',1)->first();
    if ($is_delete){
        return true;
    }
    return false;
}

function is_read($page_id){
    $is_read = UserPermission::where('user_id',\Illuminate\Support\Facades\Auth::user()->id)->where('project_page_id',$page_id)->where('can_read',1)->first();
    if ($is_read){
        return true;
    }
    return false;
}

function UploadImage($image, $path){
    $imageName = Str::random().'.'.$image->getClientOriginalExtension();
    $path = $image->move(public_path($path), $imageName);
    if($path == true){
        return $imageName;
    }else{
        return null;
    }
}

function send_sms($mobile_no, $otp){
    $url = 'https://www.smsgatewayhub.com/api/mt/SendSMS?APIKey=H26o0GZiiEaUyyy0kvOV5g&senderid=MADMRT&channel=2&DCS=0&flashsms=0&number=91'.$mobile_no.'&text=Welcome%20to%20Madness%20Mart,%20Your%20One%20time%20verification%20code%20is%20'.$otp.'.%20Regards%20-%20MADNESS%20MART&route=31&EntityId=1301164983812180724&dlttemplateid=1307165088121527950';
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
    ));
    $response = curl_exec($curl);
    curl_close($curl);
//    echo $response;
}

function sendPushNotificationcustomers($id,$data)
{
        
        $user = User::where('id',$id)->first();
        $tokens_android = \App\Models\CustomerDeviceToken::where('device_id',$user->device_id)->where('device_type','android')->pluck('token')->all();
        $tokens_ios = \App\Models\CustomerDeviceToken::where('device_id',$user->device_id)->where('device_type','ios')->pluck('token')->all();
        if (count($tokens_android) == 0 && count($tokens_ios) == 0) {
            return false;
        }
     
        if (isset($tokens_ios) && !empty($tokens_ios)){
            $ios_fields = array(
                'registration_ids' => $tokens_ios,
                'data' => $data,
                'notification' => array(
                    "title" => $data['title'],
                    "body" => $data['message'],
                    "image" => url($data['image']),
                    "priority" => "high",
                    "sound" => "default",
                )
            );
            sendNotification($ios_fields,"ios");
        }

        if (isset($tokens_android) && !empty($tokens_android)){
            $android_fields = array(
                'registration_ids' => $tokens_android,
                'data' => $data,
                'notification' => array(
                    "title" => $data['title'],
                    "body" => $data['message'],
                    "image" => url($data['image']),
                    "priority" => "high",
                    "sound" => "default",
                )
            );
            sendNotification($android_fields,"android");
        }

        return true;
    
}

function sendNotification($data,$type){
    $api_key = env('ANDROID_NOTIFICATION_KEY');
    if($type == "ios"){
        $api_key = env('IOS_NOTIFICATION_KEY');
    }
    $headers = array('Authorization: key=' . $api_key, 'Content-Type: application/json');
    $url = 'https://fcm.googleapis.com/fcm/send';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $result = curl_exec($ch);
    curl_close($ch);

    //$data = explode(':', $result);
    //$sucess = explode(",", $data[2]);

    return true;
}





