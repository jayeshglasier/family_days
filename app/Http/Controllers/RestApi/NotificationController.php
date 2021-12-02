<?php

namespace App\Http\Controllers\RestApi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Helper\ResponseMessage;
use App\Model\SystemSetting;
use App\Helper\Exceptions;
use App\Model\ChoreIcon;
use App\Model\ChoreStatus;
use App\Model\PresetChores;
use App\Model\Notification;
use App\Model\Chores;
use App\Post;
use App\User;
use App\MediaFile;
use Validator;
use DB;
use Carbon\Carbon;

class NotificationController extends Controller
{
   
  public function notificationList(Request $request,$username = '')
  {
    $header = $request->header('token');

    if($header)
    {
        if(User::where('use_token',$header)->exists())
        {
            $user = User::select('id as user_id','use_token as token')->where('use_token',$header)->where('use_status',0)->first();

            $notificationRecord = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')
            ->select('tbl_notifications.not_id as notification_id','tbl_notifications.not_type','tbl_notifications.not_content','tbl_notifications.not_data','tbl_notifications.not_createdat as created_date','tbl_notifications.not_sender_id','tbl_notifications.not_chores_id','tbl_notifications.not_reward_id','tbl_notifications.not_received_id','tbl_notifications.not_is_read','tbl_notifications.not_child_name','users.id as use_id','users.use_full_name','users.use_username','users.use_image','users.use_token as token')
            ->where('tbl_notifications.not_received_id',$user->user_id)->orderBy('tbl_notifications.not_id','DESC')->get();
           
            if(!$notificationRecord->isEmpty())
            {   
                $postData = array();
                $i = 1;
                foreach ($notificationRecord as $key => $value)
                {
                    if($value->use_image)
                    {
                        $profileurl = url("public/images/user-images/".$value->use_image);
                    }else{
                        $profileurl = url("public/images/user-images/user-profile.png");
                    }

                    $date1 = $value->created_date;
                    $date2 = date('Y-m-d H:i:s');
                    $from = Carbon::createFromFormat('Y-m-d H:i:s', $date1);
                    $to = Carbon::createFromFormat('Y-m-d H:i:s', $date2);
                    $diff_in_minutes = $from->diffInMinutes($to);
                    $difference = Carbon::now()->subMinutes($diff_in_minutes)->diffForHumans();

                    $postData[] = array(
                      "notification_id" => $value->notification_id,
                      "type" => $value->not_type,
                      "message" => $value->not_data,
                      "content" => $value->not_content,
                      "chores_id" => $value->not_chores_id,
                      "reward_id" => $value->not_reward_id,
                      "user_id" => $value->use_id,
                      "token" => $value->token,
                      "full_name" => $value->use_full_name,
                      "username" => $value->not_child_name,
                      "profile_url" => $profileurl,
                      "send_time" => $difference,
                      "is_read" => $value->not_is_read);
                    $i++;
                }

                array_walk_recursive($postData, function (&$item, $key) {
                $item = null === $item ? '' : $item;
                });
                $this->data[$key] = $postData;                                
                $msg = "Notification list";
                return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => $this->data[$key]],JSON_UNESCAPED_SLASHES);

            }else{
                $msg = "No any notification found!";
                return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => array()],JSON_UNESCAPED_SLASHES);
            }
            
        }else{
            $msg = "Token isn't valid!";
            return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
        }
    }
    else
    {
        $msg = "Token is required.";
        return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
    }
  }


  public function unreadNotificationCount(Request $request)
  {
    $header = $request->header('token');

    if($header)
    {
        if(User::where('use_token',$header)->exists())
        {
            $user = User::select('id as user_id','use_token as token')->where('use_token',$header)->where('use_status',0)->first();

            $notiUnreadCount = DB::table('tbl_notifications')->join('users','tbl_notifications.not_sender_id','=','users.id')->select('not_is_read')
            ->where('tbl_notifications.not_received_id',$user->user_id)->where('not_is_read',0)->count();

            $messageUnRead = DB::table('tbl_family_message')->join('users','tbl_family_message.cha_received_id','=','users.id')->select('cha_is_read')
            ->where('tbl_family_message.cha_received_id',$user->user_id)->where('cha_is_read',0)->count();
           
            if($notiUnreadCount)
            {   
                $notification_count = $notiUnreadCount;                                
                $msg = "Unread notification / message count";
                return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'notification_count' => $notification_count,'message_count' => $messageUnRead],JSON_UNESCAPED_SLASHES);

            }else{
                $msg = "Unread notification / message count";
                return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'notification_count' => 0,'message_count' => 0],JSON_UNESCAPED_SLASHES);
            }
            
        }else{
            $msg = "Token isn't valid!";
            return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
        }
    }
    else
    {
        $msg = "Token is required.";
        return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
    }
  }


}