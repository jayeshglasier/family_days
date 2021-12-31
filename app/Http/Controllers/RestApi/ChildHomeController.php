<?php

namespace App\Http\Controllers\RestApi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use App\Helper\ResponseMessage;
use App\Model\SystemSetting;
use App\Helper\Exceptions;
use App\Model\ChoreIcon;
use App\Model\ChoreStatus;
use App\Model\PresetChores;
use App\Model\Chores;
use App\Post;
use App\User;
use App\MediaFile;
use DB;
use Carbon\Carbon;

class ChildHomeController extends Controller
{
    public function childAssignFinishedChores(Request $request) // today
    {
        $header = $request->header('token');
        $loadMore = $request->load_more; // 2 filter
        $from_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->from_date)));
        $to_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->to_date)));

        $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
        $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));
        $status = $request->status;

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
                    $userRecord = DB::table('users')->select('id','use_full_name','use_image','use_total_point')->where('use_token',$header)->first();

                    $choreQuery = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time','cho_date','cho_is_expired','cho_last_date')->leftjoin('users','tbl_chores_list.cho_child_id','users.id')->where('cho_child_id',$userRecord->id)->where('cho_status',0);

                    if($loadMore == 1)
                    {   
                        if($status == "assign_chores")
                        {
                            if($fromDate && $toDate) // FROM DATE FILTER
                            {  
                                $adminChores = $choreQuery->whereBetween('cho_date', [$from_date,$to_date])->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                            }
                            else if($fromDate)
                            {
                                $adminChores = $choreQuery->whereDate('cho_date', $from_date)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                            }else{
                                $adminChores = $choreQuery->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->where('cho_is_daily',0)->limit(500)->get()->splice(6);
                            }
                        }else
                        {
                            $adminChores = $choreQuery->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->limit(500)->get()->splice(6);
                        }
                    }else if($loadMore == 0){

                        if($status == "assign_chores")
                        {       
                            if($fromDate && $toDate) // FROM DATE FILTER
                            {
                               $adminChores = $choreQuery->whereBetween('cho_date', [$from_date,$to_date])->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                            }
                            else if($fromDate)
                            {
                                $adminChores = $choreQuery->whereDate('cho_date', $from_date)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();
                            }else{
                              $adminChores = $choreQuery->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->limit(6)->get();
                            }
                        }else
                        {
                            $adminChores = $choreQuery->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->limit(6)->get();
                        }
                    }

                    // ----------------------- BEGIN ASSIGNED CHORES LIST -----------------------

                    if(!$adminChores->isEmpty())
                    { 
                        $userDetails = array();
                        foreach ($adminChores as $key => $value)
                        { 
                            if (Carbon::parse($value->cho_set_time)->gt($current_date))
                            {
                                $recordReward = $value->cho_last_date;

                                if($value->cho_icon)
                                {
                                    $profileurl = url("public/images/chore-icon/".$value->cho_icon);
                                }else{
                                    $profileurl = url("public/images/chore-icon/default-icon.png");
                                }

                                if($value->cho_is_daily == 1)
                                {
                                    $isDaily = 'Daily Chore';
                                }else{
                                    $isDaily = '';
                                }

                                if($recordReward)
                                {
                                    $DeferenceInDays = Carbon::parse($currentDate)->diffInDays($recordReward);

                                    if($value->cho_is_daily == 1)
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
                                $userDetails[] = array(
                                    "chore_id" => $value->cho_id,
                                    "title" => $value->cho_title,
                                    "create_by" => $value->use_full_name,
                                    "point" => $value->cho_point,
                                    "is_daily" => $isDaily,
                                    "is_complete" => $value->cho_is_complete,
                                    "is_admin_complete" => $value->cho_is_admin_complete,
                                    "is_conform" => $value->cho_is_confirmation,
                                    "is_admin" => $value->use_is_admin,
                                    "is_createby" => $value->cho_is_createby,
                                    "due_date" => $dueDate,
                                    "child_id" => $value->id,
                                    "token" => $value->use_token,
                                    "left_days" => $content,
                                    "icon_url" => $profileurl);
                            }
                        }

                        array_walk_recursive($userDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $userDetails;
                        $assignChores = $this->data[$key];
                        
                    }else
                    {
                       $assignChores = array();
                    }

                    // ----------------------- END ASSIGNED CHORES LIST -----------------------

                    // ----------------------- BEGIN FINISHED CHORES LIST -----------------------

                    $finishedQuery = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time','cho_date','cho_is_expired','cho_is_complete_date')->leftjoin('users','tbl_chores_list.cho_child_id','users.id')->where('cho_is_expired','Completed')->where('cho_child_id',$userRecord->id)->where('cho_status',1)->where('cho_is_expired','Completed');

                    if($loadMore == 1)
                    {   
                        if($status == "finished_chores")
                        {
                            if($fromDate && $toDate) // FROM DATE FILTER
                            {  
                               $finishedChores = $finishedQuery->whereBetween('cho_date', [$from_date,$to_date])->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->limit(500)->get();
                            }
                            else if($fromDate)
                            {
                                $finishedChores = $finishedQuery->whereDate('cho_date', $from_date)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->limit(500)->get();
                            }else{
                              $finishedChores = $finishedQuery->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->limit(500)->get()->splice(6);
                            }
                        }else{
                              $finishedChores = $finishedQuery->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->limit(500)->get()->splice(6);
                            }

                    }else if($loadMore == 0)
                    {
                        if($status == "finished_chores")
                        {
                            if($fromDate && $toDate) // FROM DATE FILTER
                            {  
                               $finishedChores = $finishedQuery->whereBetween('cho_date', [$from_date,$to_date])->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();
                            }
                            else if($fromDate)
                            {
                                $finishedChores = $finishedQuery->whereDate('cho_date', $from_date)->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->get();
                            }else{
                              $finishedChores = $finishedQuery->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->limit(6)->get();
                            }
                        }else{
                              $finishedChores = $finishedQuery->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'DESC')->limit(6)->get();
                            }
                    }

                    if(!$finishedChores->isEmpty())
                    { 
                        $finshedDetails = array();
                        foreach ($finishedChores as $key => $value)
                        { 
                            if($value->cho_icon)
                            {
                                $profileurl = url("public/images/chore-icon/".$value->cho_icon);
                            }else{
                                $profileurl = url("public/images/chore-icon/default-icon.png");
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
                            $finshedDetails[] = array("chore_id" => $value->cho_id,"title" => $value->cho_title,"create_by" => $value->use_full_name,"point" => $value->cho_point,"is_daily" => $isDaily, "is_complete" => $value->cho_is_complete,"is_admin_complete" => $value->cho_is_admin_complete,"is_conform" => $value->cho_is_confirmation,"is_admin" => $value->use_is_admin,"is_createby" => $value->cho_is_createby,"cho_is_expired" => $value->cho_is_expired,"due_date" => $dueDate,"token" => $value->use_token,"icon_url" => $profileurl);
                        }
                        array_walk_recursive($finshedDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $finshedDetails;
                        $finishedChores = $this->data[$key];
                    }else{
                        $finishedChores = array();
                    }
                    // ----------------------- END FINISHED CHORES LIST -----------------------

                    if($userRecord->use_image)
                    {
                        $profileurl = url("public/images/user-images/".$userRecord->use_image);
                    }else{
                        $profileurl = url("public/images/user-images/user-profile.png");
                    }
                   
                    $msg = "Assigned / finished chores details";
                    return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'child_name'=> $userRecord->use_full_name,'profile_url'=> $profileurl,'total_point'=> $userRecord->use_total_point,'assign_chore' => $assignChores,'finish_chore' => $finishedChores],JSON_UNESCAPED_SLASHES);
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
}