<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\ProjectPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    private $page = "Message";

    public function index(){
        return view('admin.messages.list')->with('page',$this->page);
    }

    public function addorupdatemessage(Request $request){

        $messages = [
            'title.required' =>'Please provide a Title',
        ];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

    
        if(!isset($request->message_id)){
            $message = new Message();
            $message->message_text = $request->title;
            $message->save();
            return response()->json(['status' => '200', 'action' => 'add']);
        }
        else{
            $message = Message::find($request->message_id);
            if ($message) {
                $message->message_text = $request->title;
                $message->save();
                return response()->json(['status' => '200', 'action' => 'update']);
            }
            return response()->json(['status' => '400']);
        }
    }

    public function allmessagelist(Request $request){
        if ($request->ajax()) {
            
            $columns = array(
                0 =>'id',
                1 =>'title',
                2=> 'estatus',
                3=> 'created_at',
                4=> 'action',
            );

            $totalData = Message::count();
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
                $messages = Message::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $messages =  Message::where(function($query) use($search){
                        $query->where('id','LIKE',"%{$search}%")
                                ->orWhere('title', 'LIKE',"%{$search}%");
                        })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = Message::where(function($query) use($search){
                        $query->where('id','LIKE',"%{$search}%")
                        ->orWhere('title', 'LIKE',"%{$search}%");
                    })
                    ->count();
            }

            $data = array();

            if(!empty($messages))
            {
                foreach ($messages as $message)
                {
                    $page_id = ProjectPage::where('route_url','admin.messages.list')->pluck('id')->first();

                    if($message->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="messagestatuscheck_'. $message->id .'" onchange="chagemessagestatus('. $message->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    else if ($message->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="messagestatuscheck_'. $message->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if($message->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="messagestatuscheck_'. $message->id .'" onchange="chagemessagestatus('. $message->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    else if ($message->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="messagestatuscheck_'. $message->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editmessageBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#messageModal" onclick="" data-id="' .$message->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deletemessageBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeletemessageModal" onclick="" data-id="' .$message->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['title'] = $message->message_text;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($message->created_at));
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

    public function editmessage($id){
        $message = Message::find($id);
        return response()->json($message);
    }

    public function deletemessage($id){
        $message = Message::find($id);
        if ($message){
            $message->estatus = 3;
            $message->save();

            $message->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function chagemessagestatus($id){
        $message = Message::find($id);
        if ($message->estatus==1){
            $message->estatus = 2;
            $message->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($message->estatus==2){
            $message->estatus = 1;
            $message->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    } 
}
