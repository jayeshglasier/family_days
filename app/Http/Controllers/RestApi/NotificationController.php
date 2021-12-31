<?php

namespace App\Http\Controllers\RestApi;
use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationListCollection;
use Illuminate\Http\Request;
use App\Helper\Exceptions;
use App\Model\Notification;
use App\User;
use Validator;
use DB;

class NotificationController extends Controller
{
   
    public function notificationList(Request $request,$username = '')
    {
        $header = $request->header('token');
        $user = User::select('id as user_id','use_token as token')->where('use_token',$header)->where('use_status',0)->first();
        $notificationRecord = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')
        ->select('tbl_notifications.not_id as notification_id','tbl_notifications.not_type','tbl_notifications.not_content','tbl_notifications.not_data','tbl_notifications.not_createdat as created_date','tbl_notifications.not_sender_id','tbl_notifications.not_chores_id','tbl_notifications.not_reward_id','tbl_notifications.not_received_id','tbl_notifications.not_is_read','tbl_notifications.not_child_name','users.id as use_id','users.use_full_name','users.use_username','users.use_image','users.use_token as token')
        ->where('tbl_notifications.not_received_id',$user->user_id)->orderBy('tbl_notifications.not_id','DESC')->get();

        if(!$notificationRecord->isEmpty())
        {
            $msg = "Notification list";
            $result = new NotificationListCollection($notificationRecord);
            
        }else{
            $msg = "No any notification found!";
            $result = array();
        }
        return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => $result],JSON_UNESCAPED_SLASHES);
    }

    public function unreadNotificationCount(Request $request)
    {
        $header = $request->header('token');

        $user = User::select('id as user_id','use_token as token')->where('use_token',$header)->where('use_status',0)->first();
        $notiUnreadCount = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')->select('not_is_read')->where('tbl_notifications.not_received_id',$user->user_id)->where('not_is_read',0)->count();

        $messageUnRead = DB::table('tbl_family_message')->join('users','tbl_family_message.cha_received_id','=','users.id')->select('cha_is_read')->where('tbl_family_message.cha_received_id',$user->user_id)->where('cha_is_read',0)->count();

        if($notiUnreadCount)
        {   
            $notification_count = $notiUnreadCount;                                
            $msg = "Unread notification / message count";
            return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'notification_count' => $notification_count,'message_count' => $messageUnRead],JSON_UNESCAPED_SLASHES);

        }else{
            $msg = "Unread notification / message count";
            return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'notification_count' => 0,'message_count' => 0],JSON_UNESCAPED_SLASHES);
        }
    }
}