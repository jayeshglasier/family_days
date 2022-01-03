<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\Model\Chores;
use App\Model\Notification;
use App\Model\StatusNotification;
use DB;

class SendDailyNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sendNotification:day';

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
        $notificationId = 0;

        $current_date = date('Y-m-d H:i:s');

        // Send notification remaining 24 hours (Chores)
        $twentyFive = new \DateTime();
        $twentyFive->modify('+25 hours');
        $chorestwentyFour = $twentyFive->format('Y-m-d H:i:s');

        $twentyThree = new \DateTime();
        $twentyThree->modify('+23 hours');
        $chorestwentyThree = $twentyThree->format('Y-m-d H:i:s');

        // Send notification remaining 12 hours (Chores)
        $eleven = new \DateTime();
        $eleven->modify('+11 hours');
        $chorestwelve = $eleven->format('Y-m-d H:i:s');

        $fourteen = new \DateTime();
        $fourteen->modify('+14 hours');
        $choresfourteen = $fourteen->format('Y-m-d H:i:s');

        // Send notification remaining 6 hours (Chores)
        $seven = new \DateTime();
        $seven->modify('+7 hours');
        $choresSeven = $seven->format('Y-m-d H:i:s');

        $five = new \DateTime();
        $five->modify('+5 hours');
        $choresfive = $five->format('Y-m-d H:i:s');

        // Send notification remaining 3 hours (Chores)
        $four = new \DateTime();
        $four->modify('+4 hours');
        $choresfour = $four->format('Y-m-d H:i:s');

        $two = new \DateTime();
        $two->modify('+2 hours');
        $choresTwo = $two->format('Y-m-d H:i:s');

         // Send notification remaining 1 hours (Chores)
        $one = new \DateTime();
        $one->modify('+1 hours');
        $choresone = $one->format('Y-m-d H:i:s');

        // -------------------- Begin Send notification remaining 24 hours (Chores) --------------------
        $recordtwentyFour = Chores::select('cho_id','cho_title','cho_set_time','use_username','id as used_id','use_fcm_token','use_device_type')->join('users','tbl_chores_list.cho_child_id','users.id')->where('cho_status',0)->whereBetween('cho_set_time',[$chorestwentyThree,$chorestwentyFour])->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();

        if(!$recordtwentyFour->isEmpty())
        {
            $content = "Chore expiring in 24 Hours";
            $type = "twenty_four_hours_remaining_chores";
            foreach ($recordtwentyFour as $key => $value) 
            {
                if(StatusNotification::where('sno_chores_id',$value->cho_id)->where('sno_is_twenty_four',1)->exists())
                {

                }else{
                    $updateReq['sno_is_twenty_four'] = 1;
                    $update = StatusNotification::where('sno_chores_id',$value->cho_id)->update($updateReq);

                    $this->notification($value['use_fcm_token'],$value->cho_title,$content,$type,$notificationId,$value['use_device_type']);
                }
            
            }
        }

        // -------------------- End Send notification remaining 24 hours (Chores) --------------------


        $recordtwelve = Chores::select('cho_id','cho_title','cho_set_time','use_username','id as used_id','use_fcm_token','use_device_type')->join('users','tbl_chores_list.cho_child_id','users.id')->where('cho_status',0)->whereBetween('cho_set_time',[$chorestwelve,$choresfourteen])->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();

        if(!$recordtwelve->isEmpty())
        {
            $content = "Chore expiring in 12 Hours";
            $type = "twelve_hours_remaining_chores";
            foreach ($recordtwelve as $key => $value) 
            {
                if(StatusNotification::where('sno_chores_id',$value->cho_id)->where('sno_is_twelve',1)->exists())
                {

                }else{
                    $updateReq['sno_is_twelve'] = 1;
                    $update = StatusNotification::where('sno_chores_id',$value->cho_id)->update($updateReq);
                    $this->notification($value['use_fcm_token'],$value->cho_title,$content,$type,$notificationId,$value['use_device_type']);
                }
            
            }
        }

        $recordsix = Chores::select('cho_id','cho_title','cho_set_time','use_username','id as used_id','use_fcm_token','use_device_type')->join('users','tbl_chores_list.cho_child_id','users.id')->where('cho_status',0)->whereBetween('cho_set_time',[$choresfive,$choresSeven])->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();

        if(!$recordsix->isEmpty())
        {
            $content = "Chore expiring in 6 Hours";
            $type = "six_hours_remaining_chores";
            foreach ($recordsix as $key => $value) 
            {
                if(StatusNotification::where('sno_chores_id',$value->cho_id)->where('sno_is_six',1)->exists())
                {

                }else{
                    $updateReq['sno_is_six'] = 1;
                    $update = StatusNotification::where('sno_chores_id',$value->cho_id)->update($updateReq);

                    $this->notification($value['use_fcm_token'],$value->cho_title,$content,$type,$notificationId,$value['use_device_type']);
                }
            }
        }

        $recordThree = Chores::select('cho_id','cho_title','cho_set_time','use_username','id as used_id','use_fcm_token','use_device_type')->join('users','tbl_chores_list.cho_child_id','users.id')->where('cho_status',0)->whereBetween('cho_set_time',[$choresTwo,$choresfour])->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();

        if(!$recordThree->isEmpty())
        {
            $content = "Chore expiring in 3 Hours";
            $type = "three_hours_remaining_chores";
            foreach ($recordThree as $key => $value) 
            {
                if(StatusNotification::where('sno_chores_id',$value->cho_id)->where('sno_is_three',1)->exists())
                {

                }else{
                    $updateReq['sno_is_three'] = 1;
                    $update = StatusNotification::where('sno_chores_id',$value->cho_id)->update($updateReq);

                    $this->notification($value['use_fcm_token'],$value->cho_title,$content,$type,$notificationId,$value['use_device_type']);
                }
            
            }
        }

        $recordOne = Chores::select('cho_id','cho_title','cho_set_time','use_username','id as used_id','use_fcm_token','use_device_type')->join('users','tbl_chores_list.cho_child_id','users.id')->where('cho_status',0)->whereBetween('cho_set_time',[$choresone,$choresTwo])->orderBy(DB::raw("(DATE_FORMAT(cho_set_time,'%Y-%m-%d %H:%i:%s'))"),'ASC')->get();

        if(!$recordOne->isEmpty())
        {
            $content = "Chore expiring in 1 Hours";
            $type = "one_hour_remaining_chores";
            foreach ($recordOne as $key => $value) 
            {
                if(StatusNotification::where('sno_chores_id',$value->cho_id)->where('sno_is_one',1)->exists())
                {

                }else{
                    $updateReq['sno_is_one'] = 1;
                    $update = StatusNotification::where('sno_chores_id',$value->cho_id)->update($updateReq);

                    $this->notification($value['use_fcm_token'],$value->cho_title,$content,$type,$notificationId,$value['use_device_type']);
                }
            }
        }
    }

    public function notification($token, $choreName,$content,$type,$notificationId,$deviceType)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $token = $token;

        $fcmNotification = array();

        if($deviceType == 1) // iOs
        {

            $notification = array(
                'body' => $choreName,
                'title' => $choreName.' '. $content.' - ChoreUp',
                'sound' => "default",
                'color' => "#203E78",
                'type' => $type,
                'notification_id' => $notificationId,
                'mutable-content' => 1
            );

            $fcmNotification = array(
                'registration_ids' => array($token),
                'priority' => 'high',
                'aps'=>array('alert'=>array('title'=>'test','body'=>'body'), 'content-available'=>1,'mutable_content' =>1),
                'type' => $type,
                'mutable-content' => 1,
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
                    'mutable-content' => 1
                )
            );
        }

        if($deviceType == 2) // Andriod
        {
            $notification = [
                'date'      => date('d-m-Y H:i:s'),
                'title'     => $choreName.' '. $content.' - ChoreUp',
                'body'      => $choreName,
                'sound'     => "default",
                'color'     => "#203E78",
                'type'      => $type,
                'message'   => $choreName,
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
