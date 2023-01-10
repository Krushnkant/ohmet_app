<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    public function index(){
        return view('admin.subscription.list');
    }

    public function addorupdatesubscription(Request $request){
        $messages = [
            'price.required' =>'Please provide a Price',
            'title.required' =>'Please provide a title.', 
            'key.required' =>'Please provide a Key.', 
            'days.required' =>'Please provide a Days.', 
        ];
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric',
            'title' => 'required',
            'key' => 'required',
            'days' => 'required|numeric',
        ], $messages);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        
        if(isset($request->action) && $request->action=="update"){
            $action = "update";
            $subscription = Subscription::find($request->subscription_id);
            if(!$subscription){
                return response()->json(['status' => '400']);
            }
            $subscription->price = $request->price;
            $subscription->title = $request->title;
            $subscription->key = $request->key;
            $subscription->days = $request->days;
        }else{
            $action = "add";
            $subscription = new Subscription();
            $subscription->price = $request->price;
            $subscription->title = $request->title;
            $subscription->key = $request->key;
            $subscription->days = $request->days;
            $subscription->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        }
    
        $subscription->save();
        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allsubscriptionslist(Request $request){
        if ($request->ajax()) {
        
            $columns = array(
                0 =>'id',
                1 =>'price',
                2=> 'title',
                3=> 'days',
                4=> 'estatus',
                5=> 'created_at',
                6=> 'action',
            );

            $totalData = Subscription::count();

            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');

            if($order == "id"){
                $order == "created_at";
                $dir = 'desc';
            }

            if(empty($request->input('search.value')))
            {
                $subscriptions = Subscription::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $subscriptions =  Subscription::where(function($query) use($search){
                      $query->where('id','LIKE',"%{$search}%")
                            ->orWhere('price', 'LIKE',"%{$search}%")
                            ->orWhere('title', 'LIKE',"%{$search}%")
                            ->orWhere('created_at', 'LIKE',"%{$search}%");
                      })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = Subscription::where(function($query) use($search){
                    $query->where('id','LIKE',"%{$search}%")
                          ->orWhere('price', 'LIKE',"%{$search}%")
                          ->orWhere('title', 'LIKE',"%{$search}%")
                          ->orWhere('created_at', 'LIKE',"%{$search}%");
                    })
                    ->count();
            }

            $data = array();

            if(!empty($subscriptions))
            {
                foreach ($subscriptions as $subscription)
                {
                    $page_id = ProjectPage::where('route_url','admin.subscription.list')->pluck('id')->first();

                    if( $subscription->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="subscriptionstatuscheck_'. $subscription->id .'" onchange="changesubscriptionStatus('. $subscription->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($subscription->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="subscriptionstatuscheck_'. $subscription->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $subscription->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="subscriptionstatuscheck_'. $subscription->id .'" onchange="changesubscriptionStatus('. $subscription->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($subscription->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="subscriptionstatuscheck_'. $subscription->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    $price = '<span><i class="fa fa-inr" aria-hidden="true"></i> ' .$subscription->price .'</span>';
                    $title = '<span> ' .$subscription->title .'</span>';
                    
                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editsubscriptionBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#subscriptionModel" onclick="" data-id="' .$subscription->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deletesubscriptionBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeletesubscriptionModel" onclick="" data-id="' .$subscription->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['price'] = $price;
                    $nestedData['title'] = $title;
                    $nestedData['days'] = $subscription->days;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('Y-m-d H:i:s', strtotime($subscription->created_at));
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

    public function changesubscriptionStatus($id){
        $subscription = Subscription::find($id);
        if ($subscription->estatus==1){
            $subscription->estatus = 2;
            $subscription->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($subscription->estatus==2){
            $subscription->estatus = 1;
            $subscription->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function editsubscription($id){
        $subscription = Subscription::find($id);
        return response()->json($subscription);
    }

    public function deletesubscription($id){
        $subscription = Subscription::find($id);
        if ($subscription){
            $subscription->estatus = 3;
            $subscription->save();
            $subscription->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }
}
