<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\ProjectPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    private $page = "Languages";

    public function index(){
        return view('admin.languages.list')->with('page',$this->page);
    }

    public function addorupdatelanguage(Request $request){

        $messages = [
            'title.required' =>'Please provide a Title',
        ];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

    
        if(!isset($request->language_id)){
            $language = new Language();
            $language->title = $request->title;
            $language->save();
            return response()->json(['status' => '200', 'action' => 'add']);
        }
        else{
            $language = Language::find($request->language_id);
            if ($language) {
                $language->title = $request->title;
                $language->save();
                return response()->json(['status' => '200', 'action' => 'update']);
            }
            return response()->json(['status' => '400']);
        }
    }

    public function alllanguagelist(Request $request){
        if ($request->ajax()) {
            
            $columns = array(
                0 =>'id',
                1 =>'title',
                2=> 'estatus',
                3=> 'created_at',
                4=> 'action',
            );

            $totalData = Language::count();
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
                $languages = Language::offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();
            }
            else {
                $search = $request->input('search.value');
                $languages =  Language::where(function($query) use($search){
                        $query->where('id','LIKE',"%{$search}%")
                                ->orWhere('title', 'LIKE',"%{$search}%");
                        })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

                $totalFiltered = Language::where(function($query) use($search){
                        $query->where('id','LIKE',"%{$search}%")
                        ->orWhere('title', 'LIKE',"%{$search}%");
                    })
                    ->count();
            }

            $data = array();

            if(!empty($languages))
            {
                foreach ($languages as $language)
                {
                    $page_id = ProjectPage::where('route_url','admin.languages.list')->pluck('id')->first();

                    if($language->estatus==1 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="languagestatuscheck_'. $language->id .'" onchange="chagelanguagestatus('. $language->id .')" value="1" checked="checked"><span class="slider round"></span></label>';
                    }
                    else if ($language->estatus==1){
                        $estatus = '<label class="switch"><input type="checkbox" id="languagestatuscheck_'. $language->id .'" value="1" checked="checked"><span class="slider round"></span></label>';
                    }

                    if($language->estatus==2 && (getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id))) ){
                        $estatus = '<label class="switch"><input type="checkbox" id="languagestatuscheck_'. $language->id .'" onchange="chagelanguagestatus('. $language->id .')" value="2"><span class="slider round"></span></label>';
                    }
                    else if ($language->estatus==2){
                        $estatus = '<label class="switch"><input type="checkbox" id="languagestatuscheck_'. $language->id .'" value="2"><span class="slider round"></span></label>';
                    }

                    $action='';
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
                        $action .= '<button id="editlanguageBtn" class="btn btn-gray text-blue btn-sm" data-toggle="modal" data-target="#languageModal" onclick="" data-id="' .$language->id. '"><i class="fa fa-pencil" aria-hidden="true"></i></button>';
                    }
                    if ( getUSerRole()==1 || (getUSerRole()!=1 && is_delete($page_id)) ){
                        $action .= '<button id="deletelanguageBtn" class="btn btn-gray text-danger btn-sm" data-toggle="modal" data-target="#DeletelanguageModal" onclick="" data-id="' .$language->id. '"><i class="fa fa-trash-o" aria-hidden="true"></i></button>';
                    }

                    $nestedData['title'] = $language->title;
                    $nestedData['estatus'] = $estatus;
                    $nestedData['created_at'] = date('d-m-Y h:i A', strtotime($language->created_at));
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

    public function editlanguage($id){
        $language = Language::find($id);
        return response()->json($language);
    }

    public function deletelanguage($id){
        $language = Language::find($id);
        if ($language){
            $language->estatus = 3;
            $language->save();

            $language->delete();
            return response()->json(['status' => '200']);
        }
        return response()->json(['status' => '400']);
    }

    public function chagelanguagestatus($id){
        $language = Language::find($id);
        if ($language->estatus==1){
            $language->estatus = 2;
            $language->save();
            return response()->json(['status' => '200','action' =>'deactive']);
        }
        if ($language->estatus==2){
            $language->estatus = 1;
            $language->save();
            return response()->json(['status' => '200','action' =>'active']);
        }
    } 
}
