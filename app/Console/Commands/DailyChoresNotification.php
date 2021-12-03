<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Model\Chores;
use App\Model\Notification;
use App\Model\DailyChores;
use Carbon\Carbon;
use DB;

class DailyChoresNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendDailyChores:dailychores';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a Daily email to all users with a word and its meaning';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $current_date = date('Y-m-d');
        $notificationId = 0;

        $recordReward = Chores::select('id as user_id','cho_id','cho_title','use_fcm_token','cho_date','use_username','cho_last_date','use_device_type')->join('users','tbl_chores_list.cho_child_id','=','users.id')->whereDate('cho_date',$current_date)->orderby('cho_date','ASC')->get();

        foreach ($recordReward as $key => $value) {

            $DeferenceInDays = Carbon::parse($current_date)->diffInDays($value->cho_last_date);
            $content = 'chores left '.$DeferenceInDays.' days';
            $type = "chore_create_by_child";

            $insertMessage = new Notification;
            $insertMessage->not_child_name = $value->use_username;
            $insertMessage->not_type = $type;
            $insertMessage->not_content = $content;
            $insertMessage->not_sender_id = 0;
            $insertMessage->not_received_id = $value->user_id;
            $insertMessage->not_chores_id = $value->cho_id;
            $insertMessage->not_reward_id = '';
            $insertMessage->not_claim_id = '';
            $insertMessage->not_message_id = '';
            $insertMessage->not_data = $value->cho_title;
            $insertMessage->not_is_read = 0; // 0 = Not read 1 = read
            $insertMessage->not_read_at = date('Y-m-d H:i:s');
            $insertMessage->not_createdat = date('Y-m-d H:i:s');
            $insertMessage->not_updatedat = date('Y-m-d H:i:s');
            $insertMessage->save();
            $this->notificationDailyChores($value['use_fcm_token'],$value->cho_title,$content,$type,$notificationId,$value['use_device_type']);
         }

        // -------------------- End Send notification remaining 24 hours (Chores) -------------------
    }

    public function notificationDailyChores($token, $choresTitle,$content,$type,$notificationId,$deviceType)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $token = $token;

        $fcmNotification = array();

        if($deviceType == 1) // iOs
        {

          $notification = array(
              'body' => $choresTitle,
              'title' => $choresTitle.' '. $content.' - Family Days',
              'sound' => "default",
              'color' => "#203E78",
              'type' => $type,
              'notification_id' => $notificationId
          );

          $fcmNotification = array(
              'registration_ids' => array($token),
              'priority' => 'high',
              'aps'=>array('alert'=>array('title'=>'test','body'=>'body'), 'content-available'=>1,'mutable_content' =>1),
              'type' => $type,

              'headers' => array( 'apns-priority' => '10'),
              'content_available' => true,
              'notification'=> $notification,
              'data' => array(
                  "date" => date('d-m-Y H:i:s'),
                  "message" => $choresTitle,
                  "type" => $type,
                  'vibrate' => 1,
                  'sound' => 1,
                  'notification_id' => $notificationId
              )
          );
        }

        if($deviceType == 2) // Andriod
        {
            $notification = [
                'date'      => date('d-m-Y H:i:s'),
                'title'     => $choresTitle.' '. $content.' - Family Days',
                'body'      => $choresTitle,
                'sound'     => "default",
                'color'     => "#203E78",
                'type'      => $type,
                'message'   => $choresTitle,
                'vibrate'   => 1,
                'badge'     => 1,
                'notification_id' => $notificationId
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
