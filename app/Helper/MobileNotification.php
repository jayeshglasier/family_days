<?php 
namespace App\Helper;

use Illuminate\Database\Eloquent\Helper;
use App\Model\Notification;

class MobileNotification 
{
	public static function sendNotification($title,$userName,$sendMessageType,$sendMessageContent,$senderId,$receivedId,$choresId,$rewardId,$claimId,$messageId,$message,$deviceToken,$deviceType,$notificationCount)
	{
        $fcmNotification = array();
        $insertMessage = new Notification;
        $insertMessage->not_child_name = $userName;
        $insertMessage->not_type = $sendMessageType;
        $insertMessage->not_content = $sendMessageContent;
        $insertMessage->not_sender_id = $senderId;
        $insertMessage->not_received_id = $receivedId;
        $insertMessage->not_chores_id = $choresId;
        $insertMessage->not_reward_id = $rewardId;
        $insertMessage->not_claim_id = $claimId;
        $insertMessage->not_message_id = $messageId;
        $insertMessage->not_data = $message;
        $insertMessage->not_is_read = 0; // 0 = No Read 1 = Yes Read
        $insertMessage->not_read_at = date('Y-m-d H:i:s');
        $insertMessage->not_createdat = date('Y-m-d H:i:s');
        $insertMessage->not_updatedat = date('Y-m-d H:i:s');
        $insertMessage->save();

        $url = 'https://fcm.googleapis.com/fcm/send';
        $token = $deviceToken;

        if($deviceType)
        {
            if($deviceType == 1) // iOs
            {
                $notificationMessage = array(
                    'body' => $message,
                    'title' => $title,
                    'sound' => "default",
                    'color' => "#203E78",
                    'type' => $sendMessageType,
                    'notification_id' => $insertMessage->not_id,
                    'badge' => $notificationCount
                );

                $fcmNotification = array(
                    'registration_ids' => array($token),
                    'priority' => 'high',
                    'aps'=>array('alert'=>array('title'=>'test','body'=>'body'), 'content-available'=>1,'mutable_content' =>1),
                    'type' => $sendMessageType,
                    'badge' => $notificationCount,

                    'headers' => array( 'apns-priority' => '10'),
                    'content_available' => true,
                    'notification'=> $notificationMessage,
                    'data' => array(
                        'date' => date('d-m-Y H:i:s'),
                        'message' => $message,
                        'type' => $sendMessageType,
                        'vibrate' => 1,
                        'sound' => 1,
                        'notification_id' => $insertMessage->not_id,
                        'badge' => $notificationCount
                    )
                );
            }

            else if($deviceType == 2){ // Android
              $notification = [
                'date'      => date('d-m-Y H:i:s'),
                'title'     => $title,
                'body'      => $message,
                'sound'     => "default",
                'color'     => "#203E78",
                'type'      => $sendMessageType,
                'message'   => $message,
                'vibrate'   => 1,
                'badge'     => $notificationCount,
              ];
              
              $extraNotificationData = $notification;

              $fcmNotification = [
                  'to'   => $token,
                  'data' => $extraNotificationData
              ];
            }
            
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
	}
}

?>