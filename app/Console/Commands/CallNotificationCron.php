<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;


class CallNotificationCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'callnotification:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
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

        \Log::info("Cron is working fine!");
        return Command::SUCCESS;
    }
}

