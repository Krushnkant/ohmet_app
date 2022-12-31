<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\PriceRange;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PriceRangeController extends Controller
{
    public function index(){
        return view('admin.pricerange.list');
    }

    public function addorupdatepricerange(Request $request){
        $messages = [
            'price.required' =>'Please provide a Price',
            'coin.required' =>'Please provide a Coin.', 
        ];
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric',
            'coin' => 'required|numeric',
        ], $messages);
        
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }
        
        if(isset($request->action) && $request->action=="update"){
            $action = "update";
            $pricerange = PriceRange::find($request->pricerange_id);
            if(!$pricerange){
                return response()->json(['status' => '400']);
            }
            $pricerange->price = $request->price;
            $pricerange->coin = $request->coin;
        }else{
            $action = "add";
            $pricerange = new PriceRange();
            $pricerange->price = $request->price;
            $pricerange->coin = $request->coin;
            $pricerange->created_at = new \DateTime(null, new \DateTimeZone('Asia/Kolkata'));
        }
    
        $pricerange->save();
        return response()->json(['status' => '200', 'action' => $action]);
    }

    public function allpricerangeslist(Request $request){
        if ($request->ajax()) {
        
            $columns = array(
                0 =>'id',
                1 =>'price',
                2=> 'coin',
                3=> 'estatus',
                4=> 'created_at',
                5=> 'action',
            );

            $totalData = PriceRange::count();

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
                $priceranges = PriceRange::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $priceranges =  PriceRange::where(function($query) use($search){
                      $query->where('id','LIKE',"%{$search}%")
                            ->orWhere('price', 'LIKE',"%{$search}%")
                            ->orWhere('coin', 'LIKE',"%{$search}%")
                            ->orWhere('created_at', 'LIKE',"%{$search}%");
                      })
                      ->offset($start)
                      ->limit($limit)
                      ->orderBy($order,$dir)
                      ->get();

                $totalFiltered = PriceRange::where(function($query) use($search){
                    $query->where('id','LIKE',"%{$search}%")
                          ->orWhere('price', 'LIKE',"%{$search}%")
                          ->orWhere('coin', 'LIKE',"%{$search}%")
                          ->orWhere('created_at', 'LIKE',"%{$search}%");
                    })
                    ->count();
            }

            $data = array();

            if(!empty($priceranges))
            {
                foreach ($priceranges as $pricerange)
                {
                    $page_id = ProjectPage::where('route_url','admin.pricerange.list')->pluck('id')->first();

                    if( $pricerange->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="Pricerangestatuscheck_'. $pricerange->id .'" onchange="changePricerangeStatus('. $pricerange->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    elseif ($pricerange->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="Pricerangestatuscheck_'. $pricerange->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if( $pricerange->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="Pricerangestatuscheck_'. $pricerange->id .'" onchange="changePricerangeStatus('. $pricerange->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    elseif ($pricerange->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="Pricerangestatuscheck_'. $pricerange->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    $price = '<span><i class="fa fa-inr" aria-hidden="true"></i> ' .$pricerange->price .'</span>';
                    $coin = '<span><i class="fa fa-coins" aria-hidden="true"></i> ' .$pricerange->coin .'</span>';
                    
                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editPriceRangeBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#PriceRangeModel" onclick="" data-id="' .$pricerange->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deletePriceRangeBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeletePriceRangeModel" onclick="" data-id="' .$pricerange->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['price'] = $price;
                    $nestedData['coin'] = $coin;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('Y-m-d H:i:s', strtotime($pricerange->created_at));
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

    public function changePricerangeStatus($id){
        $pricerange = PriceRange::find($id);
        if ($pricerange->estatus==1){
            $pricerange->estatus = 2;
            $pricerange->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($pricerange->estatus==2){
            $pricerange->estatus = 1;
            $pricerange->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    }

    public function editpricerange($id){
        $pricerange = PriceRange::find($id);
        return response()->json($pricerange);
    }

    public function deletepricerange($id){
        $pricerange = PriceRange::find($id);
        if ($pricerange){
            $pricerange->estatus = 3;
            $pricerange->save();
            $pricerange->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }
}
