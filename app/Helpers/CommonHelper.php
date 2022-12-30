<?php


use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\ProjectPage;
use App\Models\UserPermission;


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





