<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Carbon\Carbon;

class NotificationListCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $resultCollection = $this->collection->transform(function ($row) 
        {
            if($row->use_image)
            {
                $profileurl = url("public/images/user-images/".$row->use_image);
            }else{
                $profileurl = url("public/images/user-images/user-profile.png");
            }

            $date1 = $row->created_date;
            $date2 = date('Y-m-d H:i:s');
            $from = Carbon::createFromFormat('Y-m-d H:i:s', $date1);
            $to = Carbon::createFromFormat('Y-m-d H:i:s', $date2);
            $diff_in_minutes = $from->diffInMinutes($to);
            $difference = Carbon::now()->subMinutes($diff_in_minutes)->diffForHumans();

            return [
                "notification_id" => $row->notification_id,
                "type" => $row->not_type,
                "message" => $row->not_data ? $row->not_data:'',
                "content" => $row->not_content ? $row->not_content:'',
                "chores_id" => $row->not_chores_id,
                "reward_id" => $row->not_reward_id,
                "user_id" => $row->use_id,
                "token" => $row->token,
                "full_name" => $row->use_full_name ? $row->use_full_name:'',
                "username" => $row->not_child_name ? $row->not_child_name:'',
                "profile_url" => $profileurl,
                "send_time" => $difference,
                "is_read" => $row->not_is_read
            ];
        });
        return $resultCollection;
    }
}
