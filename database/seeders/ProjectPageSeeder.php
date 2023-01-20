<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ProjectPage;
use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('project_pages')->truncate();

        ProjectPage::create([ 
            'id' => 1, 
            'parent_menu' => 0, 
            'label' => 'Dashboard', 
            'route_url' => 'admin.dashboard', 
            'is_display_in_menu' => 0, 
            'inner_routes' => 'admin.dashboard',
            'icon_class' => 'fa fa-dashboard', 
            'sr_no' => 1
        ]);

        ProjectPage::create([
            'id' => 2,
            'parent_menu' => 0,
            'label' => 'Users',
            'route_url' => null,
            'icon_class' => 'fa fa-users',
            'is_display_in_menu' => 1,
            'sr_no' => 2
        ]);

        ProjectPage::create([
            'id' => 3,
            'parent_menu' => 2,
            'label' => 'User List',
            'route_url' => 'admin.users.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.users.list,admin.users.addorupdate,admin.alluserslist,admin.users.changeuserstatus,admin.users.edit,admin.users.delete,admin.users.permission,admin.users.savepermission'
        ]);

        ProjectPage::create([
            'id' => 4,
            'parent_menu' => 2,
            'label' => 'Customer List',
            'route_url' => 'admin.end_users.list',
            'is_display_in_menu' => 1,
            'inner_routes' => 'admin.end_users.list,admin.end_users.addorupdate,admin.allEnduserlist,admin.end_users.changeEnduserstatus,admin.end_users.edit,admin.end_users.delete'
        ]);

        ProjectPage::create([
            'id' => 5,
            'parent_menu' => 0,
            'label' => 'Language',
            'icon_class' => 'fa fa-language',
            'route_url' => 'admin.languages.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.languages.list,admin.languages.add,admin.languages.save,admin.alllanguagelist,admin.languages.changelanguagestatus,admin.languages.delete,admin.languages.edit',
            'sr_no' => 3
        ]);

        ProjectPage::create([
            'id' => 6,
            'parent_menu' => 0,
            'label' => 'Coin Price',
            'route_url' => 'admin.pricerange.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.pricerange.list,admin.pricerange.addorupdate,admin.allpricerangeslist,admin.pricerange.changepricerangestatus,admin.pricerange.edit,admin.pricerange.delete',
            'icon_class' => 'fa fa-money',
            'sr_no' => 4
        ]);

        ProjectPage::create([
            'id' => 7,
            'parent_menu' => 0,
            'label' => 'Subscription Price',
            'route_url' => 'admin.subscription.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.subscription.list,admin.subscription.addorupdate,admin.allsubscriptionslist,admin.subscription.changesubscriptionstatus,admin.subscription.edit,admin.subscription.delete',
            'icon_class' => 'fa fa-money',
            'sr_no' => 4
        ]);

        ProjectPage::create([
            'id' => 8,
            'parent_menu' => 0,
            'label' => 'Message',
            'icon_class' => 'fa fa-file-text',
            'route_url' => 'admin.messages.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.messages.list,admin.messages.add,admin.messages.save,admin.allmessagelist,admin.messages.changemessagestatus,admin.messages.delete,admin.messages.edit',
            'sr_no' => 5
        ]);

        ProjectPage::create([
            'id' => 9,
            'parent_menu' => 0,
            'label' => 'Purchase Coins',
            'icon_class' => 'fa fa-file-text',
            'route_url' => 'admin.purchasecoin.list',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.purchasecoin.list,admin.allpurchasecoinslist',
            'sr_no' => 6
        ]);

        ProjectPage::create([
            'id' => 10,
            'parent_menu' => 0,
            'label' => 'Settings',
            'route_url' => 'admin.settings.list',
            'icon_class' => 'fa fa-cog',
            'is_display_in_menu' => 0,
            'inner_routes' => 'admin.settings.list,admin.settings.edit',
            'sr_no' => 7
        ]);

        
        $users = User::where('role',"!=",1)->get();
        $project_page_ids1 = ProjectPage::where('parent_menu',0)->where('is_display_in_menu',0)->pluck('id')->toArray();
        $project_page_ids2 = ProjectPage::where('parent_menu',"!=",0)->where('is_display_in_menu',1)->pluck('id')->toArray();
        $project_page_ids = array_merge($project_page_ids1,$project_page_ids2);
        foreach ($users as $user){
            foreach ($project_page_ids as $pid){
                $user_permission = UserPermission::where('user_id',$user->id)->where('project_page_id',$pid)->first();
                if (!$user_permission){
                    $userpermission = new UserPermission();
                    $userpermission->user_id = $user->id;
                    $userpermission->project_page_id = $pid;
                    $userpermission->save();
                }
            }
        }

    }
}
