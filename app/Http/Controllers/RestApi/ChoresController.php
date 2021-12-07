<?php

namespace App\Http\Controllers\RestApi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Helper\ResponseMessage;
use App\Model\Notification;
use App\Model\SystemSetting;
use App\Helper\Exceptions;
use App\Model\ChoreIcon;
use App\Model\ChoreStatus;
use App\Model\PresetChores;
use App\Model\DailyChores;
use App\Model\Chores;
use App\User;
use Validator;
use DB;
use Carbon\Carbon;

class ChoresController extends Controller
{
    public function assignedChores(Request $request)
    {
        $header = $request->header('token');
        $loadMore = $request->load_more;
        $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
        $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));
        $notificationId = $request->notification_id;

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {
                    $userRecord = DB::table('users')->select('id','use_is_admin','use_parents_id','use_role','use_is_reset','use_fam_unique_id')->where('use_token',$header)->first();

                    if($userRecord->use_is_reset != 0)
                    {
                        $update_req['use_is_reset'] = 0;
                        $update = User::where('use_token',$header)->update($update_req);
                    }

                    $updataData['not_is_read'] = 1;
                    $update = Notification::where('not_id',$notificationId)->update($updataData);

                    $updataIsRead['not_new_notification'] = 1;
                    $update = Notification::where('not_received_id',$userRecord->id)->update($updataIsRead);


                    //------------------------- BEGIN EXPIRED CHORES STORE -----------------------

                    $familyId = $userRecord->use_fam_unique_id;
                    $choresExpired = Chores::select('cho_id','cho_family_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time')->join('users','tbl_chores_list.cho_child_id','users.id')->where('cho_status',0)->where('cho_is_expired','<>','Completed')->where('cho_family_id',$familyId)->orderby('cho_id','DESC')->limit(1000)->get();

                    if(!$choresExpired->isEmpty())
                    { 
                        foreach ($choresExpired as $key => $value)
                        {
                            if (Carbon::parse($value->cho_set_time)->lt($current_date))
                            {   
                                $updateData['cho_status'] = 1; // 0 = Assigned Chore / 1 = Finished
                                $updateData['cho_is_complete'] = 0; // 0 = Complete 1 = Incompletes 2 = No any action
                                $updateData['cho_is_confirmation'] = 0;  // 0 = Not conform 1 = Conform
                                $updateData['cho_is_admin_complete'] = 2; // 0 = Complete 1 = Incompletes 2 = No any action
                                $updateData['cho_is_expired'] = "Expired"; //0 = Complete 1 = Incompletes 2 = No any action
                                if($value->cho_is_daily == 1)
                                {
                                    $is_daily = "daily_chores";
                                }else{
                                    $is_daily = "";
                                }
                                $updateData['cho_daily_chores'] = $is_daily;
                                $update = Chores::where('cho_id',$value->cho_id)->update($updateData);
                            }
                        }
                    }

                    //------------------------- END EXPIRED CHORES STORE -----------------------

                    $toDate = $currentDate;
                    $choresQuery = Chores::select('cho_id','cho_family_id','cho_title','cho_point','cho_icon','id','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time','cho_last_date')->join('users','tbl_chores_list.cho_child_id','users.id')->where('cho_family_id',$familyId)->where('cho_status',0)->where('cho_is_daily',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC');

                    $choresAssignCount = Chores::select('cho_status')->join('users','tbl_chores_list.cho_child_id','users.id')->where('cho_family_id',$familyId)->where('cho_status',0)->where('cho_is_confirmation',0)->count();

                    $dailyChores = Chores::select('cho_id','cho_family_id','cho_title','cho_point','cho_icon','id','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time','cho_date','cho_last_date')->join('users','tbl_chores_list.cho_child_id','users.id')->where('cho_family_id',$familyId)->where('cho_status',0)->where('cho_is_daily',1)->whereDate('cho_date',$toDate)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC');

                    $admindailyChores = $dailyChores->get()->toArray();
                    $adminChore = $choresQuery->get()->toArray();
                   
                    $adminChores = array_merge($admindailyChores, $adminChore);

                    if($adminChores)
                    { 
                        $i = 0; $userDetails = array();
                        foreach ($adminChores as $key => $value)
                        {   
                            $currentDate = date('Y-m-d');
                            $recordReward = $value['cho_last_date'];

                            if($recordReward)
                            {
                                $DeferenceInDays = Carbon::parse($currentDate)->diffInDays($recordReward);

                                if($value['cho_is_daily'] == 1)
                                {   
                                    if($DeferenceInDays == 0 || $DeferenceInDays == 1)
                                    {
                                        $leftDays = "1 day";
                                    }else{
                                        $leftDays = $DeferenceInDays. ' days';
                                    }
                                    $content = $leftDays.' left';
                                }else{
                                   $content = '';
                                }
                                
                            }else{
                                $content = '';
                            }

                            $i++;

                            if (Carbon::parse($value['cho_set_time'])->gt($current_date))
                            {
                                if($value['cho_icon'])
                                {
                                    $profileurl = url("public/images/chore-icon/".$value['cho_icon']);
                                }else{
                                    $profileurl = url("public/images/chore-icon/other-icon.png");
                                }

                                if($value['cho_is_daily'] == 1)
                                {
                                    $isDaily = 'Daily Chore';
                                }else{
                                    $isDaily = '';
                                }

                                $dueDate = date('d-m-Y H:i', strtotime(str_replace('/', '-', $value['cho_set_time'])));

                                if($loadMore == 1)
                                {
                                    if($i > 6)
                                    {
                                        $userDetails[] = array(
                                        "chore_id" => $value['cho_id'],
                                        "title" => $value['cho_title'],
                                        "create_by" => $value['use_full_name'],
                                        "point" => $value['cho_point'],
                                        "is_daily" => $isDaily,
                                        "is_complete" => $value['cho_is_complete'],
                                        "is_admin_complete" => $value['cho_is_admin_complete'],
                                        "is_conform" => $value['cho_is_confirmation'],
                                        "is_admin" => $value['use_is_admin'],
                                        "is_createby" => $value['cho_is_createby'],
                                        "due_date" => $dueDate,
                                        "child_id" => $value['id'],
                                        "token" => $value['use_token'],
                                        "left_days" => $content,
                                        "icon_url" => $profileurl);
                                    }
                                }else{
                                    if($i <= 6)
                                    {
                                        $userDetails[] = array(
                                        "chore_id" => $value['cho_id'],
                                        "title" => $value['cho_title'],
                                        "create_by" => $value['use_full_name'],
                                        "point" => $value['cho_point'],
                                        "is_daily" => $isDaily,
                                        "is_complete" => $value['cho_is_complete'],
                                        "is_admin_complete" => $value['cho_is_admin_complete'],
                                        "is_conform" => $value['cho_is_confirmation'],
                                        "is_admin" => $value['use_is_admin'],
                                        "is_createby" => $value['cho_is_createby'],
                                        "due_date" => $dueDate,
                                        "child_id" => $value['id'],
                                        "token" => $value['use_token'],
                                        "left_days" => $content,
                                        "icon_url" => $profileurl);
                                    }
                                }
                            }
                        }

                        array_walk_recursive($userDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $userDetails;
                        $msg = "Assigned chores details";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $msg, 'disapproved_count' => $choresAssignCount,'data' => $this->data[$key]],JSON_UNESCAPED_SLASHES);
                    }else
                    {
                        $msg = "Assigned chores details";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $msg, 'disapproved_count' => 0,'data' => array()],JSON_UNESCAPED_SLASHES);
                    }
                }
                else
                {
                    ResponseMessage::error("Your account isn't active.");
                }
            }else{
                ResponseMessage::error("Token isn't valid!");
            }
        }else{
           ResponseMessage::error("Token is required!");
        }
    }

    public function assignedChoresFilter(Request $request)
    {
        $header = $request->header('token');
        $loadMore = $request->load_more;
        $from_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->from_date)));
        $to_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->to_date)));
        $childId = $request->child_id;

        if($from_date == "1970-01-01")
        {
            $fromDate = "";
        }else{
            $fromDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->from_date)));
        }
        
        if($to_date == "1970-01-01")
        {
            $toDate = "";
        }else{
            $toDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->to_date)));
        }

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {
                    $userRecord = DB::table('users')->select('id','use_fam_unique_id','use_is_admin','use_parents_id','use_role','use_is_reset')->where('use_token',$header)->first();

                    $choreQuery = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time','cho_date','cho_is_expired','cho_last_date')->leftjoin('users','tbl_chores_list.cho_child_id','users.id')->where('cho_is_daily',0);
                  
                    if($userRecord->use_is_admin == 1)
                    {
                        if($fromDate && $toDate && $childId)
                        {
                            $adminChores = $choreQuery->whereBetween('cho_date', [$from_date,$to_date])->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();

                        }else if($fromDate && $toDate) // FROM DATE FILTER
                        {  
                           $adminChores = $choreQuery->whereBetween('cho_date', [$from_date,$to_date])->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();

                        }else if($fromDate && $childId)
                        {
                            $adminChores = $choreQuery->whereDate('cho_date', $from_date)->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                        }
                        else if($fromDate)
                        {
                            $adminChores = $choreQuery->whereDate('cho_date', $from_date)->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                        }
                        else if($childId)
                        {  
                            $adminChores = $choreQuery->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                        }else{
                             $adminChores = $choreQuery->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                        }
                    }else
                    {
                        if($fromDate && $toDate && $childId)
                        {
                            $adminChores = $choreQuery->whereBetween('cho_date', [$from_date,$to_date])->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();

                        }else if($fromDate && $toDate) // FROM DATE FILTER
                        {  
                           $adminChores = $choreQuery->whereBetween('cho_date', [$from_date,$to_date])->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();

                        }else if($fromDate && $childId)
                        {
                            $adminChores = $choreQuery->whereDate('cho_date', $from_date)->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                        }
                        else if($fromDate)
                        {
                            $adminChores = $choreQuery->whereDate('cho_date', $from_date)->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                        }
                        else if($childId)
                        {  
                            $adminChores = $choreQuery->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                        }else{
                             $adminChores = $choreQuery->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',0)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                        }
                    }

                    if(!$adminChores->isEmpty())
                    { 
                        $userDetails = array();
                        foreach ($adminChores as $key => $value)
                        { 
                            if (Carbon::parse($value->cho_set_time)->gt(Carbon::now()))
                            {
                                 if($value->cho_icon)
                                {
                                    $profileurl = url("public/images/chore-icon/".$value->cho_icon);
                                }else{
                                    $profileurl = url("public/images/chore-icon/other-icon.png");
                                }

                                if($value->cho_is_daily == 1)
                                {
                                    $isDaily = 'Daily Chore';
                                }else{
                                    $isDaily = '';
                                }

                                $currentDate = date('Y-m-d');
                                $recordReward = $value['cho_last_date'];

                                if($recordReward)
                                {
                                    $DeferenceInDays = Carbon::parse($currentDate)->diffInDays($recordReward);

                                    if($value['cho_is_daily'] == 1)
                                    {   
                                        if($DeferenceInDays == 0 || $DeferenceInDays == 1)
                                        {
                                            $leftDays = "1 day";
                                        }else{
                                            $leftDays = $DeferenceInDays. ' days';
                                        }
                                        $content = $leftDays.' left';
                                    }else{
                                        $content = '';
                                    }
                                    
                                }else{
                                    $content = '';
                                }

                                $dueDate = date('d-m-Y H:i', strtotime(str_replace('/', '-', $value->cho_set_time)));
                                $userDetails[] = array("chore_id" => $value->cho_id,"title" => $value->cho_title,"create_by" => $value->use_full_name,"point" => $value->cho_point,"is_daily" => $isDaily,"is_complete" => $value->cho_is_complete,"is_admin_complete" => $value->cho_is_admin_complete,"is_conform" => $value->cho_is_confirmation,"is_admin" => $value->use_is_admin,"is_createby" => $value->cho_is_createby,"due_date" => $dueDate,"left_days" => $content,"token" => $value->use_token,"icon_url" => $profileurl);
                            }
                        }

                        array_walk_recursive($userDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $userDetails;

                        $msg = "Assigned chores details";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => $this->data[$key]],JSON_UNESCAPED_SLASHES);
                    }else
                    {
                        $msg = "Assigned chores details";
                       return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => array()],JSON_UNESCAPED_SLASHES);
                    }
                }
                else
                {
                    $msg = "Your account isn't active.";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{
                $msg = "Token isn't valid!";
                return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }
        }else{
            $msg = "Token is required!";
            return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
        }
    }

    public function finishedChores(Request $request) 
    {
        $header = $request->header('token');
        $loadMore = $request->load_more;

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                { 
                    $userRecord = DB::table('users')->select('id','use_fam_unique_id','use_is_admin','use_parents_id','use_role')->where('use_token',$header)->first();
                    
                    if($userRecord->use_is_admin == 1)
                    {
                        $userId = $userRecord->id;

                        if($loadMore == 1)
                        {
                            $adminChores = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time','cho_is_expired','cho_is_complete_date')->leftjoin('users','tbl_chores_list.cho_child_id','users.id')->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get()->splice(6);
                        }else{
                            $adminChores = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time','cho_is_expired','cho_is_complete_date')->leftjoin('users','tbl_chores_list.cho_child_id','users.id')->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->limit(6)->get();
                        }

                    }else if($userRecord->use_role == 2 || $userRecord->use_role == 3)
                    {
                        $userId = $userRecord->use_parents_id;

                        if($loadMore == 1)
                        {

                        $adminChores = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_is_complete_date')->leftjoin('users','tbl_chores_list.cho_child_id','users.id','cho_set_time','cho_is_expired')->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get()->splice(6);
                        }else{
                        $adminChores = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_is_complete_date')->leftjoin('users','tbl_chores_list.cho_child_id','users.id','cho_set_time','cho_is_expired')->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->limit(6)->get();
                        }
                    }
                    else if($userRecord->use_is_admin == 0)
                    {
                        $userId = $userRecord->use_parents_id;
                        
                        if($loadMore == 1)
                        {
                        $adminChores = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time','cho_is_expired','cho_is_complete_date')->leftjoin('users','tbl_chores_list.cho_child_id','users.id')->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get()->splice(6);
                        }else{
                            $adminChores = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time','cho_is_expired','cho_is_complete_date')->leftjoin('users','tbl_chores_list.cho_child_id','users.id')->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->limit(6)->get();
                        }
                    }
                    
                    if(!$adminChores->isEmpty())
                    { 
                        $userDetails = array();
                        foreach ($adminChores as $key => $value)
                        { 
                            if($value->cho_icon)
                            {
                                $profileurl = url("public/images/chore-icon/".$value->cho_icon);
                            }else{
                                $profileurl = url("public/images/chore-icon/other-icon.png");
                            }

                            if($value->cho_is_daily == 1)
                            {
                                $isDaily = 'Daily Chore';
                            }else{
                                $isDaily = '';
                            }

                            if($value->cho_is_complete_date)
                            {
                                $dueDate = date('d-m-Y H:i', strtotime(str_replace('/', '-', $value->cho_is_complete_date)));   
                            }else{
                                $dueDate = date('d-m-Y H:i', strtotime(str_replace('/', '-', $value->cho_set_time)));
                            }
                            $userDetails[] = array("chore_id" => $value->cho_id,"title" => $value->cho_title,"create_by" => $value->use_full_name,"point" => $value->cho_point,"is_daily" => $isDaily, "is_complete" => $value->cho_is_complete,"is_admin_complete" => $value->cho_is_admin_complete,"is_conform" => $value->cho_is_confirmation,"is_admin" => $value->use_is_admin,"is_createby" => $value->cho_is_createby,"cho_is_expired" => $value->cho_is_expired,"due_date" => $dueDate,"token" => $value->use_token,"icon_url" => $profileurl);
                        }

                        array_walk_recursive($userDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $userDetails;

                        $choresAssignCount = Chores::select('cho_status')->join('users','tbl_chores_list.cho_child_id','users.id')->where('cho_createby',$userId)->where('cho_status',1)->where('cho_is_admin_complete',2)->count();

                        $msg = "Finished chores details";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'chore_incomplete_count' => $choresAssignCount,'data' => $this->data[$key]],JSON_UNESCAPED_SLASHES);
                    }else
                    {
                        $msg = "Finished chores details";
                       return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'chore_incomplete_count' => 0,'data' => array()],JSON_UNESCAPED_SLASHES);
                    }
                }
                else
                {
                    $msg = "Your account isn't active.";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{
                $msg = "Token isn't valid!";
                return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }
        }else{
            $msg = "Token is required!";
            return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
        }
    }

    public function finishedChoresFilter(Request $request) 
    {
        $header = $request->header('token');
        $loadMore = $request->load_more;
        $from_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->from_date)));
        $to_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->to_date)));
        $childId = $request->child_id;

        if($from_date == "1970-01-01")
        {
            $fromDate = "";
        }else{
            $fromDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->from_date)));
        }
        
        if($to_date == "1970-01-01")
        {
            $toDate = "";
        }else{
            $toDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->to_date)));
        }

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                { 
                    $userRecord = DB::table('users')->select('id','use_fam_unique_id','use_is_admin','use_parents_id','use_role')->where('use_token',$header)->first();

                    $choreQuery = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time','cho_date','cho_is_expired','cho_is_complete_date')->leftjoin('users','tbl_chores_list.cho_child_id','users.id')->where('cho_is_daily',0);
                    
                    if($userRecord->use_is_admin == 1)
                    {
                       
                        if($fromDate && $toDate && $childId)
                        {
                            $adminChores = $choreQuery->where('cho_is_expired','Completed')->whereBetween('cho_date', [$from_date,$to_date])->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();

                        }else if($fromDate && $toDate) // FROM DATE FILTER
                        {  
                           $adminChores = $choreQuery->where('cho_is_expired','Completed')->whereBetween('cho_date', [$from_date,$to_date])->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();

                        }else if($fromDate && $childId)
                        {
                            $adminChores = $choreQuery->where('cho_is_expired','Completed')->whereDate('cho_date', $from_date)->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();
                        }
                        else if($fromDate)
                        {
                            $adminChores = $choreQuery->where('cho_is_expired','Completed')->whereDate('cho_date', $from_date)->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();
                        }
                        else if($childId)
                        {  
                            $adminChores = $choreQuery->where('cho_is_expired','Completed')->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();
                        }else{
                             $adminChores = $choreQuery->where('cho_is_expired','Completed')->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();
                        }
                        
                    }
                    else if($userRecord->use_role == 2 || $userRecord->use_role == 3 || $userRecord->use_role == 4 || $userRecord->use_role == 5)
                    {
                        if($fromDate && $toDate && $childId)
                        {
                            $adminChores = $choreQuery->where('cho_is_expired','Completed')->whereBetween('cho_date', [$from_date,$to_date])->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();

                        }else if($fromDate && $toDate) // FROM DATE FILTER
                        {  
                           $adminChores = $choreQuery->where('cho_is_expired','Completed')->whereBetween('cho_date', [$from_date,$to_date])->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();

                        }else if($fromDate && $childId)
                        {
                            $adminChores = $choreQuery->where('cho_is_expired','Completed')->whereDate('cho_date', $from_date)->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();
                        }
                        else if($fromDate)
                        {
                            $adminChores = $choreQuery->where('cho_is_expired','Completed')->whereDate('cho_date', $from_date)->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();
                        }
                        else if($childId)
                        {  
                            $adminChores = $choreQuery->where('cho_is_expired','Completed')->whereIn('cho_child_id',explode(',',$childId))->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();
                        }else{
                             $adminChores = $choreQuery->where('cho_is_expired','Completed')->where('cho_family_id',$userRecord->use_fam_unique_id)->where('cho_status',1)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();
                        }
                        
                    }
                    
                    if(!$adminChores->isEmpty())
                    { 
                        $userDetails = array();
                        foreach ($adminChores as $key => $value)
                        { 
                            if($value->cho_icon)
                            {
                                $profileurl = url("public/images/chore-icon/".$value->cho_icon);
                            }else{
                                $profileurl = url("public/images/chore-icon/other-icon.png");
                            }

                            if($value->cho_is_daily == 1)
                            {
                                $isDaily = 'Daily Chore';
                            }else{
                                $isDaily = '';
                            }

                            if($value->cho_is_complete_date)
                            {
                                $dueDate = date('d-m-Y H:i', strtotime(str_replace('/', '-', $value->cho_is_complete_date)));   
                            }else{
                                $dueDate = date('d-m-Y H:i', strtotime(str_replace('/', '-', $value->cho_set_time)));
                            }
                            $userDetails[] = array("chore_id" => $value->cho_id,"title" => $value->cho_title,"create_by" => $value->use_full_name,"point" => $value->cho_point,"is_daily" => $isDaily, "is_complete" => $value->cho_is_complete,"is_admin_complete" => $value->cho_is_admin_complete,"is_conform" => $value->cho_is_confirmation,"is_admin" => $value->use_is_admin,"is_createby" => $value->cho_is_createby,"cho_is_expired" => $value->cho_is_expired,"due_date" => $dueDate,"token" => $value->use_token,"icon_url" => $profileurl);
                        }

                        array_walk_recursive($userDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $userDetails;

                        $msg = "Finished chores details";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => $this->data[$key]],JSON_UNESCAPED_SLASHES);
                    }else
                    {
                        $msg = "Finished chores details";
                       return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => array()],JSON_UNESCAPED_SLASHES);
                    }
                }
                else
                {
                    $msg = "Your account isn't active.";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{
                $msg = "Token isn't valid!";
                return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }
        }else{
            $msg = "Token is required!";
            return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
        }
    }
    
    public function getChoreIcons()
    {
        try
        {
            $userRecords = PresetChores::select('pre_id','pre_icon')->where('pre_status',0)->get();

            if(!$userRecords->isEmpty())
            { 
                $iconDetails = array();
                foreach ($userRecords as $key => $value)
                { 
                    $iconurl = url("public/images/chore-icon/".$value->pre_icon);
                    $iconDetails[] = array("icon_id" => $value->pre_id,"icon_name" => $value->pre_icon, "icon_url" => $iconurl);
                }

                array_walk_recursive($iconDetails, function (&$item, $key) {
                $item = null === $item ? '' : $item;
                });
                $this->data[$key] = $iconDetails;

                $message = "Preset icons list";
                return json_encode(['status' => true, 'error' => 200, 'message' => $message, 'data'=> $this->data[$key]],JSON_UNESCAPED_SLASHES);
            }else{
              $msg = "Preset icons in not found";
              return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => array()],JSON_UNESCAPED_SLASHES);
            }
                    
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function presetChores()
    {
        try
        {
            $presetChores = PresetChores::select('pre_id','pre_title','pre_icon')->where('pre_status',0)->where('pre_title','<>','')->orderby('pre_title','ASC')->get();

            if(!$presetChores->isEmpty())
            { 
                $iconDetails = array();
                foreach ($presetChores as $key => $value)
                { 
                    $iconurl = url("public/images/chore-icon/".$value->pre_icon);
                    $iconDetails[] = array("id" => $value->pre_id,"title" => $value->pre_title,"icon_name" => $value->pre_icon,"icon" => $iconurl);
                }

                array_walk_recursive($iconDetails, function (&$item, $key) {
                $item = null === $item ? '' : $item;
                });
                $this->data[$key] = $iconDetails;

                $minmaxPoint = SystemSetting::where('sys_id',1)->first();
                
                $message = "Preset chores list";
                return json_encode(['status' => true, 'error' => 200, 'message' => $message, "minimum_point" => $minmaxPoint->sys_min_chores,"maximum_point" => $minmaxPoint->sys_max_chores,'data'=> $this->data[$key]],JSON_UNESCAPED_SLASHES);
            }else{
              $msg = "Preset chores record not found";
              return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => array()],JSON_UNESCAPED_SLASHES);
            }
                    
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }
}