<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Helper\Exceptions;
use App\Helper\UserRights;
use Auth;
use Session;
use Input;

class MessageController extends Controller
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
                $data['pageDescSelect'] = "Cli_Id";
                $pageAsc_Desc = "Cli_Id";
            }

            if($searchdata)
            {
                $data['datarecords'] = Client::where('Cli_Name','like','%'.$searchdata.'%')->orderBy($pageAsc_Desc,$pageOrder)->paginate($pages);
            }else{
                $data['datarecords'] = User::join('tbl_roles','users.use_role','tbl_roles.rol_id')->where('use_role','<>',1)->orderBy('id',$pageOrder)->paginate($pages);
            }
            return view('admin.message-mgmt.index',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }
}
