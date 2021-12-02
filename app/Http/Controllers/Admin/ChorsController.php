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

class ChorsController extends Controller
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
                $data['datarecords'] = DB::table('users')
                ->select(array(
                DB::raw("users.*"),
                DB::raw("(SELECT COUNT(tbl_chores_list.cho_createby) FROM tbl_chores_list WHERE tbl_chores_list.cho_createby = users.id AND tbl_chores_list.cho_status = 0 AND tbl_chores_list.cho_is_daily = 0 GROUP BY tbl_chores_list.cho_createby) as no_of_chore"),
                DB::raw("(SELECT COUNT(tbl_chores_list.cho_createby) FROM tbl_chores_list WHERE tbl_chores_list.cho_createby = users.id AND tbl_chores_list.cho_status = 0 AND tbl_chores_list.cho_is_daily = 1 AND tbl_chores_list.cho_date = CURDATE() GROUP BY tbl_chores_list.cho_createby) as daily_chores"),
                DB::raw("(SELECT COUNT(tbl_chores_list.cho_createby) FROM tbl_chores_list WHERE tbl_chores_list.cho_createby = users.id AND tbl_chores_list.cho_status = 1 GROUP BY tbl_chores_list.cho_createby) as finished_chores")
                ))
                ->where('users.use_is_admin',1)
                ->where('users.use_status',0)
                ->orwhere('use_is_family_head',1)
                ->where('users.use_family_name','like','%'.$searchdata.'%')
                ->orderBy('id',$pageOrder)->paginate($pages);
            }else{

            $toDate = date('Y-m-d');

            $data['datarecords'] = DB::table('users')
            ->select(array(
            DB::raw("users.*"),
            DB::raw("(SELECT COUNT(tbl_chores_list.cho_createby) FROM tbl_chores_list WHERE tbl_chores_list.cho_createby = users.id AND tbl_chores_list.cho_status = 0 AND tbl_chores_list.cho_is_daily = 0 GROUP BY tbl_chores_list.cho_createby) as no_of_chore"),
            DB::raw("(SELECT COUNT(tbl_chores_list.cho_createby) FROM tbl_chores_list WHERE tbl_chores_list.cho_createby = users.id AND tbl_chores_list.cho_status = 0 AND tbl_chores_list.cho_is_daily = 1 AND tbl_chores_list.cho_date = CURDATE() GROUP BY tbl_chores_list.cho_createby) as daily_chores"),
            DB::raw("(SELECT COUNT(tbl_chores_list.cho_createby) FROM tbl_chores_list WHERE tbl_chores_list.cho_createby = users.id AND tbl_chores_list.cho_status = 1 GROUP BY tbl_chores_list.cho_createby) as finished_chores")
            ))
            ->where('users.use_is_admin',1)
            ->where('users.use_status',0)
            ->orwhere('use_is_family_head',1)
            ->orderBy('id',$pageOrder)->paginate($pages);
            }
            return view('admin.chors-mgmt.index',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    public function viewChores($id)
    {
        try 
        {   
            $data['familyId'] = $id;
            $userRecord = DB::table('users')->select('id','use_is_admin')->where('use_family_id',$id)->first();
            
            $lastYears = date("Y") - 4;
            $lastfiveYear = $lastYears."-01-01";
            $currentYears = date("Y") + 4;
            $currentYearDate = $currentYears."-12-31";

            $assignedChores = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time')->leftjoin('users','tbl_chores_list.cho_child_id','users.id')->where('cho_createby',$userRecord->id)
            ->whereBetween('cho_date', [$lastfiveYear, $currentYearDate]);

            if($userRecord->use_is_admin == 1)
            {
                $data['datarecords'] = $assignedChores->where('cho_is_daily',0)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->paginate(10);
            }else if($userRecord->use_is_admin == 0)
            {
                $data['datarecords'] = $assignedChores->where('cho_is_daily',0)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->paginate(10);
            }

             return view('admin.chors-mgmt.view',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    public function viewFinishedChores($id)
    {
        try 
        {   
            $data['familyId'] = $id;
            $lastYears = date("Y") - 4;
            $lastfiveYear = $lastYears."-01-01";
            $currentYears = date("Y");
            $currentYearDate = $currentYears."-12-31";

            $userRecord = DB::table('users')->select('id','use_is_admin')->where('use_family_id',$id)->first();

            $finishedChores = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time','cho_is_complete_date','cho_is_expired')
                ->leftjoin('users','tbl_chores_list.cho_child_id','users.id')
                ->where('cho_createby',$userRecord->id)
                ->whereBetween('cho_date', [$lastfiveYear, $currentYearDate]);

            if($userRecord->use_is_admin == 1)
            {
                $data['datarecords'] = $finishedChores->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->paginate(10);
            }else if($userRecord->use_is_admin == 0)
            {
                $data['datarecords'] = $finishedChores->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->paginate(10);
            }

             return view('admin.chors-mgmt.finished-chores-view',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    public function viewDailyChores($id)
    {
        try 
        {   
            $toDate = date('Y-m-d');
            $data['familyId'] = $id;
            $userRecord = DB::table('users')->select('id','use_is_admin')->where('use_family_id',$id)->first();
            
            $lastYears = date("Y") - 4;
            $lastfiveYear = $lastYears."-01-01";
            $currentYears = date("Y") + 4;
            $currentYearDate = $currentYears."-12-31";

            $assignedChores = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time')->leftjoin('users','tbl_chores_list.cho_child_id','users.id')->where('cho_createby',$userRecord->id)
            ->whereDate('cho_date', $toDate);

            if($userRecord->use_is_admin == 1)
            {
                $data['datarecords'] = $assignedChores->where('cho_is_daily',1)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->paginate(10);
            }else if($userRecord->use_is_admin == 0)
            {
                $data['datarecords'] = $assignedChores->where('cho_is_daily',1)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->paginate(10);
            }

             return view('admin.chors-mgmt.view-daily-chores',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    public function destroy($id)
    { 
        if(Chores::where('cho_id',$id)->exists())
        {
            $chores = Chores::select('cho_createby')->where('cho_id',$id)->first();
            $userDetails = User::select('use_family_id')->where('id',$chores->cho_createby)->first();
            Chores::where('cho_id',$id)->delete();
            Session::flash('success', 'Chores deleted successfully');
            return redirect()->intended('/view-chores-list/'.$userDetails->use_family_id);
        }else{
            $chores = Chores::select('cho_createby')->where('cho_id',$id)->first();
            $userDetails = User::select('use_family_id')->where('id',$chores->cho_createby)->first();
            Session::flash('error', 'Could not delete chores!');
            return redirect()->intended('/view-chores-list/'.$userDetails->use_family_id);
        }
    }
}
