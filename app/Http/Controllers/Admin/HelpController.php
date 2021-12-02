<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Helper\Exceptions;
use App\Helper\UserRights;
use App\Model\AdminContact;
use Auth;
use Session;
use Input;

class HelpController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try
        {
            $data['i'] = 1;
            $data['searchdata'] = Input::get('searchdata');
            $searchdata = Input::get('searchdata');
            $data['pageGoto'] = Input::get('page');
            $pageFilter = Input::get('pagefilter');
            if($pageFilter)
            {
                $data['pages'] = Input::get('pagefilter');
                $pages = Input::get('pagefilter');
            }else{
                $data['pages'] = 50;
                $pages = 50;
            }

            $pageOrderBy = Input::get('Asc_Desc_Record');
            if($pageOrderBy)
            {
                $data['pageOrder'] = Input::get('Asc_Desc_Record');
                $pageOrder = Input::get('Asc_Desc_Record');
            }else{
                $data['pageOrder'] = "DESC";
                $pageOrder = "DESC";
            }

            $pageOrderBySelect = Input::get('Asc_Desc_Select');
            if($pageOrderBySelect)
            {
                $data['pageDescSelect'] = Input::get('Asc_Desc_Select');
                $pageAsc_Desc = Input::get('Asc_Desc_Select');
            }else{
                $data['pageDescSelect'] = "amc_id";
                $pageAsc_Desc = "amc_id";
            }

            if($searchdata)
            {
              $data['datarecords'] = AdminContact::where('amc_full_name','like','%'.$searchdata.'%')
              ->Orwhere('amc_email','like','%'.$searchdata.'%')
              ->Orwhere('amc_phone','like','%'.$searchdata.'%')
              ->orderBy('amc_id',$pageOrder)
              ->paginate($pages);
            }else{
                $data['datarecords'] = AdminContact::orderBy('amc_id',$pageOrder)->paginate($pages);
            }
            
            return view('admin.help-mgmt.index',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

     // User Active = 0 and Inactive = 1 
    public function updateStatus(Request $request)
    {
        try
        {  
            if($request->mode == "true")
            {
                $userStatus = AdminContact::where('amc_id',$request->amc_id)->update(array('amc_status' => 0));
                $data['status'] = "true";
                return $data;
            }
            else
            {
                $userStatus = AdminContact::where('amc_id',$request->amc_id)->update(array('amc_status' => 1));
                 $data['status'] = "false";
                return $data;
            }

        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    public function destroy($id)
    { 
        if(AdminContact::where('amc_id',$id)->exists())
        {
            AdminContact::where('amc_id',$id)->delete();
            Session::flash('success', 'Help information deleted');
            return redirect()->intended('help');
        }else{
            Session::flash('error', "Help information isn't deleted!");
            return redirect()->intended('help');
        }
    }
}
