<?php

namespace App\Http\Controllers\RestApi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Helper\ResponseMessage;
use App\Helper\NotificationKey;
use App\Model\SystemSetting;
use App\Helper\Exceptions;
use App\Model\ChoreIcon;
use App\Model\ChoreStatus;
use App\Model\PresetChores;
use App\Model\Notification;
use App\Model\StatusNotification;
use App\Model\Chores;
use App\Model\DailyChores;
use App\Post;
use App\User;
use App\MediaFile;
use Validator;
use DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;


class ChoresModuleController extends Controller
{  
    /// Admin create chore api

    public function createChore(Request $request,$notificationId = 0)
    {
        $header = $request->header('token');

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {
                    $userRecord = DB::table('users')->select('id','use_role','use_username','use_is_admin','use_parents_id','use_family_id','use_fam_unique_id')->where('use_token',$header)->first();

                    $notification = NotificationKey::notificationType();
                   
                    if($userRecord->use_is_admin == 1)
                    {
                        $createbyId = $userRecord->id;

                    }else if($userRecord->use_is_admin == 0)
                    {
                        $createbyId = $userRecord->use_parents_id;

                    }else{
                        
                        $createbyId = 0;
                    }

                    if($userRecord)
                    { 
                        $childId = explode(',',$request->child_id);
                        if($request->child_id)
                        {
                            $rules = [
                                'icon_name' => 'required',
                                'title' => 'required',
                                'chore_time' => 'required|date_format:Y-m-d H:i:s'
                                ];

                            $validator = Validator::make($request->all(), $rules);

                            if($validator->fails())
                            {
                                $errors = $validator->errors();
                                foreach ($errors->all() as $message) {                
                                    return json_encode(['status' => false, 'error' => 401, 'message' => $message],JSON_UNESCAPED_SLASHES);
                                }
                            }else
                            {
                            $uniqueId = date("YmdHis");
                            foreach ($childId as $key => $value) 
                            { 
                                if(User::where('id',$value)->exists())
                                {
                                    if($request['chore_time'])
                                    {
                                        $choreTime = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request['chore_time'])));
                                        $time = date('H:i:s', strtotime(str_replace('/', '-', $request['chore_time'])));
                                        $choreDate = date('Y-m-d', strtotime(str_replace('/', '-', $request['chore_time'])));
                                    }else{
                                        $choreTime = date('Y-m-d H:i:s');
                                        $time = date('H:i:s');
                                        $choreDate = date('Y-m-d');
                                    }

                                    $todayDate = date('Y-m-d');

                                    if($request['is_daily_chore'] == 1)
                                    {
                                        if(strtotime($todayDate) > strtotime($choreDate))
                                        {   //code to validate start and end date
                                            return ResponseMessage::error("Daily chores date should be greater than Today date!");
                                        }else{
                                            $period = CarbonPeriod::create($todayDate, $choreDate);

                                            $uniqueId = date("YmdHis");

                                            $i = 0; $l = 0; foreach ($period as $dateDaily)
                                            {
                                                $l++;
                                                $j = $i++;
                                                $k = $j+1;
                                                $insertData = new Chores;
                                                $insertData->cho_unique_id = $k.$uniqueId;
                                                $insertData->cho_family_id = $userRecord->use_fam_unique_id;
                                                $insertData->cho_icon = $request['icon_name'];
                                                $insertData->cho_title = $request['title'];
                                                $insertData->cho_is_daily = 1; // 0 = No Daily 1 = Daily
                                                $insertData->cho_daily_chores = "daily_chores";
                                                $insertData->cho_point = $request['set_point'] ? $request['set_point']:10;
                                                $insertData->cho_set_time = $dateDaily->format('Y-m-d').' ' .$time;
                                                $insertData->cho_date = $dateDaily->format('Y-m-d');
                                                $insertData->cho_status = 0; // 0 = Assigned Chore / 1 = Finished
                                                $insertData->cho_is_complete = 0; // 0 = No Daily 1 = Daily
                                                $insertData->cho_is_confirmation = 1;  // 0 = Not conform 1 = Conform
                                                $insertData->cho_is_createby = 0;  // 0 = Parents Create , 1 = Child Create Chore                                                 $insertData->cho_is_admin_complete = 0; //0 = Complete 1 = Incompletes 2 = No any action  
                                                $insertData->cho_child_id = $value;
                                                $insertData->cho_createby = $createbyId; //0 = Parents 1 = Child create by
                                                $insertData->cho_is_expired = "";
                                                $insertData->cho_last_date = $choreDate;
                                                $insertData->cho_createat = $dateDaily->format('Y-m-d H:i:s');
                                                $insertData->cho_updateat = $dateDaily->format('Y-m-d H:i:s');
                                                $insertData->save();

                                                $insertStatus = new StatusNotification;
                                                $insertStatus->sno_chores_id = $insertData->cho_id;
                                                $insertStatus->sno_is_twenty_four = 0;
                                                $insertStatus->sno_is_twelve = 0;
                                                $insertStatus->sno_is_six = 0;
                                                $insertStatus->sno_is_three = 0;
                                                $insertStatus->sno_is_one = 0;
                                                $insertStatus->sno_createat = date('Y-m-d H:i:s');
                                                $insertStatus->sno_updateat = date('Y-m-d H:i:s');
                                                $insertStatus->save();

                                                if($l <= 1)
                                                {
                                                    $childDetails = User::select('id as child_id','use_username','use_full_name')->where('id',$value)->first();

                                                    $insertMessage = new Notification;
                                                    $insertMessage->not_type = $notification['choreByParentType'];
                                                    $insertMessage->not_content = $notification['choreByParentContent'];
                                                    $insertMessage->not_sender_id = $userRecord->id;
                                                    $insertMessage->not_received_id = $value;
                                                    $insertMessage->not_chores_id = $insertData->cho_id;
                                                    $insertMessage->not_reward_id = '';
                                                    $insertMessage->not_claim_id = '';
                                                    $insertMessage->not_message_id = '';
                                                    $insertMessage->not_data = $request->title ? $request->title:'';
                                                    $insertMessage->not_is_read = 0; // 0 = Not read 1 = read
                                                    $insertMessage->not_child_name = $childDetails->use_username;
                                                    $insertMessage->not_read_at = date('Y-m-d H:i:s');
                                                    $insertMessage->not_createdat = date('Y-m-d H:i:s');
                                                    $insertMessage->not_updatedat = date('Y-m-d H:i:s');
                                                    $insertMessage->save();

                                                    $insertChores = new DailyChores;
                                                    $insertChores->cho_child_id = $value;
                                                    $insertChores->chd_title = $request['title'];
                                                    $insertChores->chd_date = $choreDate;
                                                    $insertChores->chd_createdat = date('Y-m-d H:i:s');
                                                    $insertChores->chd_updatedat = date('Y-m-d H:i:s');
                                                    $insertChores->save();
                                                }
                                            }
                                        }                                        
                                    }else{

                                        $insertData = new Chores;
                                        $insertData->cho_unique_id = $uniqueId;
                                        $insertData->cho_family_id = $userRecord->use_fam_unique_id;
                                        $insertData->cho_icon = $request['icon_name'];
                                        $insertData->cho_title = $request['title'];
                                        $insertData->cho_is_daily = 0; // 0 = No Daily 1 = Daily
                                        $insertData->cho_daily_chores = "";
                                        $insertData->cho_point = $request['set_point'] ? $request['set_point']:10;
                                        $insertData->cho_set_time = $choreTime;
                                        $insertData->cho_date = $choreDate;
                                        $insertData->cho_status = 0; // 0 = Assigned Chore / 1 = Finished
                                        $insertData->cho_is_complete = 0; // 0 = No Daily 1 = Daily
                                        $insertData->cho_is_confirmation = 1;  // 0 = Not conform 1 = Conform
                                        $insertData->cho_is_createby = 0;  // 0 = Parents Create , 1 = Child Create Chore                                         $insertData->cho_is_admin_complete = 0; //0 = Complete 1 = Incompletes 2 = No any action  
                                        $insertData->cho_child_id = $value;
                                        $insertData->cho_createby = $createbyId; //0 = Parents 1 = Child create by
                                        $insertData->cho_is_expired = "";
                                        $insertData->cho_last_date = $choreDate;
                                        $insertData->cho_createat = date('Y-m-d H:i:s');
                                        $insertData->cho_updateat = date('Y-m-d H:i:s');
                                        $insertData->save();

                                        $insertStatus = new StatusNotification;
                                        $insertStatus->sno_chores_id = $insertData->cho_id;
                                        $insertStatus->sno_is_twenty_four = 0;
                                        $insertStatus->sno_is_twelve = 0;
                                        $insertStatus->sno_is_six = 0;
                                        $insertStatus->sno_is_three = 0;
                                        $insertStatus->sno_is_one = 0;
                                        $insertStatus->sno_createat = date('Y-m-d H:i:s');
                                        $insertStatus->sno_updateat = date('Y-m-d H:i:s');
                                        $insertStatus->save();

                                        $childDetails = User::select('id as child_id','use_username','use_full_name')->where('id',$value)->first();

                                        $getFamilyDetails = User::select('id as user_id','use_fcm_token')->where('use_fam_unique_id',$userRecord->use_fam_unique_id)->whereIn('use_role',[2,3])->get();

                                            $arrayToken = array();
                                            foreach ($getFamilyDetails as $key => $tokenValue) {

                                                if($tokenValue->user_id != $userRecord->id)
                                                {
                                                    $insertNoti = new Notification;
                                                    $insertNoti->not_type = $notification['choreByParentType'];
                                                    $insertNoti->not_content = $notification['choreByParentContent'];
                                                    $insertNoti->not_sender_id = $userRecord->id;
                                                    $insertNoti->not_received_id = $tokenValue->user_id;
                                                    $insertNoti->not_chores_id = $insertData->cho_id;
                                                    $insertNoti->not_reward_id = '';
                                                    $insertNoti->not_claim_id = '';
                                                    $insertNoti->not_message_id = '';
                                                    $insertNoti->not_data = $request->title ? $request->title:'';
                                                    $insertNoti->not_is_read = 0; // 0 = Not read 1 = read
                                                    $insertNoti->not_child_name = $childDetails->use_username; //0 = Parents 1 = Child create by
                                                    $insertNoti->not_read_at = date('Y-m-d H:i:s');
                                                    $insertNoti->not_createdat = date('Y-m-d H:i:s');
                                                    $insertNoti->not_updatedat = date('Y-m-d H:i:s');
                                                    $insertNoti->save();

                                                    $notificationId = $insertNoti->not_id;
                                                    if($tokenValue['use_fcm_token'])
                                                    {
                                                        $notificationCount = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')->where('not_received_id',$tokenValue->user_id)->where('not_new_notification',0)->count('not_new_notification');

                                                        $this->notification($tokenValue['use_fcm_token'],$request->title,$notification['choreByParentContent'],$notification['choreByParentType'],$childDetails->use_username,$notificationId,$notificationCount);
                                                    }
                                                }
                                               
                                            }

                                        $insertMessage = new Notification;
                                        $insertMessage->not_type = $notification['choreByParentType'];
                                        $insertMessage->not_content = $notification['choreByParentContent'];
                                        $insertMessage->not_sender_id = $userRecord->id;
                                        $insertMessage->not_received_id = $value;
                                        $insertMessage->not_chores_id = $insertData->cho_id;
                                        $insertMessage->not_reward_id = '';
                                        $insertMessage->not_claim_id = '';
                                        $insertMessage->not_message_id = '';
                                        $insertMessage->not_data = $request->title ? $request->title:'';
                                        $insertMessage->not_is_read = 0; // 0 = Not read 1 = read
                                        $insertMessage->not_child_name = $childDetails->use_username; //0 = Parents 1 = Child create by
                                        $insertMessage->not_read_at = date('Y-m-d H:i:s');
                                        $insertMessage->not_createdat = date('Y-m-d H:i:s');
                                        $insertMessage->not_updatedat = date('Y-m-d H:i:s');
                                        $insertMessage->save();
                                    }
                                    
                                    $notificationId = $insertMessage->not_id;
                                    
                                    $getToken = User::select('use_fcm_token','use_username')->where('id',$value)->first();

                                    if($getToken)
                                    {
                                        $notificationCount = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')->where('not_received_id',$value)->where('not_new_notification',0)->count('not_new_notification');

                                        $this->notification($getToken['use_fcm_token'],$request->title,$notification['choreByParentContent'],$notification['choreByParentType'],$getToken['use_username'],$notificationId,$notificationCount);
                                    }

                                }
                            }

                            //------------------------- BEGIN EXPIRED CHORES STORE -----------------------
                            $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
                            $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));

                            $choresExpired = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_complete','use_is_admin','use_token','cho_is_confirmation','cho_is_daily','cho_is_createby','cho_child_id','cho_is_admin_complete','cho_set_time')->join('users','tbl_chores_list.cho_child_id','users.id')->where('cho_status',0)->where('cho_is_expired','<>','Completed')->where('cho_createby',$userRecord->id)->orderby('cho_id','DESC')->limit(1000)->get();

                            if(!$choresExpired->isEmpty())
                            {
                                foreach ($choresExpired as $key => $value)
                                {
                                    if (Carbon::parse($value->cho_set_time)->lt($current_date))
                                    {   
                                        $updateData['cho_status'] = 1; // 0 = Assigned Chore / 1 = Finished
                                        $updateData['cho_is_complete'] = 0; // 0 = Complete 1 = Incompletes 2 = No any action
                                        //$updateData['cho_is_confirmation'] = 0;  // 0 = Not conform 1 = Conform
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
          
                            ResponseMessage::successMessage("Chore created successfully.");
                        }
                        }else{
                            ResponseMessage::successMessage("Please select almost one child!");
                        }
                    }else
                    {
                        ResponseMessage::successMessage("Something is missing!");
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

    public function notification($token, $choreName,$content,$type,$use_username,$notificationId,$notificationCount)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $token = $token;

        $notification = array(
            'body' => $choreName,
            'title' => $choreName. ' '.$content.' '. $use_username.' - Family Days',
            'sound' => "default",
            'color' => "#203E78",
            'type' => $type,
            'notification_id' => $notificationId,
            'badge' => $notificationCount
        );

        $fcmNotification = array(
            'registration_ids' => array($token),
            'priority' => 'high',
            'aps'=>array('alert'=>array('title'=>'test','body'=>'body'), 'content-available'=>1,'mutable_content' =>1),
            'type' => $type,
            'badge' => $notificationCount,

            'headers' => array( 'apns-priority' => '10'),
            'content_available' => true,
            'notification'=> $notification,
            'data' => array(
                "date" => date('d-m-Y H:i:s'),
                "message" => $choreName,
                "type" => $type,
                'vibrate' => 1,
                'sound' => 1,
                'notification_id' => $notificationId,
                'badge' => $notificationCount
            )
        );

        $fcmNotification = json_encode ( $fcmNotification );

        $headers = array (
              'Authorization: key=' . "AAAAOdYNOvc:APA91bExCJkdjntcgr7u1Vztw6sRphQn4kzGMYFiTLTEknzkgrlVanwi8Sacl4vqbOumof5tbvHWFhpc7kHrjUniwNzwnx640_7lnS2culjTlixZviCE2wr9EmMmm9-Aq7q_yciEK1J4",
              'Content-Type: application/json'
          );

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $fcmNotification );
        $result = curl_exec ( $ch );
        curl_close ( $ch );

        return true;
    }

    public function addChildChores(Request $request)
    {
        try
        {
            $header = $request->header('token');

            if($header)
            {
                if(User::where('use_token',$header)->exists())
                {   
                    if(User::where('use_token',$header)->where('use_status',0)->exists())
                    {
                        $userRecord = DB::table('users')->select('id','use_username','use_parents_id','use_fam_unique_id')->where('use_token',$header)->first();

                        $notification = NotificationKey::notificationType();

                        if($userRecord)
                        { 
                            if($request['chore_time'])
                            {
                                $choreTime = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request['chore_time'])));
                                $time = date('H:i:s', strtotime(str_replace('/', '-', $request['chore_time'])));
                                $choreDate = date('Y-m-d', strtotime(str_replace('/', '-', $request['chore_time'])));
                            }else{
                                $choreTime = date('Y-m-d H:i:s');
                                $time = date('H:i:s');
                                $choreDate = date('Y-m-d');
                            }

                            $todayDate = date('Y-m-d');

                            if($request['is_daily_chore'] == 1)
                            {
                                if(strtotime($todayDate) > strtotime($choreDate))
                                {   //code to validate start and end date
                                    return ResponseMessage::error("Daily chores date should be greater than Today date!");
                                }else{
                                    $period = CarbonPeriod::create($todayDate, $choreDate);

                                    $uniqueId = date("YmdHis");

                                    $i = 0; 
                                    $l = 0; 
                                    foreach ($period as $dateDaily)
                                    {
                                        $l++;
                                        $j = $i++;
                                        $k = $j+1;
                                        $insertData = new Chores;
                                        $insertData->cho_unique_id = $k.$uniqueId;
                                        $insertData->cho_family_id =  $userRecord->use_fam_unique_id;
                                        $insertData->cho_icon = $request['icon_name'];
                                        $insertData->cho_title = $request['title'];
                                        $insertData->cho_is_daily = 1; // 0 = No Daily 1 = Daily
                                        $insertData->cho_daily_chores = "daily_chores";
                                        $insertData->cho_point = $request['set_point'] ? $request['set_point']:10;
                                        $insertData->cho_set_time = $dateDaily->format('Y-m-d').' ' .$time;
                                        $insertData->cho_date = $dateDaily->format('Y-m-d');
                                        $insertData->cho_status = 0; // 0 = Assigned Chore / 1 = Finished
                                        $insertData->cho_is_complete = 0; // 0 = No Daily 1 = Daily
                                        $insertData->cho_is_confirmation = 0;  // 0 = Not conform 1 = Conform
                                        $insertData->cho_is_createby = 1;  // 0 = Parents Create , 1 = Child Create Chore
                                        $insertData->cho_is_admin_complete = 2; //0 = Complete 1 = Incompletes 2 = No any action  
                                        $insertData->cho_child_id = $userRecord->id;
                                        $insertData->cho_createby = $userRecord->use_parents_id; //0 = Parents 1 = Child create by
                                        $insertData->cho_is_expired = "";
                                        $insertData->cho_createat = $dateDaily->format('Y-m-d H:i:s');
                                        $insertData->cho_updateat = $dateDaily->format('Y-m-d H:i:s');
                                        $insertData->save();

                                        $insertStatus = new StatusNotification;
                                        $insertStatus->sno_chores_id = $insertData->cho_id;
                                        $insertStatus->sno_is_twenty_four = 0;
                                        $insertStatus->sno_is_twelve = 0;
                                        $insertStatus->sno_is_six = 0;
                                        $insertStatus->sno_is_three = 0;
                                        $insertStatus->sno_is_one = 0;
                                        $insertStatus->sno_createat = date('Y-m-d H:i:s');
                                        $insertStatus->sno_updateat = date('Y-m-d H:i:s');
                                        $insertStatus->save();

                                        if($l <= 1)
                                        {
                                            $notificationId = 0;
                                            $getToken = User::select('use_fcm_token','id as user_id','use_username')->where('use_fam_unique_id',$userRecord->use_fam_unique_id)->whereIn('use_role',[2,3])->get();
                                           
                                                foreach ($getToken as $key => $tokenValue) {
                                                $insertMessage = new Notification;
                                                $insertMessage->not_type = $notification['choreByChildType'];
                                                $insertMessage->not_content = $notification['choreByChildContent'];
                                                $insertMessage->not_sender_id = $userRecord->id;
                                                $insertMessage->not_received_id = $tokenValue->user_id;
                                                $insertMessage->not_chores_id = $insertData->cho_id;
                                                $insertMessage->not_reward_id = '';
                                                $insertMessage->not_claim_id = '';
                                                $insertMessage->not_message_id = '';
                                                $insertMessage->not_data = $request->title ? $request->title:'';
                                                $insertMessage->not_child_name = $userRecord->use_username;
                                                $insertMessage->not_is_read = 0; // 0 = Not read 1 = read
                                                $insertMessage->not_read_at = date('Y-m-d H:i:s');
                                                $insertMessage->not_createdat = date('Y-m-d H:i:s');
                                                $insertMessage->not_updatedat = date('Y-m-d H:i:s');
                                                $insertMessage->save();

                                                $notificationId = $insertMessage->not_id;

                                                $notificationCount = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')->where('not_received_id',$tokenValue->user_id)->where('not_new_notification',0)->count('not_new_notification');

                                                $this->notification($tokenValue['use_fcm_token'],$request->title,$notification['choreByChildContent'],$notification['choreByChildType'],$userRecord->use_username,$notificationId,$notificationId);
                                            }
                                        }
                                    }
                                }
                                // ------------- End Notification --------------------  
                                
                                $msg = "Chore created successfully.";
                                return json_encode(['status' => true, 'error' => 200, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                            }else
                            {
                                $uniqueId = date("YmdHis");      
                                $insertData = new Chores;
                                $insertData->cho_unique_id = $uniqueId;
                                $insertData->cho_family_id = $userRecord->use_fam_unique_id;
                                $insertData->cho_icon = $request['icon_name'];
                                $insertData->cho_title = $request['title'];
                                $insertData->cho_is_daily = $request['is_daily_chore'];
                                $insertData->cho_point = $request['set_point'];

                                if($request['is_daily_chore'] == 1)
                                {
                                    $insertData->cho_daily_chores = "daily_chores";
                                }else if($request['is_daily_chore'] == 0)
                                {
                                    $insertData->cho_daily_chores = "";
                                }
                                $insertData->cho_set_time = $choreTime;
                                $insertData->cho_date = $choreDate;
                                $insertData->cho_set_time = $choreTime;
                                $insertData->cho_status = 0;
                                $insertData->cho_is_complete = 0;
                                $insertData->cho_is_admin_complete = 2;
                                $insertData->cho_is_createby = 1;
                                $insertData->cho_is_confirmation = 0;  // 0 = Not conform 1 = Conform
                                $insertData->cho_child_id = $userRecord->id;
                                $insertData->cho_createby = $userRecord->use_parents_id;
                                $insertData->cho_is_expired = "";
                                $insertData->cho_createat = date('Y-m-d H:i:s');
                                $insertData->cho_updateat = date('Y-m-d H:i:s');
                                $insertData->save();

                                $insertStatus = new StatusNotification;
                                $insertStatus->sno_chores_id = $insertData->cho_id;
                                $insertStatus->sno_is_twenty_four = 0;
                                $insertStatus->sno_is_twelve = 0;
                                $insertStatus->sno_is_six = 0;
                                $insertStatus->sno_is_three = 0;
                                $insertStatus->sno_is_one = 0;
                                $insertStatus->sno_createat = date('Y-m-d H:i:s');
                                $insertStatus->sno_updateat = date('Y-m-d H:i:s');
                                $insertStatus->save();

                                // ------------- Start Notification --------------------  
                                $arrayToken = array();
                                if($userRecord)
                                {
                                    $notificationId = 0;
                                    $getToken = User::select('use_fcm_token','id as user_id')->where('use_fam_unique_id',$userRecord->use_fam_unique_id)->whereIn('use_role',[2,3])->get();
                                   
                                        foreach ($getToken as $key => $tokenValue) {
                                            $insertMessage = new Notification;
                                            $insertMessage->not_type = $notification['choreByChildType'];
                                            $insertMessage->not_content = $notification['choreByChildContent'];
                                            $insertMessage->not_sender_id = $userRecord->id;
                                            $insertMessage->not_received_id = $tokenValue->user_id;
                                            $insertMessage->not_chores_id = $insertData->cho_id;
                                            $insertMessage->not_reward_id = '';
                                            $insertMessage->not_claim_id = '';
                                            $insertMessage->not_message_id = '';
                                            $insertMessage->not_child_name = $userRecord->use_username;
                                            $insertMessage->not_data = $request->title ? $request->title:'';
                                            $insertMessage->not_is_read = 0; // 0 = Not read 1 = read
                                            $insertMessage->not_read_at = date('Y-m-d H:i:s');
                                            $insertMessage->not_createdat = date('Y-m-d H:i:s');
                                            $insertMessage->not_updatedat = date('Y-m-d H:i:s');
                                            $insertMessage->save();

                                        $notificationId = $insertMessage->not_id;

                                        $notificationCount = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')->where('not_received_id',$tokenValue->user_id)->where('not_new_notification',0)->count('not_new_notification');

                                        $this->notification($tokenValue['use_fcm_token'],$request->title,$notification['choreByChildContent'],$notification['choreByChildType'],$userRecord->use_username,$notificationId,$notificationCount);
                                    }
                                }

                                // ------------- End Notification --------------------  
                                
                                $msg = "Chore created successfully.";
                                return json_encode(['status' => true, 'error' => 200, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                            }
                            
                        }else
                        {
                            $msg = "Something is missing";
                           return json_encode(['status' => true, 'error' => 200, 'message' => $msg],JSON_UNESCAPED_SLASHES);
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
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function editChores(Request $request)
    {
        try
        {
            $choreId = $request->chore_id;

            $notificationId = $request->notification_id;

            if($notificationId)
            {
                $updataData['not_is_read'] = 1;
                $update = Notification::where('not_id',$notificationId)->update($updataData);
            }

            if($choreId)
            { 
                if(Chores::where('cho_id',$choreId)->exists())
                {
                    $adminChores = Chores::select('cho_id','cho_title','cho_point','cho_icon','use_full_name','cho_createby','cho_is_daily','cho_is_complete','cho_is_confirmation','use_is_admin','use_token','cho_set_time')->leftjoin('users','tbl_chores_list.cho_createby','users.id')->where('cho_id',$choreId)->first();

                    if($adminChores->cho_icon)
                    {
                        $profileurl = url("public/images/chore-icon/".$adminChores->cho_icon);
                    }else{
                        $profileurl = url("public/images/chore-icon/other-icon.png");
                    }

                    $minmaxPoint = SystemSetting::where('sys_id',1)->first();

                    $choreDetails = array("chore_id" => $adminChores->cho_id,"title" => $adminChores->cho_title,"set_time" => $adminChores->cho_set_time,"is_daily" => $adminChores->cho_is_daily,"is_complete" => $adminChores->cho_is_complete,"is_confirmation" => $adminChores->cho_is_confirmation,"point" => $adminChores->cho_point,"minimum_point" => $minmaxPoint->sys_min_chores,"maximum_point" => $minmaxPoint->sys_max_chores,"icon_name" => $adminChores->cho_icon,"icon" => $profileurl);
                    $message = "Chores details";
                    return json_encode(['status' => true, 'error' => 200, 'message' => $message, 'data'=> $choreDetails],JSON_UNESCAPED_SLASHES);
                }else{
                   $msg = "Chore id isn't valid.";
                    return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => array()],JSON_UNESCAPED_SLASHES); 
                }
            }else{
              $msg = "Chore id is required.";
              return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => array()],JSON_UNESCAPED_SLASHES);
            }
                    
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function updateChores(Request $request)
    {
        try
        {
            $choreId = $request->chore_id;
            $header = $request->header('token');
            $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
            $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));

            if($choreId)
            {
                if(Chores::where('cho_id',$choreId)->exists())
                {
                    $userRecord = DB::table('users')->select('id','use_is_admin','use_role')->where('use_token',$header)->first();

                    $choreDetails = Chores::select('cho_is_createby')->where('cho_id',$choreId)->first();

                    if($userRecord->use_role == 4 || $userRecord->use_role == 5)
                    {
                        if($choreDetails->cho_is_createby == 0)
                        {   
                            $updateData['cho_icon'] = $request['icon_name'];
                            $updateData['cho_title'] = $request['title'];
                            $updateData['cho_is_daily'] = $request['is_daily_chore'];
                            $updateData['cho_point'] = $request['set_point'];
                            $updateData['cho_is_complete'] = $request['is_complete'];
                            
                            if($request['is_complete'] == 1)
                            {
                                $updateData['cho_status'] = 1;
                                $updateData['cho_is_expired'] = "Completed";
                                $updateData['cho_is_confirmation'] = $request['is_confirmation']; // Admin Conform
                                $updateData['cho_is_complete_date'] = $current_date;
                                $updateData['cho_date'] = $currentDate;
                                $updateData['cho_is_admin_complete'] = 2;

                            }else if($request['is_complete'] == 0)
                            {
                                if($request['is_confirmation'] == 1)
                                {
                                    $updateData['cho_is_confirmation'] = 0;
                                }else{
                                    $updateData['cho_is_confirmation'] = $request['is_confirmation'];
                                }
                                $updateData['cho_status'] = 0;
                                $updateData['cho_is_complete_date'] = null;
                                $updateData['cho_is_expired'] = "";
                                $updateData['cho_is_admin_complete'] = 2;
                            }
                        }else{
                            $updateData['cho_icon'] = $request['icon_name'];
                            $updateData['cho_title'] = $request['title'];
                            $updateData['cho_is_daily'] = $request['is_daily_chore'];
                            $updateData['cho_point'] = $request['set_point'];
                            $updateData['cho_is_complete'] = $request['is_complete'];
                            $updateData['cho_is_confirmation'] = $request['is_confirmation'];
                            $updateData['cho_is_admin_complete'] = 2;
                            if($request['is_complete'] == 1)
                            {
                                $updateData['cho_status'] = 1;
                                $updateData['cho_is_expired'] = "Completed";
                                $updateData['cho_is_complete_date'] = $current_date;
                                $updateData['cho_date'] = $currentDate;
                            }else if($request['is_complete'] == 0)
                            {
                                $updateData['cho_status'] = 0;
                                $updateData['cho_is_complete_date'] = null;
                                $updateData['cho_is_expired'] = "";
                            }
                        }
                    }
                    if($userRecord->use_role == 2 || $userRecord->use_role == 3)
                    {
                        if($request['is_complete'] == 1)
                        {   
                            $updateData['cho_icon'] = $request['icon_name'];
                            $updateData['cho_title'] = $request['title'];
                            $updateData['cho_is_daily'] = $request['is_daily_chore'];
                            if($request['is_daily_chore'] == 1)
                            {
                                $updateData['cho_daily_chores'] = "daily_chores";
                            }else if($request['is_daily_chore'] == 0)
                            {
                                $updateData['cho_daily_chores'] = "";
                            }
                            $updateData['cho_point'] = $request['set_point'];
                            $updateData['cho_status'] = 1;
                            $updateData['cho_is_complete'] = $request['is_complete'];
                            $updateData['cho_is_confirmation'] = 1;
                            $updateData['cho_is_admin_complete'] = 0;
                            $updateData['cho_is_expired'] = "Completed";
                            $updateData['cho_is_complete_date'] = $current_date;
                            $updateData['cho_date'] = $currentDate;

                        }else
                        {
                            $updateData['cho_icon'] = $request['icon_name'];
                            $updateData['cho_title'] = $request['title'];
                            $updateData['cho_is_daily'] = $request['is_daily_chore'];
                            if($request['is_daily_chore'] == 1)
                            {
                                $updateData['cho_daily_chores'] = "daily_chores";
                            }else if($request['is_daily_chore'] == 0)
                            {
                                $updateData['cho_daily_chores'] = "";
                            }
                            $updateData['cho_point'] = $request['set_point'];
                            $updateData['cho_is_admin_complete'] = 0;
                            $updateData['cho_is_complete'] = $request['is_complete'];
                            $updateData['cho_status'] = 0;
                            $updateData['cho_is_confirmation'] = 1;
                            $updateData['cho_is_expired'] = "";
                            $updateData['cho_is_complete_date'] = null;
                        }
                    }
                    if($request['chore_time'])
                    {
                        $updateData['cho_set_time'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $request['chore_time'])));
                        $updateData['cho_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $request['chore_time'])));
                    }

                    $update = Chores::where('cho_id',$choreId)->update($updateData);

                    if($userRecord->use_role == 2 || $userRecord->use_role == 3)
                    {
                        if($request['is_complete'] == 1)
                        { 
                            $choresDetails = Chores::where('cho_id',$choreId)->first();

                            $useDetails = User::select('use_total_point')->where('id',$choresDetails->cho_child_id)->first();

                            if($useDetails)
                            {
                                $updateDatas['use_total_point'] = $useDetails->use_total_point + $choresDetails->cho_point;
                                $updatePoint = User::where('id',$choresDetails->cho_child_id)->update($updateDatas);
                            }
                            
                        }
                    }

                    if($request['is_complete'] == 1)
                    {  
                        $message = "Chore completed successfully.";
                      }else{

                        if($request['is_daily_chore'] == 1)
                        {
                            $message = "Today's daily chore updated successfully.";
                        }else{
                            $message = "Chore updated successfully.";
                        }   
                    }
                    return json_encode(['status' => true, 'error' => 200, 'message' => $message],JSON_UNESCAPED_SLASHES);
                }
                else{
                  $msg = "Chore id is'nt valid.";
                  return json_encode(['status' => true, 'error' => 200, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{
              $msg = "Chore id is required.";
              return json_encode(['status' => true, 'error' => 200, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }

        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function iscompleteChores(Request $request)
    {
        try
        {
            $choreId = explode(',', $request->chore_id);
            $header = $request->header('token');
            $current_time = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
            $current_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));

            if($choreId && $header)
            { 
                  
                $userdetails = User::select('use_is_admin','use_role')->where('use_token',$header)->first();

                if($userdetails){
                
                    if($userdetails->use_is_admin == 1 || $userdetails->use_role == 2 || $userdetails->use_role == 3)
                    {
                        if($request['is_complete'] == 1)
                        {   
                            foreach ($choreId as $key => $value) 
                            {
                                if(Chores::where('cho_id',$value)->exists())
                                {
                                    $updateData['cho_is_admin_complete'] = 1;
                                    $updateData['cho_is_complete'] = $request['is_complete'];
                                    $updateData['cho_status'] = 0;
                                    $updateData['cho_is_expired'] = "";
                                    $updateData['cho_is_complete_date'] = $current_time;
                                    $updateData['cho_date'] = $current_date;
                                    $update = Chores::where('cho_id',$value)->update($updateData);
                                }
                            }
                            if(count($choreId) == 1){
                                $message = "Chore marked incomplete";
                            }else{
                                $message = "Chores marked incomplete";
                            }
                            return json_encode(['status' => true, 'error' => 200, 'message' => $message,'is_complete' => $request['is_complete']],JSON_UNESCAPED_SLASHES);
                        }else if($request['is_complete'] == 0)
                        {
                            foreach ($choreId as $key => $value) 
                            {
                                if(Chores::where('cho_id',$value)->exists())
                                {
                                    $updateData['cho_is_admin_complete'] = 0;
                                    $updateData['cho_status'] = 1;
                                    $updateData['cho_is_expired'] = "Completed";
                                    $updateData['cho_is_complete_date'] = $current_time;
                                    $updateData['cho_date'] = $current_date;
                                    $update = Chores::where('cho_id',$value)->update($updateData);

                                    $choresDetails = Chores::where('cho_id',$value)->first();
                                    $useDetails = User::select('use_total_point')->where('id',$choresDetails->cho_child_id)->first();
                                    if($useDetails){
                                        $updateDatad['use_total_point'] = $useDetails->use_total_point + $choresDetails->cho_point;
                                        $updatedd = User::where('id',$choresDetails->cho_child_id)->update($updateDatad);
                                    }
                                }
                            }

                            if(count($choreId) == 1){
                                $message = "Chore marked completed";
                            }else{
                                $message = "Chores marked completed";
                            }
                            return json_encode(['status' => true, 'error' => 200, 'message' => $message,'is_complete' => $request['is_complete']],JSON_UNESCAPED_SLASHES);
                        }
                    }else{
                        if($request['is_complete'] == 1)
                        {   
                            foreach ($choreId as $key => $value) 
                            {
                                if(Chores::where('cho_id',$value)->exists())
                                {
                                    $updateData['cho_is_admin_complete'] = 1;
                                    $updateData['cho_is_complete'] = $request['is_complete'];
                                    $updateData['cho_status'] = 0;
                                    $updateData['cho_is_expired'] = "InCompleted";
                                    $updateData['cho_is_complete_date'] = $current_time;
                                    $updateData['cho_date'] = $current_date;
                                    $update = Chores::where('cho_id',$value)->update($updateData);
                                }
                            }

                            if(count($choreId) == 1){
                                $message = "Chore marked incomplete";
                            }else{
                                $message = "Chores marked incomplete";
                            }
                            return json_encode(['status' => true, 'error' => 200, 'message' => $message,'is_complete' => $request['is_complete']],JSON_UNESCAPED_SLASHES);
                        }else if($request['is_complete'] == 0){
                            foreach ($choreId as $key => $value) 
                            {
                                if(Chores::where('cho_id',$value)->exists())
                                {
                                    $updateData['cho_is_admin_complete'] = 0;
                                    $updateData['cho_is_complete'] = $request['is_complete'];
                                    $updateData['cho_status'] = 1;
                                    $updateData['cho_is_expired'] = "Completed";
                                    $updateData['cho_is_complete_date'] = $current_time;
                                    $updateData['cho_date'] = $current_date;
                                    $update = Chores::where('cho_id',$value)->update($updateData);

                                    $choresDetails = Chores::where('cho_id',$value)->first();

                                    $useDetails = User::select('use_total_point')->where('id',$choresDetails->cho_child_id)->first();
                                    if($useDetails){
                                        $updateDatas['use_total_point'] = $useDetails->use_total_point + $choresDetails->cho_point;
                                        $update = User::where('id',$choresDetails->cho_child_id)->update($updateDatas);
                                    }
                                }
                            }
                           
                            if(count($choreId) == 1){
                                $message = "Chore marked completed";
                            }else{
                                $message = "Chores marked completed";
                            }
                            return json_encode(['status' => true, 'error' => 200, 'message' => $message,'is_complete' => $request['is_complete']],JSON_UNESCAPED_SLASHES);
                        }
                    }
                }else{
                    $msg = "Your account isn't active.";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{

              $msg = "Chores id & token are required.";
              return json_encode(['status' => true, 'error' => 200, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }
                    
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

     public function approvedChores(Request $request)
    {
        try
        {
            $choreId = explode(',', $request->chore_id);
            $header = $request->header('token');

            if($choreId && $header)
            { 
                foreach ($choreId as $key => $value) 
                {
                    $updateData['cho_is_confirmation'] = 1;
                    $update = Chores::where('cho_id',$value)->update($updateData);
                    
                }
                if(count($choreId) == 1){
                    $message = "Chore is approved";
                }else{
                    $message = "Chores are approved";
                }
                return json_encode(['status' => true, 'error' => 200, 'message' => $message],JSON_UNESCAPED_SLASHES);
            }else{
              $msg = "Chores id & token are required.";
              return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }
                    
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function deleteChore(Request $request)
    { 
        $rules = [
            'chore_id' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {                
                return json_encode(['status' => false, 'error' => 401, 'message' => $message],JSON_UNESCAPED_SLASHES);
            }
        }

        $choreId = explode(',',$request->chore_id);

        if($choreId)
        {
            foreach ($choreId as $key => $value) 
            {
                Chores::where('cho_id',$value)->delete();
            }

            if(count($choreId) == 1){
                ResponseMessage::successMessage("Chore is deleted");
            }else{
                ResponseMessage::successMessage("Chores are deleted");
            }
        }else{
            ResponseMessage::error("Chore id is required!");
        }
    }
}