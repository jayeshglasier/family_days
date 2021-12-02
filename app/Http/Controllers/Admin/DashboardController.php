<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Helper\Exceptions;
use App\User;
use App\Model\Chores;
use App\Model\Rewards;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $todayDate = date('Y-m-d', strtotime("-3 days"));
        $adminChores = Chores::select('cho_id')->whereDate('cho_date', '<',$todayDate)->where('cho_status',0)->where('cho_is_expired','<>','Completed')->get();

        if(!$adminChores->isEmpty())
        {
            foreach($adminChores as $key => $value)
            {
                $updateData['cho_status'] = 1; // 0 = Assigned Chore / 1 = Finished
                $updateData['cho_is_complete'] = 0; // 0 = Complete 1 = Incompletes 2 = No any action
                $updateData['cho_is_confirmation'] = 0;  // 0 = Not conform 1 = Conform
                $updateData['cho_is_admin_complete'] = 2; // 0 = Complete 1 = Incompletes 2 = No any action
                $updateData['cho_is_expired'] = "Expired"; //0 = Complete 1 = Incompletes 2 = No any action
                $update = Chores::where('cho_id',$value->cho_id)->update($updateData);
            }
        }

        $rewardExpiredRecords = Rewards::where('red_status',0)->whereDate('red_frame_date', '<',$todayDate)->orderby('red_frame_date','ASC')->get();

        if(!$rewardExpiredRecords->isEmpty())
        {
            foreach($rewardExpiredRecords as $key => $expvalue)
            {
                $updateReward['red_status'] = 1; // 0 = Active / 1 = Inactive
                $updateReward['red_is_confirmation'] = 1;  // 0 = Yes 1 = No
                $updateReward['red_is_expired'] = "Expired";
                $updateReward['red_expired_date'] = $todayDate;
                $update = Rewards::where('red_id',$expvalue->red_id)->update($updateReward);
            }
        }

        $data['totalUser'] = User::where('use_role','<>',1)->count();
        $data['totalFamily'] = User::where('use_is_family_head',1)->where('use_role','<>',1)->count();
        return view('home',$data);
    }
}