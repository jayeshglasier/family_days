<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\StatusNotification;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActiveRewardExport;
use App\Exports\FamilyRewardExport;
use App\Helper\Exceptions;
use App\Helper\UserRights;
use App\Model\Chores;
use App\Model\Rewards;
use App\User;
use Auth;
use Session;
use Input;

class RewardsController extends Controller
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

            $lastYears = date("Y") - 4;
            $lastfiveYear = $lastYears."-01-01";
            $currentYears = date("Y") + 4;
            $currentYearDate = $currentYears."-12-31";

            if($searchdata)
            {
                $data['datarecords'] = DB::table('users')
                ->select(array(
                DB::raw("users.*"),
                DB::raw("(SELECT COUNT(tbl_rewards.red_status) FROM tbl_rewards WHERE tbl_rewards.red_use_createby = users.id AND tbl_rewards.red_status = 0 GROUP BY tbl_rewards.red_status) as no_of_chore")
                ))
                ->where('users.use_is_admin',1)
                ->where('users.use_status',0)
                ->where('users.use_family_name','like','%'.$searchdata.'%')
                ->orderBy('id',$pageOrder)->paginate($pages);
            }else{

            $data['datarecords'] = DB::table('users')
            ->select(array(
            DB::raw("users.*"),
            DB::raw("(SELECT COUNT(tbl_rewards.red_status) FROM tbl_rewards WHERE tbl_rewards.red_use_createby = users.id AND tbl_rewards.red_status = 0  GROUP BY tbl_rewards.red_status) as no_of_chore")
            ))
            ->where('users.use_is_admin',1)
            ->where('users.use_status',0)
            ->orwhere('use_is_family_head',1)
            ->orderBy('id',$pageOrder)->paginate($pages);
            }
            
            StatusNotification::where('sno_is_twenty_four',1)->where('sno_is_twelve',1)->where('sno_is_six',1)->where('sno_is_three',1)->where('sno_is_one',1)->delete();

            //AND tbl_rewards.red_frame_date BETWEEN CAST(".$lastfiveYear." AS DATE) AND CAST(".$currentYearDate." AS DATE)
            
            return view('admin.reward-mgmt.index',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    public function viewreward($id)
    {
        try 
        {   
            $userRecord = DB::table('users')->select('id','use_is_admin','use_fam_unique_id','use_family_id')->where('use_family_id',$id)->first();

            $data['familyId'] = $userRecord->use_family_id;

            $lastYears = date("Y") - 4;
            $lastfiveYear = $lastYears."-01-01";
            $currentYears = date("Y") + 4;
            $currentYearDate = $currentYears."-12-31";

            $data['datarecords'] = Rewards::select('red_icon','red_cat_name','red_rewards_name','red_point','use_full_name','red_frame_date','red_id','red_frame_date','red_brand_name','bds_link')->join('users','tbl_rewards.red_child_id','users.id')
            ->leftjoin('tbl_sub_brands','tbl_rewards.red_brand_icon','tbl_sub_brands.bds_brand_icon')
            ->where('red_family_id',$userRecord->use_fam_unique_id)
            ->whereBetween('red_frame_date', [$lastfiveYear, $currentYearDate])
            ->where('red_status',0)
            ->orderby('red_frame_date','ASC')
            ->paginate(10);
             return view('admin.reward-mgmt.view',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    public function expiredreward($id)
    { 
        try 
        {   
            $userRecord = DB::table('users')->select('id','use_is_admin','use_fam_unique_id','use_family_id')->where('use_family_id',$id)->first();

            $data['familyId'] = $userRecord->use_family_id;

            $lastYears = date("Y") - 4;
            $lastfiveYear = $lastYears."-01-01";
            $currentYears = date("Y") + 4;
            $currentYearDate = $currentYears."-12-31";

            $data['datarecords'] = Rewards::select('red_icon','red_cat_name','red_rewards_name','red_point','use_full_name','red_frame_date','red_id','red_frame_date','red_brand_name')
            ->join('users','tbl_rewards.red_child_id','users.id')
            ->where('red_family_id',$userRecord->use_fam_unique_id)
            ->whereBetween('red_expired_date', [$lastfiveYear, $currentYearDate])
            ->where('red_status',1)->orderby('red_frame_date','ASC')->paginate(10);
             return view('admin.reward-mgmt.expired-view',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    public function destroy($id)
    { 
        if(Rewards::where('red_id',$id)->exists())
        {
            $Rewards = Rewards::select('red_createdby')->where('red_id',$id)->first();
            $userDetails = User::select('use_family_id')->where('id',$Rewards->red_createdby)->first();
            Rewards::where('red_id',$id)->delete();
            Session::flash('success', 'Reward deleted');
            return redirect()->intended('/view-reward-list/'.$userDetails->use_family_id);
        }else{
            $Rewards = Rewards::select('red_createdby')->where('red_id',$id)->first();
            $userDetails = User::select('use_family_id')->where('id',$Rewards->red_createdby)->first();
            Session::flash('error', "Reward isn't deleted!");
            return redirect()->intended('/view-reward-list/'.$userDetails->use_family_id);
        }
    }

    public function exportExcel($id)
    {
        $data['familyId'] = $id;
        $userDetails = User::select('use_family_id','use_family_name')->where('use_family_id',$id)->first();

        return Excel::download(new ActiveRewardExport($data), $userDetails->use_family_name.'-reward-list'.'.xlsx');
    }

    public function exportFamilyReport()
    {
        return Excel::download(new FamilyRewardExport, 'all-reward-list.xlsx');
    }
}
