<?php

namespace App\Http\Controllers\RestApi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Helper\ResponseMessage;
use App\Helper\NotificationKey;
use App\Helper\Exceptions;
use App\Helper\UserMaster;
use App\Model\RewardsCategorys;
use App\Model\SystemSetting;
use App\Model\Notification;
use App\Model\Brands;
use App\Model\PresetReward;
use App\Model\SubBrands;
use App\Model\Rewards;
use Carbon\Carbon;
use Validator;
use App\User;
use DB;


class RewardsController extends Controller
{
    public function rewardCategoryList()
    {
        try
        {
            $rewardCategory = RewardsCategorys::select('rec_id','rec_cat_name','rec_icon')->where('rec_icon',0)->orderby('rec_id','DESC')->get();

            if(!$rewardCategory->isEmpty())
            { 
                $rewardCategoryDetails = array();
                foreach ($rewardCategory as $key => $value)
                { 
                    $iconurl = url("public/images/reward-icon/".$value->rec_icon);
                    $rewardCategoryDetails[] = array("cat_id" => $value->rec_id,"title" => $value->rec_cat_name,"icon_name" => $value->rec_icon,"icon" => $iconurl);
                }
            }else{
                $rewardCategoryDetails = array();
            }
            $minmaxPoint = SystemSetting::where('sys_id',1)->first();

            $message = "Category list";
            return json_encode(['status' => true, 'error' => 200, 'message' => $message, 'minimum_point'=> $minmaxPoint->sys_min_reward,'maximum_point'=> $minmaxPoint->sys_max_reward,'data'=> $rewardCategoryDetails],JSON_UNESCAPED_SLASHES);
                    
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function brandList(Request $request)
    {
        try
        {
            $cateroryId = $request->caterory_id;

            if($cateroryId)
            {
                $brands = Brands::select('brd_id','brd_brand_name','brd_cat_id')->where('brd_cat_id',$cateroryId)->where('brd_status',0)->orderby('brd_brand_name','ASC')->get();
                 $subBrandsDetails = array();
                if(!$brands->isEmpty())
                { 
                    $brandsDetails = array();
                    foreach ($brands as $key => $bvalue)
                    {   
                        $subBrands = SubBrands::select('bds_id','bds_brand_id','bds_brand_icon','bds_link')->where('bds_brand_id',$bvalue->brd_id)->where('bds_cat_id',$request->caterory_id)->where('bds_status',0)->orderby('bds_brand_id','ASC')->get();
                        $subBrandsDetails = array();
                        foreach ($subBrands as $key => $value)
                        { 
                            $iconurl = url("public/images/brand-icon/".$value->bds_brand_icon);
                            $subBrandsDetails[] = array("id" => $value->bds_id,"brand_id" => $value->bds_brand_id,"brand_icon_name" => $value->bds_brand_icon,"brand_icon" => $iconurl,"brand_url" => $value->bds_link ? $value->bds_link:'');
                        }
                        $brandsDetails[] = array("brand_id" => $bvalue->brd_id,"brand_name" => $bvalue->brd_brand_name,'sub_brands' => $subBrandsDetails);
                    }
                }else{
                    $brandsDetails = array();
                }
                $message = "Brands list";
                return json_encode(['status' => true, 'error' => 200, 'message' => $message,'data'=> $brandsDetails],JSON_UNESCAPED_SLASHES); 
            }else{
                $message = "Category id is required";
                 return json_encode(['status' => true, 'error' => 200, 'message' => $message],JSON_UNESCAPED_SLASHES); 
            }       
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function createReward(Request $request,$notificationId = 0)
    {
        $header = $request->header('token');
        $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
        $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {
                    $userRecord = DB::table('users')->select('id','use_role','use_is_admin','use_username','use_parents_id','use_fam_unique_id')->where('use_token',$header)->first();

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
                                'category_id' => 'required',
                                'brand_name' => 'required',
                                'brand_icon' => 'required',
                                'frame_date' => 'required',
                                'point' => 'required',
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
                            $uniqueId = "reward".date("YmdHis");
                            foreach ($childId as $key => $value) 
                            { 
                                if(User::where('id',$value)->where('use_role','>',3)->exists())
                                {
                                    $rewardCategory = RewardsCategorys::where('rec_id',$request->category_id)->first();
                                    $insertData = new Rewards;
                                    $insertData->red_unique_id = $uniqueId;
                                    $insertData->red_family_id = $userRecord->use_fam_unique_id;
                                    $insertData->red_cat_id = $request->category_id;
                                    $insertData->red_cat_name = $rewardCategory->rec_cat_name;
                                    $insertData->red_icon = $rewardCategory->rec_icon;
                                    $insertData->red_brand_name = $request['brand_name'];
                                    $insertData->red_brand_icon = $request['brand_icon'];
                                    $insertData->red_rewards_name = $request['rewards_name'] ? $request['rewards_name']:'';
                                    if($request['frame_date'])
                                    {
                                        $rewardDateTime = date('Y-m-d', strtotime(str_replace('/', '-', $request['frame_date'])));
                                        $rewardDate = date('Y-m-d', strtotime(str_replace('/', '-', $request['frame_date'])));
                                    }else{
                                        $rewardDateTime = $currentDate;
                                        $rewardDate = $currentDate;
                                    }
                                    $insertData->red_frame_date = $rewardDateTime;
                                    $insertData->red_create_date = $rewardDate;
                                    $insertData->red_point = $request['point'] ? $request['point']:10;
                                    $insertData->red_use_createby = $createbyId; // Parents Id
                                    if($userRecord->use_role == 2 || $userRecord->use_role == 3)
                                    {
                                        $createby = "P";
                                        $isConfirmation = 0;
                                    }else{
                                        $createby = "C";
                                        $isConfirmation = 1;
                                    }
                                    $insertData->red_createby = $createby;  // P = Parents , C = Child Create Reward
                                    $insertData->red_child_id = $value;
                                    $insertData->red_is_confirmation = $isConfirmation; //0 = Yes 1 = NO
                                    $insertData->red_is_expired = "";
                                    $insertData->red_expired_date = null;
                                    $insertData->red_is_claim = 0; // 0 = No 1 = Yes
                                    $insertData->red_is_claim_date = null;
                                    $insertData->red_status = 0; // 0 = Active / 1 = Inactive
                                    $insertData->red_createdby = $userRecord->id;
                                    $insertData->red_updatedby = $userRecord->id;
                                    $insertData->red_createat = date('Y-m-d H:i:s');
                                    $insertData->red_updateat = date('Y-m-d H:i:s');
                                    $insertData->save();
                                }
                            }

                            $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
                            $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));

                            $rewardExpiredRecords = Rewards::where('red_family_id',$userRecord->use_fam_unique_id)->where('red_status',0)->whereDate('red_frame_date', '<',$currentDate)->orderby('red_frame_date','ASC')->get();
                    
                            if(!$rewardExpiredRecords->isEmpty())
                            { 
                                foreach ($rewardExpiredRecords as $key => $expvalue)
                                {
                                    if (Carbon::parse($expvalue->red_frame_date)->lt($currentDate))
                                    {   
                                        $updateData['red_status'] = 1; // 0 = Active / 1 = Inactive
                                        $updateData['red_is_confirmation'] = 1;  // 0 = Yes 1 = No
                                        $updateData['red_is_expired'] = "Expired"; //0 = Complete 1 = Incompletes 2 = No any action
                                        $updateData['red_expired_date'] = date('Y-m-d');
                                        $update = Rewards::where('red_id',$expvalue->red_id)->update($updateData);
                                    }
                                }
                            }

                            // ------------- Start Notification --------------------   

                            $childId = explode(',',$request->child_id);
                           
                            $arrayToken = array();
                            
                            if($childId)
                            {
                                $rewardDetails = Rewards::select('red_id','red_rewards_name')->orderby('red_id','DESC')->first();

                                $familyDetails = DB::table('users')->select('id as user_id','use_role','use_is_admin','use_username','use_parents_id','use_fam_unique_id','use_fcm_token')->where('use_fam_unique_id',$userRecord->use_fam_unique_id)->get();

                                $arrayToken = array();

                                    foreach ($childId as $key => $tokenValue) {
                                        $getToken = User::select('use_username','use_fcm_token')->where('id',$tokenValue)->first();

                                        foreach ($familyDetails as $key => $value) 
                                        {
                                            if($value->user_id != $userRecord->id)
                                            {
                                                $insertMessage = new Notification;
                                                $insertMessage->not_child_name = $getToken->use_username;
                                                $insertMessage->not_type = $notification['rewardByParentType'];
                                                $insertMessage->not_content = $notification['rewardByParentContent'];
                                                $insertMessage->not_sender_id = $userRecord->id;
                                                $insertMessage->not_received_id = $value->user_id;
                                                $insertMessage->not_chores_id = '';
                                                $insertMessage->not_reward_id = $rewardDetails['red_id'];
                                                $insertMessage->not_claim_id = '';
                                                $insertMessage->not_message_id = '';
                                                $insertMessage->not_data = $rewardDetails['red_rewards_name'];
                                                $insertMessage->not_is_read = 0; // 0 = Not read 1 = read
                                                $insertMessage->not_read_at = date('Y-m-d H:i:s');
                                                $insertMessage->not_createdat = date('Y-m-d H:i:s');
                                                $insertMessage->not_updatedat = date('Y-m-d H:i:s');
                                                $insertMessage->save();

                                                $notificationId = $insertMessage->not_id;

                                                if($getToken)
                                                {

                                                    $notificationCount = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')->where('not_received_id',$value->user_id)->where('not_new_notification',0)->count('not_new_notification');

                                                    $this->notification($value->use_fcm_token,$rewardDetails['red_rewards_name'],$notification['rewardByParentContent'],$notification['rewardByParentType'],$getToken['use_username'],$notificationId,$notificationCount);
                                                }
                                            }
                                        }
                                    }
                                }
                             // ------------- End Notification --------------------             

                            $msg = "Reward is created";
                            return json_encode(['status' => true, 'error' => 200, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                        }
                        }else{
                             $msg = "Please select almost one child!";
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
    }

    public function notification($token, $rewardName,$rewardByChildContent,$rewardByChildType,$use_username,$notificationId,$notificationCount)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $token = $token;

        $notification = array(
            'body' => $rewardName,
            'title' => $rewardName. ' '.$rewardByChildContent.' '. $use_username.' - Family Days',
            'sound' => "default",
            'color' => "#203E78",
            'type' => $rewardByChildType,
            'notification_id' => $notificationId,
            'badge' => $notificationCount
        );

        $fcmNotification = array(
            'registration_ids' => array($token),
            'priority' => 'high',
            'aps'=>array('alert'=>array('title'=>'test','body'=>'body'), 'content-available'=>1,'mutable_content' =>1),
            'type' => $rewardByChildType,
            'badge' => $notificationCount,

            'headers' => array( 'apns-priority' => '10'),
            'content_available' => true,
            'notification'=> $notification,
            'data' => array(
                'date' => date('d-m-Y H:i:s'),
                'message' => $rewardName,
                'type' => $rewardByChildType,
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

    public function rewardList(Request $request)
    {
        $header = $request->header('token');
        $loadMore = $request->load_more;
        $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
        $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));
        $currentYears = date('Y', strtotime(str_replace('/', '-',$request->date_time)));
        $notificationId = $request->notification_id;

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {
                    $updataData['not_is_read'] = 1;
                    $update = Notification::where('not_id',$notificationId)->update($updataData);

                    $userTokens = DB::table('users')->select('id','use_full_name','use_token','use_is_admin')->where('use_token',$header)->where('use_status',0)->first();

                    $userRecord = DB::table('users')->select('id','use_fam_unique_id','use_is_admin','use_parents_id','use_role','use_is_reset')->where('use_token',$header)->first();

                    $lastYears = date("Y") - 4;
                    $lastfiveYear = date('Y-m-d', strtotime(str_replace('/', '-',$lastYears."-01-01")));

                    $lastYears = $currentYears - 4;
                    $lastfiveYear = $lastYears."-01-01";
                    $futureYears = $currentYears + 4;
                    $futureYearDate = $futureYears."-12-31";

                    $rewardQuery = Rewards::join('users','tbl_rewards.red_child_id','=','users.id')->where('red_family_id',$userRecord->use_fam_unique_id)->whereBetween('red_frame_date', [$lastfiveYear, $futureYearDate])->where('red_status',0)->orderby('red_frame_date','ASC');

                    if($loadMore == 1)
                    {
                        $rewardRecords = $rewardQuery->orderby('red_frame_date','ASC')->get()->splice(6);
                    }else {
                        $rewardRecords = $rewardQuery->limit(6)->get();
                    }
                   
                    $rewardExpiredRecords = Rewards::where('red_family_id',$userRecord->use_fam_unique_id)->where('red_status',0)->whereDate('red_frame_date', '<',$currentDate)->orderby('red_frame_date','ASC')->get();

                    
                    if(!$rewardExpiredRecords->isEmpty())
                    { 
                        foreach ($rewardExpiredRecords as $key => $expvalue)
                        {
                            if (Carbon::parse($expvalue->red_frame_date)->lt($currentDate))
                            {   
                                $updateData['red_status'] = 1; // 0 = Active / 1 = Inactive
                                $updateData['red_is_confirmation'] = 1;  // 0 = Yes 1 = No
                                $updateData['red_is_expired'] = "Expired"; //0 = Complete 1 = Incompletes 2 = No any action
                                $updateData['red_expired_date'] = date('Y-m-d');
                                $update = Rewards::where('red_id',$expvalue->red_id)->update($updateData);
                            }
                        }
                    }

                    if(!$rewardRecords->isEmpty())
                    { 

                        $rewardsDetails = array();
                        foreach ($rewardRecords as $key => $value)
                        { 

                            $subBrands = SubBrands::select('bds_link')->where('bds_brand_icon',$value->red_brand_icon)->where('bds_status',0)->orderby('bds_brand_id','ASC')->first();

                            if($subBrands){
                                $brand_url = $subBrands->bds_link ? $subBrands->bds_link:'';
                            }else{
                                $brand_url = '';
                            }

                            if($value->red_icon)
                            {
                                $categoryiconUrl = url("public/images/reward-icon/".$value->red_icon);
                            }else{
                                $categoryiconUrl = url("public/images/reward-icon/reward-default-icon.png");
                            }

                            $dueDate = date('d-m-Y', strtotime(str_replace('/', '-', $value->red_frame_date)));
                            if($value->red_cat_id == 1)
                            {
                                $categoryName = $value->red_rewards_name;
                            }else{
                                $categoryName = $value->red_cat_name;
                            }

                            $rewardsDetails[] = array(
                                "reward_id" => $value->red_id,
                                "category" => $categoryName,
                                "category_icon" => $categoryiconUrl,                                    
                                "point" => $value->red_point,
                                "is_conform" => $value->red_is_confirmation,
                                "due_date" => $dueDate,
                                "token" => $value->use_token,
                                "brand_url" => $brand_url,
                                "create_by" => $value->use_full_name,
                                "is_admin" => $userTokens->use_is_admin,
                                "is_createby" => $value->red_createby,
                            );
                        }

                        array_walk_recursive($rewardsDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $rewardsDetails;
                        ResponseMessage::success("Reward details",$this->data[$key]);
                    }else
                    {
                        ResponseMessage::success("Reward details",array());
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

    public function isConformation(Request $request)
    {
        try
        {
            $rules = [
            'reward_id' => 'required',
            'is_conform' => 'required'
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
                $reward_id = explode(',', $request->reward_id);

                foreach ($reward_id as $key => $value) 
                {
                    if(Rewards::where('red_id',$value)->exists())
                    {
                        $updateData['red_is_confirmation'] = $request['is_conform'];
                        $infoUpdate = Rewards::where('red_id',$value)->update($updateData);

                        if($request->is_conform == 0)
                        {
                            $rewardDetails = Rewards::select('red_id','red_point','red_rewards_name','red_child_id')->where('red_id',$value)->first(); 

                            $child = User::select('use_parents_id')->where('id',$rewardDetails->red_child_id)->first();

                            if($child)
                            {
                                $parentsDetail = User::select('id','use_fcm_token','use_parents_id','use_username')->where('id',$child->use_parents_id)->first();
                                $parentId = $parentsDetail->id;
                                $parentUsername = $parentsDetail->use_username;
                            }else{
                                $parentId = 0;
                                $parentUsername = 'Parents';
                            }
                            
                            $childDetails = User::select('id as user_id','use_fcm_token','use_username')->where('id',$rewardDetails->red_child_id)->get();

                            if($childDetails)
                            {
                                foreach ($childDetails as $value) {

                                $rewardByChildContent =  "reward confirm by";
                                $rewardByChildType = "parents_confirm_reward";
                                $insertMessage = new Notification;
                                $insertMessage->not_child_name = $value->use_username;
                                $insertMessage->not_type = $rewardByChildType;
                                $insertMessage->not_content = $rewardByChildContent;
                                $insertMessage->not_sender_id = $parentId;
                                $insertMessage->not_received_id = $value->user_id;
                                $insertMessage->not_chores_id = '';
                                $insertMessage->not_reward_id = $rewardDetails['red_id'];
                                $insertMessage->not_claim_id = '';
                                $insertMessage->not_message_id = '';
                                $insertMessage->not_data = $rewardDetails['red_rewards_name'];
                                $insertMessage->not_is_read = 0; // 0 = Not read 1 = read
                                $insertMessage->not_read_at = date('Y-m-d H:i:s');
                                $insertMessage->not_createdat = date('Y-m-d H:i:s');
                                $insertMessage->not_updatedat = date('Y-m-d H:i:s');
                                $insertMessage->save();

                                $notificationCount = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')->where('not_received_id',$value->user_id)->where('not_new_notification',0)->count('not_new_notification');

                                $notificationId = $insertMessage->not_id;
                                $this->notiChildRewardEdit($value->use_fcm_token,$rewardDetails['red_rewards_name'],$rewardByChildContent,$rewardByChildType,$parentUsername,$notificationId,$notificationCount);
                                }
                            }
                        }
                    }
                }
                $message = "Reward activated successfully.";
                return json_encode(['status' => true, 'error' => 200, 'message' => $message],JSON_UNESCAPED_SLASHES);
            }
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function rewardDetails(Request $request)
    {
        $rewardId = $request->reward_id;
        $notificationId = $request->notification_id;

        if($notificationId)
        {
            $updataData['not_is_read'] = 1;
            $update = Notification::where('not_id',$notificationId)->update($updataData);
        }
        

        if($rewardId)
        {
            if(Rewards::where('red_id',$rewardId)->exists())
            {   
                $rewardRecords = Rewards::where('red_id',$rewardId)->orderby('red_frame_date','DESC')->first();

                if($rewardRecords->red_icon)
                {
                    $categoryiconUrl = url("public/images/reward-icon/".$rewardRecords->red_icon);
                }else{
                    $categoryiconUrl = url("public/images/reward-icon/reward-default-icon.png");
                }

                if($rewardRecords->red_brand_icon)
                {
                    $brandIconUrl = url("public/images/brand-icon/".$rewardRecords->red_brand_icon);
                }else{
                    $brandIconUrl = url("public/images/brand-icon/reward-default-icon.png");
                }

                $userDetails = DB::table('users')->select('id','use_full_name','use_token','use_is_admin')->where('id',$rewardRecords->red_child_id)->first();

                $dueDate = date('d-m-Y', strtotime(str_replace('/', '-', $rewardRecords->red_frame_date)));

                $minmaxPoint = SystemSetting::where('sys_id',1)->first();

                $rewardsDetails = array(
                    "reward_id" => $rewardRecords->red_id,
                    "category" => $rewardRecords->red_cat_name,
                    "category_icon_name" => $rewardRecords->red_icon,   
                    "category_icon" => $categoryiconUrl,  
                    "brand_name" => $rewardRecords->red_brand_name,
                    "brand_icon_name" => $rewardRecords->red_brand_icon,
                    "brand_icon" => $brandIconUrl,
                    "reward_name" => $rewardRecords->red_rewards_name,                    
                    "point" => $rewardRecords->red_point,
                    "is_conform" => $rewardRecords->red_is_confirmation,
                    "due_date" => $dueDate,
                    "token" => $userDetails->use_token,
                    "create_by" => $userDetails->use_full_name,
                    "is_createby" => $rewardRecords->red_createby,
                    "minimum_point" => $minmaxPoint->sys_min_reward,
                    "maximum_point" => $minmaxPoint->sys_max_reward,
                );

                $msg = "Reward details";
                return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => $rewardsDetails],JSON_UNESCAPED_SLASHES);
               
            }else{
                $msg = "Reward Id isn't valid!";
                return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }
        }else{
            $msg = "Reward Id is required!";
            return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
        }
    }

    public function updateReward(Request $request)
    {
        $header = $request->header('token');

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {
                    $userRecord = DB::table('users')->select('id','use_username','use_role','use_is_admin','use_parents_id','use_fam_unique_id')->where('use_token',$header)->first();

                    if($userRecord)
                    {                        
                          
                        $rules = [
                            'reward_id' => 'required',
                            'category_id' => 'required',
                            'brand_name' => 'required',
                            'brand_icon' => 'required',
                            'frame_date' => 'required',
                            'point' => 'required',
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
                            $rewardCategory = RewardsCategorys::where('rec_id',$request->category_id)->first();
                           
                            $updateData['red_cat_id'] = $request->category_id;
                            $updateData['red_cat_name'] = $request->category;
                            $updateData['red_icon'] = $request->category_icon;
                            $updateData['red_brand_name'] = $request['brand_name'];
                            $updateData['red_brand_icon'] = $request['brand_icon'];
                            $updateData['red_rewards_name'] = $request['rewards_name'] ? $request['rewards_name']:'';
                            if($request['frame_date'])
                            {
                                $rewardDateTime = date('Y-m-d', strtotime(str_replace('/', '-', $request['frame_date'])));
                                $rewardDate = date('Y-m-d', strtotime(str_replace('/', '-', $request['frame_date'])));
                            }else{
                                $rewardDateTime = date('Y-m-d');
                                $rewardDate = date('Y-m-d');
                            }
                            $updateData['red_frame_date'] = $rewardDateTime;
                            $updateData['red_create_date'] = $rewardDate;
                            $updateData['red_point'] = $request['point'] ? $request['point']:10;
                            if($userRecord->use_role == 2 || $userRecord->use_role == 3)
                            {
                                $isConfirmation = 0;
                            }else{
                                $isConfirmation = 1;
                            }
                           
                            $updateData['red_is_confirmation'] = $isConfirmation; //0 = Yes 1 = NO
                            $updateData['red_updateat'] = date('Y-m-d H:i:s');
                            $rewardUpdate = Rewards::where('red_id',$request->reward_id)->update($updateData);

                            if($rewardUpdate)
                            {
                                if($userRecord->use_role == 2 || $userRecord->use_role == 3)
                                {
                                    $isConfirmation = 0;

                                    $rewardDetails = Rewards::select('red_id','red_point','red_rewards_name','red_child_id')->where('red_id',$request->reward_id)->first(); 

                                    $parentsDetails = User::select('id as user_id','use_fcm_token')->where('id',$rewardDetails->red_child_id)->get();

                                    foreach ($parentsDetails as $value) {

                                        $rewardByChildType = "reward_create_by_child";
                                        $rewardByChildContent =  "reward updated by";
                                        $insertMessage = new Notification;
                                        $insertMessage->not_child_name = $userRecord->use_username;
                                        $insertMessage->not_type = $rewardByChildType;
                                        $insertMessage->not_content =$rewardByChildContent;
                                        $insertMessage->not_sender_id = $userRecord->id;
                                        $insertMessage->not_received_id = $value->user_id;
                                        $insertMessage->not_chores_id = '';
                                        $insertMessage->not_reward_id = $rewardDetails['red_id'];
                                        $insertMessage->not_claim_id = '';
                                        $insertMessage->not_message_id = '';
                                        $insertMessage->not_data = $rewardDetails['red_rewards_name'];
                                        $insertMessage->not_is_read = 0; // 0 = Not read 1 = read
                                        $insertMessage->not_read_at = date('Y-m-d H:i:s');
                                        $insertMessage->not_createdat = date('Y-m-d H:i:s');
                                        $insertMessage->not_updatedat = date('Y-m-d H:i:s');
                                        $insertMessage->save();

                                        $notificationCount = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')->where('not_received_id',$value->user_id)->where('not_new_notification',0)->count('not_new_notification');

                                        $notificationId = $insertMessage->not_id;
                                        $this->notiChildRewardEdit($value->use_fcm_token,$rewardDetails['red_rewards_name'],$rewardByChildContent,$rewardByChildType,$userRecord->use_username,$notificationId,$notificationCount);
                                    }

                                }else{

                                    $parentsDetails = User::select('id as user_id','use_fcm_token')->whereIn('use_role',[2,3])->where('use_fam_unique_id',$userRecord->use_fam_unique_id)->get();

                                    $rewardDetails = Rewards::select('red_id','red_point','red_rewards_name')->where('red_id',$request->reward_id)->first();

                                    foreach ($parentsDetails as $value) {

                                        $rewardByChildContent =  "reward updated by";
                                        $rewardByChildType = "reward_create_by_child";
                                        $insertMessage = new Notification;
                                        $insertMessage->not_child_name = $userRecord->use_username;
                                        $insertMessage->not_type = $rewardByChildType;
                                        $insertMessage->not_content = $rewardByChildContent;
                                        $insertMessage->not_sender_id = $userRecord->id;
                                        $insertMessage->not_received_id = $value->user_id;
                                        $insertMessage->not_chores_id = '';
                                        $insertMessage->not_reward_id = $rewardDetails['red_id'];
                                        $insertMessage->not_claim_id = '';
                                        $insertMessage->not_message_id = '';
                                        $insertMessage->not_data = $rewardDetails['red_rewards_name'];
                                        $insertMessage->not_is_read = 0; // 0 = Not read 1 = read
                                        $insertMessage->not_read_at = date('Y-m-d H:i:s');
                                        $insertMessage->not_createdat = date('Y-m-d H:i:s');
                                        $insertMessage->not_updatedat = date('Y-m-d H:i:s');
                                        $insertMessage->save();

                                        $notificationCount = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')->where('not_received_id',$value->user_id)->where('not_new_notification',0)->count('not_new_notification');

                                        $notificationId = $insertMessage->not_id;
                                        $this->notiChildRewardEdit($value->use_fcm_token,$rewardDetails['red_rewards_name'],$rewardByChildContent,$rewardByChildType,$userRecord->use_username,$notificationId,$notificationCount);
                                    }
                                }
                                 $msg = "Reward Updated Successfully!";
                                return json_encode(['status' => true, 'error' => 200, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                            }else{
                                 $msg = "Reward isn't updated";
                                return json_encode(['status' => true, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                            }
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
    }

    public function notiChildRewardEdit($token, $rewardName,$rewardByChildContent,$rewardByChildType,$use_username,$notificationId,$notificationCount)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $token = $token;

        $notification = array(
            'body' => $rewardName,
            'title' => $rewardName. ' '.$rewardByChildContent.' '. $use_username.' - Family Days',
            'sound' => "default",
            'color' => "#203E78",
            'type' => $rewardByChildType,
            'notification_id' => $notificationId,
            'badge' => $notificationCount
        );

        $fcmNotification = array(
            'registration_ids' => array($token),
            'priority' => 'high',
            'aps'=>array('alert'=>array('title'=>'test','body'=>'body'), 'content-available'=>1,'mutable_content' =>1),
            'type' => $rewardByChildType,
            'badge' => $notificationCount,

            'headers' => array( 'apns-priority' => '10'),
            'content_available' => true,
            'notification'=> $notification,
            'data' => array(
                'date' => date('d-m-Y H:i:s'),
                'message' => $rewardName,
                'type' => $rewardByChildType,
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

    public function deleteReward(Request $request)
    { 
        $reward_id = explode(',',$request->reward_id);
        if($reward_id)
        {
            foreach ($reward_id as $key => $value) {
                 Rewards::where('red_id',$value)->delete();
            }
            if(count($reward_id) == 1){
                ResponseMessage::successMessage("Reward is deleted");
            }else{
                ResponseMessage::successMessage("Rewards are deleted");
            }
        }else{
            ResponseMessage::error("Reward id is required!");
        }
    } 

    public function filterRewards(Request $request,$childId = 0)
    {
        $header = $request->header('token');
        $from_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->from_date)));
        $to_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->to_date)));
        $childId = $request->child_id;
        // dd($childId);

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
                    $userTokens = DB::table('users')->select('id','use_full_name','use_token','use_is_admin')->where('use_token',$header)->where('use_status',0)->first();

                    $userRecord = DB::table('users')->select('id','use_fam_unique_id','use_is_admin','use_parents_id','use_role','use_is_reset')->where('use_token',$header)->first();

                    $rewardQuery = Rewards::join('users','tbl_rewards.red_child_id','=','users.id')->where('red_family_id',$userRecord->use_fam_unique_id)->where('red_status',0);
                  
                    if($fromDate && $toDate && $childId)
                    {
                        $rewardsRecord = $rewardQuery->whereBetween('red_frame_date', [$from_date,$to_date])->whereIn('red_child_id',explode(',',$childId))->orderby('red_frame_date','ASC')->get();

                    }else if($fromDate && $toDate) // FROM DATE FILTER
                    {  
                       $rewardsRecord = $rewardQuery->whereBetween('red_frame_date', [$from_date,$to_date])->orderby('red_frame_date','ASC')->get();

                    }else if($fromDate && $childId)
                    {
                        $rewardsRecord = $rewardQuery->whereDate('red_frame_date', $from_date)->whereIn('red_child_id',explode(',',$childId))->orderby('red_frame_date','ASC')->get();
                    }
                    else if($fromDate)
                    {
                        $rewardsRecord = $rewardQuery->whereDate('red_frame_date', $from_date)->orderby('red_frame_date','ASC')->get();
                    }
                    else if($childId)
                    {  
                        $rewardsRecord = $rewardQuery->whereIn('red_child_id',explode(',',$childId))->orderby('red_frame_date','ASC')->get();
                    }else{
                         $rewardsRecord = $rewardQuery->where('red_child_id','7899ddd')->orderby('red_frame_date','ASC')->get();
                    }
                      
                    if(!$rewardsRecord->isEmpty())
                    { 
                        $userDetails = array();
                        foreach ($rewardsRecord as $key => $value)
                        { 
                             if($value->red_icon)
                            {
                                $categoryiconUrl = url("public/images/reward-icon/".$value->red_icon);
                            }else{
                                $categoryiconUrl = url("public/images/reward-icon/reward-default-icon.png");
                            }

                            $dueDate = date('d-m-Y', strtotime(str_replace('/', '-', $value->red_frame_date)));
                            if($value->red_cat_id == 1)
                            {
                                $categoryName = $value->red_rewards_name;
                            }else{
                                $categoryName = $value->red_cat_name;
                            }
                            $rewardsDetails[] = array(
                                "reward_id" => $value->red_id,
                                "category" => $categoryName,
                                "category_icon" => $categoryiconUrl,                                    
                                "point" => $value->red_point,
                                "is_conform" => $value->red_is_confirmation,
                                "due_date" => $dueDate,
                                "token" => $value->use_token,
                                "create_by" => $value->use_full_name,
                                "is_admin" => $userTokens->use_is_admin,
                                "is_createby" => $value->red_createby,
                            );
                        }

                        array_walk_recursive($rewardsDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $rewardsDetails;

                        $msg = "Reward filter details";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => $this->data[$key]],JSON_UNESCAPED_SLASHES);
                    }else
                    {
                        $msg = "Reward filter details";
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

    // ----------------- BEGIN USER WISE REWARD LIST ----------------------

    public function userWiseRewardList(Request $request)
    {
        $header = $request->header('token');
        $loadMore = $request->load_more;
        $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
        $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));
        $currentYear = date('Y', strtotime(str_replace('/', '-',$request->date_time)));

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {
                    $userTokens = DB::table('users')->select('id','use_full_name','use_token','use_is_admin')->where('use_token',$header)->where('use_status',0)->first();

                    $userRecord = DB::table('users')->select('id','use_fam_unique_id','use_is_admin','use_parents_id','use_role','use_is_reset')->where('use_token',$header)->first();

                    $lastYears = $currentYear - 4;
                    $lastfiveYear = date('Y-m-d', strtotime(str_replace('/', '-',$lastYears."-01-01")));
                    $currentYears = $currentYear + 4;
                    $currentYearDate = $currentYears."-12-31";

                    if($userRecord->use_role == 2 || $userRecord->use_role == 3)
                    {
                        $rewardQuery = Rewards::join('users','tbl_rewards.red_child_id','=','users.id')->where('red_use_createby',$userRecord->id)->whereBetween('red_frame_date', [$lastfiveYear, $currentYearDate])->where('red_status',0)->orderby('red_frame_date','ASC');
                    }else{
                        $rewardQuery = Rewards::join('users','tbl_rewards.red_child_id','=','users.id')->where('red_child_id',$userRecord->id)->whereBetween('red_frame_date', [$lastfiveYear, $currentYearDate])->where('red_status',0)->orderby('red_frame_date','ASC');
                    }
                    
                    if($loadMore == 1)
                    {
                        $rewardRecords = $rewardQuery->orderby('red_frame_date','ASC')->get()->splice(6);
                    }else {
                        $rewardRecords = $rewardQuery->limit(6)->get();
                    }
                   
                    if(!$rewardRecords->isEmpty())
                    { 

                        $rewardsDetails = array();
                        foreach ($rewardRecords as $key => $value)
                        { 
                            $subBrands = SubBrands::select('bds_link')->where('bds_brand_icon',$value->red_brand_icon)->where('bds_status',0)->orderby('bds_brand_id','ASC')->first();

                            if($subBrands){
                                $brand_url = $subBrands->bds_link ? $subBrands->bds_link:'';
                            }else{
                                $brand_url = '';
                            }
                            
                            if($value->red_icon)
                            {
                                $categoryiconUrl = url("public/images/reward-icon/".$value->red_icon);
                            }else{
                                $categoryiconUrl = url("public/images/reward-icon/reward-default-icon.png");
                            }

                            $dueDate = date('d-m-Y', strtotime(str_replace('/', '-', $value->red_frame_date)));
                            if($value->red_cat_id == 1)
                            {
                                $categoryName = $value->red_rewards_name;
                            }else{
                                $categoryName = $value->red_cat_name;
                            }
                            $rewardsDetails[] = array(
                                "reward_id" => $value->red_id,
                                "category" => $categoryName,
                                "category_icon" => $categoryiconUrl,                                    
                                "point" => $value->red_point,
                                "is_conform" => $value->red_is_confirmation,
                                "due_date" => $dueDate,
                                "token" => $value->use_token,
                                "brand_url" => $brand_url,
                                "create_by" => $value->use_full_name,
                                "is_admin" => $userTokens->use_is_admin,
                                "is_createby" => $value->red_createby,
                            );
                        }

                        array_walk_recursive($rewardsDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $rewardsDetails;
                        ResponseMessage::success("Reward details",$this->data[$key]);
                    }else
                    {
                        ResponseMessage::success("Reward details",array());
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

    public function userWisefilterRewards(Request $request)
    {
        $header = $request->header('token');
        $from_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->from_date)));
        $to_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->to_date)));

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
                    $userTokens = DB::table('users')->select('id','use_full_name','use_token','use_is_admin')->where('use_token',$header)->where('use_status',0)->first();

                    $userRecord = DB::table('users')->select('id','use_fam_unique_id','use_is_admin','use_parents_id','use_role','use_is_reset')->where('use_token',$header)->first();

                    if($userRecord->use_role == 2 || $userRecord->use_role == 3)
                    {
                        $rewardQuery = Rewards::join('users','tbl_rewards.red_child_id','=','users.id')->where('red_use_createby',$userRecord->id)->where('red_status',0);
                    }else{
                        $rewardQuery = Rewards::join('users','tbl_rewards.red_child_id','=','users.id')->where('red_child_id',$userRecord->id)->where('red_status',0);
                    }
                  
                    if($fromDate && $toDate) // FROM DATE FILTER
                    {  
                       $rewardsRecord = $rewardQuery->whereBetween('red_frame_date', [$from_date,$to_date])->orderby('red_frame_date','ASC')->get();

                    }
                    else if($fromDate)
                    {
                        $rewardsRecord = $rewardQuery->whereDate('red_frame_date', $from_date)->orderby('red_frame_date','ASC')->get();
                    }
                    else{
                         $rewardsRecord = $rewardQuery->orderby('red_frame_date','ASC')->limit(6)->get();
                    }
                      
                    if(!$rewardsRecord->isEmpty())
                    { 
                        $userDetails = array();
                        foreach ($rewardsRecord as $key => $value)
                        { 
                             if($value->red_icon)
                            {
                                $categoryiconUrl = url("public/images/reward-icon/".$value->red_icon);
                            }else{
                                $categoryiconUrl = url("public/images/reward-icon/reward-default-icon.png");
                            }

                            $dueDate = date('d-m-Y', strtotime(str_replace('/', '-', $value->red_frame_date)));
                            if($value->red_cat_id == 1)
                            {
                                $categoryName = $value->red_rewards_name;
                            }else{
                                $categoryName = $value->red_cat_name;
                            }
                            $rewardsDetails[] = array(
                                "reward_id" => $value->red_id,
                                "category" => $categoryName,
                                "category_icon" => $categoryiconUrl,                                    
                                "point" => $value->red_point,
                                "is_conform" => $value->red_is_confirmation,
                                "due_date" => $dueDate,
                                "token" => $value->use_token,
                                "create_by" => $value->use_full_name,
                                "is_admin" => $userTokens->use_is_admin,
                                "is_createby" => $value->red_createby,
                            );
                        }

                        array_walk_recursive($rewardsDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $rewardsDetails;

                        ResponseMessage::success("Reward filter details user wise",$this->data[$key]);
                    }else
                    {
                        ResponseMessage::success("Reward filter details user wise",array());
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

    // ----------------- END USER WISE REWARD LIST ----------------------

    // ----------------- BEGIN CLAIM MODULE ----------------------

    public function createClaim(Request $request,$notificationId=0)
    {
        $header = $request->header('token');
        $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
        $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {
                    $userRecord = DB::table('users')->select('id','use_role','use_is_admin','use_username','use_parents_id','use_fam_unique_id','use_total_point')->where('use_token',$header)->first();

                    $notification = NotificationKey::notificationType();

                    if($userRecord)
                    { 
                        $rules = [
                            'reward_id' => 'required',
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
                            $rewardDetails = Rewards::select('red_point','red_rewards_name')->where('red_id',$request->reward_id)->first();

                            if($rewardDetails)
                            {
                                if($userRecord->use_total_point >= $rewardDetails->red_point)
                                {
                                    $updateData['red_is_claim'] = 1; // 0 = No 1 = Yes
                                    $updateData['red_is_claim_date'] = $currentDate;
                                    $updateData['red_status'] = 1;
                                    $updateData['red_updateat'] = $current_date;
                                    $updateReward = Rewards::where('red_id',$request->reward_id)->update($updateData);

                                    $useDetails = User::select('use_total_point')->where('id',$userRecord->id)->first();

                                    $updateUser['use_total_point'] = $useDetails->use_total_point - $rewardDetails->red_point;
                                    $updatePoint = User::where('id',$userRecord->id)->update($updateUser);

                                    $parentsDetail = DB::table('users')->select('id as user_id','use_role','use_is_admin','use_username','use_parents_id','use_fam_unique_id','use_total_point','use_fcm_token')->where('use_fam_unique_id',$userRecord->use_fam_unique_id)->whereIn('use_role',[2,3])->get();

                                    $arrayToken = array();

                                    if(!$parentsDetail->isEmpty())
                                    {
                                        foreach ($parentsDetail as $key => $value) 
                                        {
                                            $insertMessage = new Notification;
                                            $insertMessage->not_child_name = $userRecord->use_username;
                                            $insertMessage->not_type = $notification['cliamchildType'];
                                            $insertMessage->not_content = $notification['cliamchildContent'];
                                            $insertMessage->not_sender_id = $userRecord->id;
                                            $insertMessage->not_received_id = $value->user_id;
                                            $insertMessage->not_chores_id = '';
                                            $insertMessage->not_reward_id = '';
                                            $insertMessage->not_claim_id = $request->reward_id;
                                            $insertMessage->not_message_id = '';
                                            $insertMessage->not_data = $rewardDetails->red_rewards_name ? $rewardDetails->red_rewards_name:'';
                                            $insertMessage->not_is_read = 0; // 0 = Not read 1 = read
                                            $insertMessage->not_read_at = $current_date;
                                            $insertMessage->not_createdat = $current_date;
                                            $insertMessage->not_updatedat = $current_date;
                                            $insertMessage->save();

                                            $notificationId = $insertMessage->not_id;

                                            $arrayToken[] = $value->use_fcm_token;
                                        }

                                        $parentsTokens = $arrayToken;

                                        if($parentsTokens)
                                        {
                                          $notificationMessage = array(
                                            'body' => $rewardDetails->red_rewards_name,
                                            'title' => $rewardDetails->red_rewards_name. ' '.$notification['cliamchildContent'].' '. $userRecord->use_username.' - Family Days',
                                            'sound' => "default",
                                            'color' => "#203E78",
                                            'type' => $notification['cliamchildType'],
                                            'notification_id' => $notificationId
                                        );

                                        $fields = array(
                                            'registration_ids' => $parentsTokens,
                                            'priority' => 'high',
                                            'aps'=>array('alert'=>array('title'=>'test','body'=>'body'), 'content-available'=>1,'mutable_content' =>1),
                                            'type' => $notification['cliamchildType'],

                                            'headers' => array( 'apns-priority' => '10'),
                                            'content_available' => true,
                                            'notification'=> $notificationMessage,
                                            'data' => array(
                                                "date" => $current_date,
                                                "message" => $rewardDetails->red_rewards_name,
                                                "type" => $notification['cliamchildType'],
                                                'vibrate' => 1,
                                                'sound' => 1,
                                                'notification_id' => $notificationId
                                            )
                                        );
                                            NotificationKey::notificationCurl($fields);
                                        }
                                    }
                                    
                                    $message = "Reward claimed successfully";
                                    return json_encode(['status' => true, 'error' => 200, 'message' => $message],JSON_UNESCAPED_SLASHES);
                                }else{
                                    $message = "Insufficient points for claim. complete more chores.";
                                    return json_encode(['status' => false, 'error' => 401, 'message' => $message],JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                $message = "Claim id is'nt valid!";
                                return json_encode(['status' => false, 'error' => 401, 'message' => $message],JSON_UNESCAPED_SLASHES);
                            }
                        }
                    }else
                    {
                        $msg = "Your account isn't active.";
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
    }

    public function claimList(Request $request)
    {
        $header = $request->header('token');
        $loadMore = $request->load_more;
        $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
        $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));
        $currentYears = date('Y', strtotime(str_replace('/', '-',$request->date_time)));
        $notificationId = $request->notification_id;

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {
                    $updataData['not_is_read'] = 1;
                    $update = Notification::where('not_id',$notificationId)->update($updataData);

                    $userTokens = DB::table('users')->select('id','use_full_name','use_token','use_is_admin')->where('use_token',$header)->where('use_status',0)->first();

                    $userRecord = DB::table('users')->select('id','use_fam_unique_id','use_is_admin','use_parents_id','use_role','use_is_reset','use_total_point')->where('use_token',$header)->first();

                    if($userRecord->use_role == 4 || $userRecord->use_role == 5)
                    {
                        $totalPoint = $userRecord->use_total_point;
                    }else{
                        $totalPoint = "";
                    }

                    $lastYears = $currentYears - 4;
                    $lastfiveYear = $lastYears."-01-01";
                    $futureYears = $currentYears + 4;
                    $futureYearDate = $futureYears."-12-31";

                    $rewardQuery = Rewards::join('users','tbl_rewards.red_child_id','=','users.id')->where('red_family_id',$userRecord->use_fam_unique_id)->whereBetween('red_frame_date', [$lastfiveYear, $futureYearDate])->where('red_is_claim',1)->orderby('red_frame_date','ASC');

                    if($loadMore == 1)
                    {
                        $rewardRecords = $rewardQuery->orderby('red_frame_date','ASC')->get()->splice(6);
                    }else {
                        $rewardRecords = $rewardQuery->limit(6)->get();
                    }
                   
                    if(!$rewardRecords->isEmpty())
                    { 

                        $rewardsDetails = array();
                        foreach ($rewardRecords as $key => $value)
                        { 

                            if($value->red_icon)
                            {
                                $categoryiconUrl = url("public/images/reward-icon/".$value->red_icon);
                            }else{
                                $categoryiconUrl = url("public/images/reward-icon/reward-default-icon.png");
                            }

                            $subBrands = SubBrands::select('bds_link')->where('bds_brand_icon',$value->red_brand_icon)->where('bds_status',0)->orderby('bds_brand_id','ASC')->first();

                            if($subBrands){
                                $brand_url = $subBrands->bds_link ? $subBrands->bds_link:'';
                            }else{
                                $brand_url = '';
                            }

                            $dueDate = date('d-m-Y', strtotime(str_replace('/', '-', $value->red_frame_date)));
                            if($value->red_cat_id == 1)
                            {
                                $categoryName = $value->red_rewards_name;
                            }else{
                                $categoryName = $value->red_cat_name;
                            }
                            $rewardsDetails[] = array(
                                "reward_id" => $value->red_id,
                                "category" => $categoryName,
                                "category_icon" => $categoryiconUrl,                                    
                                "point" => $value->red_point,
                                "token" => $value->use_token,
                                "brand_url" => $brand_url,
                                "create_by" => $value->use_full_name,
                                "is_createby" => $value->red_createby,
                                "create_date" => $value->red_is_claim_date
                            );
                        }

                        array_walk_recursive($rewardsDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $rewardsDetails;

                        $msg = "Claim List";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'total_point' => $totalPoint,'data' => $this->data[$key]],JSON_UNESCAPED_SLASHES);
                    }else
                    {
                        ResponseMessage::success("Reward approved details",array());
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

    public function childClaimList(Request $request)
    {
        $header = $request->header('token');
        $loadMore = $request->load_more;
        $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
        $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));
        $currentYears = date('Y', strtotime(str_replace('/', '-',$request->date_time)));

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {

                    $userRecord = DB::table('users')->select('id','use_role','use_fam_unique_id','use_total_point')->where('use_token',$header)->first();

                    $lastYears = $currentYears - 4;
                    $lastfiveYear = $lastYears."-01-01";
                    $futureYears = $currentYears + 4;
                    $futureYearDate = $futureYears."-12-31";

                    if($userRecord->use_role == 4 || $userRecord->use_role == 5)
                    {
                        $rewardQuery = Rewards::join('users','tbl_rewards.red_child_id','=','users.id')->where('red_child_id',$userRecord->id)->whereBetween('red_frame_date', [$lastfiveYear, $futureYearDate])->where('red_is_claim',1)->orderby('red_frame_date','ASC');
                        $totalPoint = $userRecord->use_total_point;
                    }else{
                        $rewardQuery = Rewards::join('users','tbl_rewards.red_child_id','=','users.id')->where('red_family_id',$userRecord->use_fam_unique_id)->whereBetween('red_frame_date', [$lastfiveYear, $futureYearDate])->where('red_is_claim',1)->orderby('red_frame_date','ASC');
                        $totalPoint = "";
                    }

                    if($loadMore == 1)
                    {
                        $rewardRecords = $rewardQuery->orderby('red_frame_date','ASC')->get()->splice(6)->map(function($rewardRecords){  
                            if($rewardRecords->red_icon)
                            {
                                $categoryiconUrl = url("public/images/reward-icon/".$rewardRecords->red_icon);
                            }else{
                                $categoryiconUrl = url("public/images/reward-icon/reward-default-icon.png");
                            }

                            $subBrands = SubBrands::select('bds_link')->where('bds_brand_icon',$rewardRecords->red_brand_icon)->where('bds_status',0)->orderby('bds_brand_id','ASC')->first();

                            if($subBrands){
                                $brand_url = $subBrands->bds_link ? $subBrands->bds_link:'';
                            }else{
                                $brand_url = '';
                            }

                            if($rewardRecords->red_cat_id == 1)
                            {
                                $categoryName = $rewardRecords->red_rewards_name;
                            }else{
                                $categoryName = $rewardRecords->red_cat_name;
                            }        
                            return[     
                                "reward_id" => $rewardRecords->red_id,
                                "category" => $categoryName,
                                "category_icon" => $categoryiconUrl,                                    
                                "point" => $rewardRecords->red_point,
                                "token" => $rewardRecords->use_token,
                                "brand_url" => $brand_url,
                                "create_by" => $rewardRecords->use_full_name,
                                "is_createby" => $rewardRecords->red_createby,
                                "create_date" => $rewardRecords->red_is_claim_date
                            ];      
                        });;
                    }else {
                        $rewardRecords = $rewardQuery->limit(6)->get()
                        ->map(function($rewardRecords){  
                            if($rewardRecords->red_icon)
                            {
                                $categoryiconUrl = url("public/images/reward-icon/".$rewardRecords->red_icon);
                            }else{
                                $categoryiconUrl = url("public/images/reward-icon/reward-default-icon.png");
                            }

                            if($rewardRecords->red_cat_id == 1)
                            {
                                $categoryName = $rewardRecords->red_rewards_name;
                            }else{
                                $categoryName = $rewardRecords->red_cat_name;
                            }  

                            $subBrands = SubBrands::select('bds_link')->where('bds_brand_icon',$rewardRecords->red_brand_icon)->where('bds_status',0)->orderby('bds_brand_id','ASC')->first();

                            if($subBrands){
                                $brand_url = $subBrands->bds_link ? $subBrands->bds_link:'';
                            }else{
                                $brand_url = '';
                            }

                            return[     
                                "reward_id" => $rewardRecords->red_id,
                                "category" => $categoryName,
                                "category_icon" => $categoryiconUrl,                                    
                                "point" => $rewardRecords->red_point,
                                "token" => $rewardRecords->use_token,
                                "brand_url" => $brand_url,
                                "create_by" => $rewardRecords->use_full_name,
                                "is_createby" => $rewardRecords->red_createby,
                                "create_date" => $rewardRecords->red_is_claim_date
                            ];      
                        });         
                    }

                    $msg = "Claim Child List";
                    return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'total_point' => $totalPoint,'data' => $rewardRecords],JSON_UNESCAPED_SLASHES);
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

    public function filterclaimList(Request $request)
    {
        $header = $request->header('token');
        $from_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->from_date)));
        $to_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->to_date)));
        $childId = $request->child_id;
        // dd($childId);

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
                    $userTokens = DB::table('users')->select('id','use_full_name','use_token','use_is_admin')->where('use_token',$header)->where('use_status',0)->first();

                    $userRecord = DB::table('users')->select('id','use_fam_unique_id','use_is_admin','use_parents_id','use_role','use_is_reset')->where('use_token',$header)->first();

                    $rewardQuery = Rewards::join('users','tbl_rewards.red_child_id','=','users.id')->where('red_is_claim',1)->where('red_family_id',$userRecord->use_fam_unique_id);
                  
                    if($fromDate && $toDate && $childId)
                    {
                        $rewardsRecord = $rewardQuery->whereBetween('red_is_claim_date', [$from_date,$to_date])->whereIn('red_child_id',explode(',',$childId))->orderby('red_is_claim_date','ASC')->get();

                    }else if($fromDate && $toDate) // FROM DATE FILTER
                    {  
                       $rewardsRecord = $rewardQuery->whereBetween('red_is_claim_date', [$from_date,$to_date])->orderby('red_is_claim_date','ASC')->get();

                    }else if($fromDate && $childId)
                    {
                        $rewardsRecord = $rewardQuery->whereDate('red_is_claim_date', $from_date)->whereIn('red_child_id',explode(',',$childId))->orderby('red_is_claim_date','ASC')->get();
                    }
                    else if($fromDate)
                    {
                        $rewardsRecord = $rewardQuery->whereDate('red_is_claim_date', $from_date)->orderby('red_is_claim_date','ASC')->get();
                    }
                    else if($childId)
                    {  
                        $rewardsRecord = $rewardQuery->whereIn('red_child_id',explode(',',$childId))->orderby('red_is_claim_date','ASC')->get();
                    }else{
                         $rewardsRecord = $rewardQuery->orderby('red_is_claim_date','ASC')->limit(6)->get();
                    }
                      
                    if(!$rewardsRecord->isEmpty())
                    { 
                        $userDetails = array();
                        foreach ($rewardsRecord as $key => $value)
                        { 
                             if($value->red_icon)
                            {
                                $categoryiconUrl = url("public/images/reward-icon/".$value->red_icon);
                            }else{
                                $categoryiconUrl = url("public/images/reward-icon/reward-default-icon.png");
                            }

                            $dueDate = date('d-m-Y', strtotime(str_replace('/', '-', $value->red_frame_date)));
                            if($value->red_cat_id == 1)
                            {
                                $categoryName = $value->red_rewards_name;
                            }else{
                                $categoryName = $value->red_cat_name;
                            }

                            $subBrands = SubBrands::select('bds_link')->where('bds_brand_icon',$value->red_brand_icon)->where('bds_status',0)->orderby('bds_brand_id','ASC')->first();

                            if($subBrands){
                                $brand_url = $subBrands->bds_link ? $subBrands->bds_link:'';
                            }else{
                                $brand_url = '';
                            }

                            $rewardsDetails[] = array(
                                "reward_id" => $value->red_id,
                                "category" => $categoryName,
                                "category_icon" => $categoryiconUrl,                                    
                                "point" => $value->red_point,
                                "token" => $value->use_token,
                                "brand_url" => $brand_url,
                                "create_by" => $value->use_full_name,
                                "is_createby" => $value->red_createby,
                                "create_date" => $value->red_is_claim_date
                            );
                        }

                        array_walk_recursive($rewardsDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $rewardsDetails;

                        $msg = "Claim filter details";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => $this->data[$key]],JSON_UNESCAPED_SLASHES);
                    }else
                    {
                        $msg = "Claim filter details";
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

    public function fieldchildClaimList(Request $request)
    {
        $header = $request->header('token');
        $from_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->from_date)));
        $to_date = date('Y-m-d', strtotime(str_replace('/', '-',$request->to_date)));

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
                    $userTokens = DB::table('users')->select('id','use_full_name','use_token','use_is_admin')->where('use_token',$header)->where('use_status',0)->first();

                    $userRecord = DB::table('users')->select('id','use_fam_unique_id','use_is_admin','use_parents_id','use_role','use_is_reset')->where('use_token',$header)->first();

                    $rewardQuery = Rewards::join('users','tbl_rewards.red_child_id','=','users.id')->where('red_is_claim',1)->where('red_child_id',$userRecord->id);
                  
                    if($fromDate && $toDate) // FROM DATE FILTER
                    {  
                       $rewardsRecord = $rewardQuery->whereBetween('red_is_claim_date',[$from_date,$to_date])->orderby('red_is_claim_date','ASC')->get();

                    }else if($fromDate)
                    {
                        $rewardsRecord = $rewardQuery->whereDate('red_is_claim_date',$from_date)->orderby('red_is_claim_date','ASC')->get();
                    }
                    else{
                         $rewardsRecord = $rewardQuery->orderby('red_is_claim_date','ASC')->limit(6)->get();
                    }
                      
                    if(!$rewardsRecord->isEmpty())
                    { 
                        $userDetails = array();
                        foreach ($rewardsRecord as $key => $value)
                        { 
                             if($value->red_icon)
                            {
                                $categoryiconUrl = url("public/images/reward-icon/".$value->red_icon);
                            }else{
                                $categoryiconUrl = url("public/images/reward-icon/reward-default-icon.png");
                            }

                            $dueDate = date('d-m-Y', strtotime(str_replace('/', '-', $value->red_frame_date)));
                            if($value->red_cat_id == 1)
                            {
                                $categoryName = $value->red_rewards_name;
                            }else{
                                $categoryName = $value->red_cat_name;
                            }

                            $subBrands = SubBrands::select('bds_link')->where('bds_brand_icon',$value->red_brand_icon)->where('bds_status',0)->orderby('bds_brand_id','ASC')->first();

                            if($subBrands){
                                $brand_url = $subBrands->bds_link ? $subBrands->bds_link:'';
                            }else{
                                $brand_url = '';
                            }

                            $rewardsDetails[] = array(
                                "reward_id" => $value->red_id,
                                "category" => $categoryName,
                                "category_icon" => $categoryiconUrl,                                    
                                "point" => $value->red_point,
                                "token" => $value->use_token,
                                "brand_url" => $brand_url,
                                "create_by" => $value->use_full_name,
                                "is_createby" => $value->red_createby,
                                "create_date" => $value->red_is_claim_date
                            );
                        }

                        array_walk_recursive($rewardsDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $rewardsDetails;

                        $msg = "Claim filter details";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => $this->data[$key]],JSON_UNESCAPED_SLASHES);
                    }else
                    {
                        $msg = "Claim filter details";
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

    // ----------------- END CLAIM MODULE ----------------------
}
