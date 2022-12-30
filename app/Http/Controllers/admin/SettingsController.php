<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProjectPage;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SettingsController extends Controller
{
    private $page = "Settings";

    public function index(){
        $Settings = Setting::first();
        $canWrite = false;
        $page_id = ProjectPage::where('route_url','admin.settings.list')->pluck('id')->first();
        if( getUSerRole()==1 || (getUSerRole()!=1 && is_write($page_id)) ){
            $canWrite = true;
        }
        return view('admin.settings.list',compact('Settings','canWrite'))->with('page',$this->page);
    }

    public function editSettings(){
        $Settings = Setting::find(1);
        return response()->json($Settings);
    }

    public function updateInvoiceSetting(Request $request){
        $messages = [
            'company_name.required' =>'Please provide a Company Name',
            'company_logo.image' =>'Please provide a Valid Extension Logo(e.g: .jpg .png)',
            'company_logo.mimes' =>'Please provide a Valid Extension Logo(e.g: .jpg .png)',
            'company_favicon.image' =>'Please provide a Valid Extension favicon(e.g: .jpg .png)',
            'company_favicon.mimes' =>'Please provide a Valid Extension favicon(e.g: .jpg .png)',
        ];

        $validator = Validator::make($request->all(), [
            'company_name' => 'required',
            'company_logo' => 'image|mimes:jpeg,png,jpg',
            'company_favicon' => 'image|mimes:jpeg,png,jpg',
            
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(),'status'=>'failed']);
        }

        $Settings = Setting::find(1);
        if(!$Settings){
            return response()->json(['status' => '400']);
        }
        $Settings->company_name = $request->company_name;
    
        $old_image = $Settings->company_logo;
        if ($request->hasFile('company_logo')) {
            $image = $request->file('company_logo');
            $image_name = 'company_logo_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/company');
            $image->move($destinationPath, $image_name);
            if(isset($old_image)) {
                $old_image = public_path('images/company/' . $old_image);
                if (file_exists($old_image)) {
                    unlink($old_image);
                }
            }
            $Settings->company_logo = $image_name;
        }

        $old_image_favicon = $Settings->company_favicon;
        if ($request->hasFile('company_favicon')) {
            $image = $request->file('company_favicon');
            $image_name = 'company_favicon_' . rand(111111, 999999) . time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('images/company');
            $image->move($destinationPath, $image_name);
            if(isset($old_image_favicon)) {
                $old_image_favicon = public_path('images/company/' . $old_image_favicon);
                if (file_exists($old_image_favicon)) {
                    unlink($old_image_favicon);
                }
            }
            $Settings->company_favicon = $image_name;
        }

        $Settings->save();
        return response()->json(['status' => '200','Settings' => $Settings]);
    }

}
