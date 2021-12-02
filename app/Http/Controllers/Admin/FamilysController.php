<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Helper\Exceptions;
use App\Helper\UserRights;
use App\Model\Chores;
use Auth;
use Session;
use Input;

class FamilysController extends Controller
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
                $data['pages'] = 10;
                $pages = 10;
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
                $data['pageDescSelect'] = "id";
                $pageAsc_Desc = "id";
            }

            if($searchdata)
            {
                $data['datarecords'] = User::join('tbl_roles','users.use_role','tbl_roles.rol_id')
                ->where('use_username','like','%'.$searchdata.'%')
                ->Orwhere('email','like','%'.$searchdata.'%')
                ->Orwhere('use_family_name','like','%'.$searchdata.'%')
                ->Orwhere('use_full_name','like','%'.$searchdata.'%')
                ->Orwhere('use_phone_no','like','%'.$searchdata.'%')
                ->where('use_is_family_head',1)
                ->orderBy($pageAsc_Desc,$pageOrder)
                ->paginate($pages);
            }else{
                $data['datarecords'] = User::join('tbl_roles','users.use_role','tbl_roles.rol_id')->where('use_is_family_head',1)->orderBy($pageAsc_Desc,$pageOrder)->paginate($pages);
            }
            return view('admin.family-mgmt.index',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    public function viewDetails($id)
    {
        $usertDetail = User::select('use_fam_unique_id')->where('use_family_id',$id)->first();

        $data['datarecords'] = DB::table('users')->select('id as user_id','email','use_token as token','use_full_name as full_name','use_image','use_role','use_is_admin','rol_id','rol_name','use_phone_no','use_dob','use_username','use_is_family_head','use_family_id')->join('tbl_roles','users.use_role','tbl_roles.rol_id')->where('use_fam_unique_id',$usertDetail->use_fam_unique_id)->orderBy('use_role','ASC')->get();
        return view('admin.family-mgmt.view',$data);
    }

    public function editDetails($id)
    {
        $usertDetail = User::where('use_family_id',$id)->first();

        $data['datarecords'] = DB::table('users')->select('id as user_id','email','use_token as token','use_full_name as full_name','use_image','use_role','use_is_admin','rol_id','rol_name','use_phone_no','use_dob')->join('tbl_roles','users.use_role','tbl_roles.rol_id')->where('use_parents_id',$usertDetail->id)->orwhere('use_token',$usertDetail->use_token)->get();
        return view('admin.family-mgmt.edit',$data);
    }

    public function editFamilyMemberDetails($id)
    {   
        $data['updatedata'] = User::join('tbl_roles','users.use_role','tbl_roles.rol_id')->where('use_family_id',$id)->first();
        return view('users-mgmt/edit-family-member',$data);
    }

     // User Active = 0 and Inactive = 1 
    public function updateStatus(Request $request)
    {
        try
        {   
            if($request->mode == "true")
            {   
                $userStatus = User::where('use_fam_unique_id',$request->user_id)->update(array('use_status' => 0));
                $data['status'] = "true";
                return $data;
            }
            else
            {
                $userStatus = User::where('use_fam_unique_id',$request->user_id)->update(array('use_status' => 1));
                 $data['status'] = "false";
                return $data;
            }

        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

     public function destroy($id)
    { 
        if(User::where('use_fam_unique_id',$id)->exists())
        {
            $userData = User::select('id','use_fam_unique_id','use_total_member')->where('use_fam_unique_id',$id)->first();
            User::where('use_fam_unique_id',$id)->delete();
            Chores::where('cho_child_id',$userData->id)->delete();
            Session::flash('success', 'Family members delete SuccessFully..!');
            return redirect()->intended('/familys');
        }else{
          Session::flash('error', 'Family members is not Deleted..!');
          return redirect()->intended('/familys');
        }
    }

}
