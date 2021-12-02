<?php

namespace App\Http\Controllers\RestApi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use App\Helper\ResponseMessage;
use App\Helper\NotificationKey;
use App\Helper\Exceptions;
use App\Mail\UserRegistered;
use App\Model\Notification;
use App\Model\Messages;
use App\Model\MessagesCounts;
use App\Post;
use App\User;
use App\MediaFile;
use Mail;
use DB;

class MessageController extends Controller
{
    /**
     * Return all users except the existing one
     * 
     */

    public function familymemberList(Request $request,$unRead = 0)
    {
        try
        {
            $header = $request->header('token');
            $notificationId = $request->notification_id;

            if($header)
            {
                if(User::where('use_token',$header)->exists())
                {
                    if(User::where('use_token',$header)->where('use_status',0)->exists())
                    {   
                        $usertDetail = DB::table('users')->select('id as userId','email','use_token as token','use_full_name as full_name','use_image','use_role','use_parents_id','use_is_admin','use_fam_unique_id','use_username','use_total_point')->where('use_token',$header)->first();

                        $updataData['not_is_read'] = 1;
                        $update = Notification::where('not_id',$notificationId)->update($updataData);

                        if($usertDetail)
                        {
                            if($usertDetail->use_role == 2)
                            {
                                $userRole = "Father";
                            }else if($usertDetail->use_role == 3){
                                $userRole = "Mother";
                            }else if($usertDetail->use_role == 4){
                                $userRole = "Son";
                            }else if($usertDetail->use_role == 5){
                                $userRole = "Daughter";
                            }else{
                                $userRole = "";
                            }

                            if($usertDetail->use_is_admin == 1)
                            {
                                $isAdmin = "Admin";
                            }else{
                                $isAdmin = "";
                            }

                            if($usertDetail->use_image)
                            {
                                $profileImages = url("public/images/user-images/".$usertDetail->use_image);
                            }else{
                                $profileImages = url("public/images/user-images/user-profile.png");
                            }

                            $userInformation = array("user_id" => $usertDetail->userId,"username" => $usertDetail->use_username,"full_name" => $usertDetail->full_name,"email" => $usertDetail->email, "token" => $usertDetail->token,"role" => $usertDetail->use_role,"role_type" => $userRole,"is_admin" => $isAdmin,"total_point" => $usertDetail->use_total_point,"profile_url" => $profileImages);
                        }else{
                            $userInformation = array();
                        }

                        $userRecords = DB::table('users')->select('id as user_id','email','use_token as token','use_full_name as full_name','use_image','use_role','use_is_admin','use_username','use_total_point')->where('id','<>',$usertDetail->userId)->where('use_fam_unique_id',$usertDetail->use_fam_unique_id)->orderBy('use_role','ASC')->limit(15)->get();
 
                        if(!$userRecords->isEmpty())
                        { 
                        $userDetails = array();
                        foreach ($userRecords as $key => $value)
                        { 

                            $statusRead = MessagesCounts::where('msc_sender_id',$value->user_id)->where('msc_received_id',$usertDetail->userId)->first();
                            if($statusRead)
                            {
                                $unRead = $statusRead->msc_count;
                            }else{
                                $unRead = 1;
                            }

                            $messageCount = Messages::select('cha_is_read')->where('cha_is_read',0)->where('cha_send_id',$value->user_id)->where('cha_received_id',$usertDetail->userId)->count();

                            if($value->use_image)
                            {
                                $profileurl = url("public/images/user-images/".$value->use_image);
                            }else{
                                $profileurl = url("public/images/user-images/user-profile.png");
                            }

                            if($value->use_role == 2)
                            {
                                $userType = "Father";
                            }else if($value->use_role == 3){
                                $userType = "Mother";
                            }else if($value->use_role == 4){
                                $userType = "Son";
                            }else if($value->use_role == 5){
                                $userType = "Daughter";
                            }else{
                                $userType = "";
                            }

                            if($value->use_is_admin == 1)
                            {
                                $isAdmin = "Admin";
                            }else{
                                $isAdmin = "";
                            }

                            $userRecord[] = array("user_id" => $value->user_id,"username" => $value->use_username,"full_name" => $value->full_name,"email" => $value->email, "token" => $value->token,"role" => $value->use_role,"role_type" => $userType,"is_admin" => $isAdmin,"total_point" => $value->use_total_point,'is_read' => $unRead,'message_count' => $messageCount,"profile_url" => $profileurl);
                        }

                        array_walk_recursive($userRecord, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $userRecord;

                        $message = "Family member list";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $message,'user_info' => $userInformation, 'data'=> $this->data[$key]],JSON_UNESCAPED_SLASHES);

                        }else{
                        $message = "Family member list";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $message,'user_info' => array(), 'data'=> array()],JSON_UNESCAPED_SLASHES);
                    }
                    }else
                    {
                        ResponseMessage::error("Your account isn't active.");
                    }
                }
                else{
                    ResponseMessage::error("Token isn't valid!");
                }
            }else{
                ResponseMessage::error("Token is required!");
            }
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function createMessage(Request $request,$senderId=0)
    {
        $header = $request->header('token');  // Means message sender user 

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {
                    $userRecord = DB::table('users')->select('id','use_role','use_is_admin','use_parents_id','use_username')->where('use_token',$header)->first();

                    $notification = NotificationKey::notificationType();
                   
                    if($userRecord)
                    {
                        $senderId = $userRecord->id;
                    }

                    if($userRecord)
                    { 
                        $rules = [
                            'received_id' => 'required',
                            'message' => 'required',
                            'date_time' => 'required',
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
                        $uniqueId = "message".date("YmdHis");
                        $current_date = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$request->date_time)));
                        $currentDate = date('Y-m-d', strtotime(str_replace('/', '-',$request->date_time)));

                        $insertData = new Messages;
                        $insertData->cha_unique_id = $uniqueId;
                        $insertData->cha_send_id = $senderId; // user sender message id
                        $insertData->cha_received_id = $request['received_id']; // user recived message id
                        $insertData->cha_message = $request['message']; // text message
                        $insertData->cha_date = $currentDate;
                        $insertData->cha_is_read = 0; //0 = No 1 = Yes
                        $insertData->cha_status = 0; //0 = Active 1 = Inactive
                        $insertData->cha_createat = $current_date;
                        $insertData->cha_updateat = $current_date;
                        $insertData->save();

                        if(MessagesCounts::where('msc_sender_id',$senderId)->where('msc_received_id',$request['received_id'])->exists())
                        {
                            $update['msc_count'] = 0;
                            MessagesCounts::where('msc_sender_id',$senderId)->where('msc_received_id',$request['received_id'])->update($update);

                        }else{
                            $insert = new MessagesCounts;
                            $insert->msc_sender_id = $senderId; // user sender message id
                            $insert->msc_received_id = $request['received_id']; // user recived message id
                            $insert->msc_count = 0;
                            $insert->msc_createat = $current_date;
                            $insert->msc_updateat = $current_date;
                            $insert->save();
                        }

                        $userReceived = DB::table('users')->select('id as user_id','use_fcm_token')->where('id',$request['received_id'])->first();

                        if($userReceived)
                        {
                            $insertMessage = new Notification;
                            $insertMessage->not_child_name = $userRecord->use_username;
                            $insertMessage->not_type = $notification['sendMessageType'];
                            $insertMessage->not_content = $notification['sendMessageContent'];
                            $insertMessage->not_sender_id = $senderId;
                            $insertMessage->not_received_id = $request['received_id'];
                            $insertMessage->not_chores_id = '';
                            $insertMessage->not_reward_id = '';
                            $insertMessage->not_claim_id = '';
                            $insertMessage->not_message_id = $insertData->cha_id;
                            $insertMessage->not_data = $request['message'];
                            $insertMessage->not_is_read = 0; // 0 = No Read 1 = Yes Read
                            $insertMessage->not_read_at = date('Y-m-d H:i:s');
                            $insertMessage->not_createdat = date('Y-m-d H:i:s');
                            $insertMessage->not_updatedat = date('Y-m-d H:i:s');
                            $insertMessage->save();

                            $notificationCount = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')->where('not_received_id',$request['received_id'])->where('not_new_notification',0)->count('not_new_notification');

                            $notificationMessage = array(
                                'body' => $request['message'],
                                'title' => $userRecord->use_username. ' '.$notification['sendMessageContent'].' - Family Days',
                                'sound' => "default",
                                'color' => "#203E78",
                                'type' => $notification['sendMessageType'],
                                'notification_id' => $insertMessage->not_id,
                                'badge' => $notificationCount
                            );

                            $fields = array(
                                'registration_ids' => array($userReceived->use_fcm_token),
                                'priority' => 'high',
                                'aps'=>array('alert'=>array('title'=>'test','body'=>'body'), 'content-available'=>1,'mutable_content' =>1),
                                'type' => $notification['sendMessageType'],
                                'badge' => $notificationCount,

                                'headers' => array( 'apns-priority' => '10'),
                                'content_available' => true,
                                'notification'=> $notificationMessage,
                                'data' => array(
                                    'date' => date('d-m-Y H:i:s'),
                                    'message' => $request['message'],
                                    'type' => $notification['sendMessageType'],
                                    'vibrate' => 1,
                                    'sound' => 1,
                                    'notification_id' => $insertMessage->not_id,
                                    'badge' => $notificationCount
                                )
                            );

                            NotificationKey::notificationCurl($fields);
                        }
                        ResponseMessage::successMessage("Message send successfully!");
                        }
                    }else
                    {
                        ResponseMessage::successMessage("Message is'nt send!");
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

    public function messageList(Request $request)
    {
        $header = $request->header('token');
        $receivedId = $request->user_id;

        $notificationId = $request->notification_id;

        if($notificationId)
        {
            $updataData['not_is_read'] = 1;
            $update = Notification::where('not_id',$notificationId)->update($updataData);
        }

        if($header)
        {
            if(User::where('use_token',$header)->exists())
            {   
                if(User::where('use_token',$header)->where('use_status',0)->exists())
                {
                    $userRecord = DB::table('users')->select('id as userId','use_role','use_is_admin','use_parents_id')->where('use_token',$header)->first();

                    $userReceived = DB::table('users')->select('id as user_id','use_role','use_is_admin','use_parents_id','use_total_point')->where('id',$request->user_id)->first();
                    
                    $childTotalPoint = "";

                    if($userReceived)
                    {
                        if($userReceived->use_role == 4 || $userReceived->use_role == 5)
                        {
                            $childTotalPoint = $userReceived->use_total_point;
                        }
                        $userRole = $userReceived->use_role;
                    }else{
                        $userRole = 2;
                    }

                    $update['msc_count'] = 1;
                    MessagesCounts::where('msc_sender_id',$receivedId)->where('msc_received_id',$userRecord->userId)->update($update);


                    $updateMessage['cha_is_read'] = 1;
                    Messages::where('cha_send_id',$receivedId)->where('cha_received_id',$userRecord->userId)->update($updateMessage);
                   
                    $messageRecords = Messages::select('cha_id','cha_send_id','cha_received_id','cha_message','cha_createat')->whereIn('cha_send_id',[$userRecord->userId,$receivedId])->orderBy(DB::raw("(DATE_FORMAT(cha_createat,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();

                    if(!$messageRecords->isEmpty())
                    { 
                        $messageDetails = array();
                        foreach ($messageRecords as $key => $value)
                        { 
                            if($value->cha_received_id == $request->user_id || $value->cha_received_id == $userRecord->userId)
                            {
                                $userDetail = DB::table('users')->select('id as user_id','use_full_name','use_username','use_token','use_image')->where('id',$value->cha_send_id)->first();

                            if($userDetail)
                            {
                                $profileurl = url("public/images/user-images/".$userDetail->use_image);
                                $username = $userDetail->use_username;
                                $fullname = $userDetail->use_full_name;
                                $token = $userDetail->use_token;
                            }else{
                                $profileurl = url("public/images/user-images/user-profile.png");
                                $username = '';
                                $fullname = '';
                                $token = '';
                            }

                            $messageDetails[] = array(
                            "message_id" => $value->cha_id,
                            "message" => $value->cha_message,
                            "username" => $username,
                            "fullname" => $fullname,
                            "token" => $token,
                            "date_time" => date('d-m-Y H:i', strtotime(str_replace('/', '-', $value->cha_createat))),
                            "profileurl" => $profileurl);
                            }
                        }

                        array_walk_recursive($messageDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $messageDetails;
                        $message = "Messages list";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $message,'user_role' => $userRole,'total_point' => $childTotalPoint,'data'=> $this->data[$key]],JSON_UNESCAPED_SLASHES);
                    }else
                    {
                        $message = "Messages list";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $message,'user_role' => $userRole,'total_point' => $childTotalPoint,'data'=> array()],JSON_UNESCAPED_SLASHES);
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

    public function destroy(Request $request)
    { 
        $messageId = $request->message_id;
        if($messageId)
        {
            if(Messages::where('cha_id',$messageId)->exists())
            {
                Messages::where('cha_id',$messageId)->delete();
                ResponseMessage::successMessage("Message is deleted");
            }else{
                ResponseMessage::error("Message id isn't valid!");
            }
        }else{
            ResponseMessage::error("Message id is required!");
        }
    }

   
}
