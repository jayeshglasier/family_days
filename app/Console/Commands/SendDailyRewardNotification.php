<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Model\Chores;
use App\Model\Notification;
use App\Model\Rewards;
use Carbon\Carbon;
use DB;

class SendDailyRewardNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendReward:reward';

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
        // \Log::info("Hourly Update has been send successfully!");
        //$this->info('Hourly Update has been send successfully');

        $current_date = date('Y-m-d');
        $tomorrow_date = Carbon::now()->subDays(-1);
        $tomorrowDate = $tomorrow_date->format('Y-m-d');
        $notificationId = 0;

        $recordReward = Rewards::select('id as user_id','red_id','red_rewards_name','use_fcm_token','use_device_type')->join('users','tbl_rewards.red_child_id','=','users.id')->whereBetween('red_frame_date', [$current_date,$tomorrowDate])->where('red_status',0)->orderby('red_frame_date','ASC')->get();

        if(!$recordReward->isEmpty())
        {
            $content = "Reward expiring in 24 Hours";
            $type = "twenty_four_hours_remaining_reward";
            foreach ($recordReward as $key => $value) {
                $this->notification($value['use_fcm_token'],$value->red_rewards_name,$content,$type,$notificationId,$value['use_device_type']);
            }
        }

        // -------------------- End Send notification remaining 24 hours (Chores) -------------------
    }

    public function notification($token, $rewardName,$content,$type,$notificationId,$deviceType)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $token = $token;

        $fcmNotification = array();

        if($deviceType == 1) // iOs
        {

          $notification = array(
              'body' => $rewardName,
              'title' => $rewardName.' '. $content.' - Family Days',
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
                  "message" => $rewardName,
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
                'title'     => $rewardName.' '. $content.' - Family Days',
                'body'      => $rewardName,
                'sound'     => "default",
                'color'     => "#203E78",
                'type'      => $type,
                'message'   => $rewardName,
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
