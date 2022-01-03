<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helper\ProjectHelper;
use Carbon\Carbon;

class UsersCollection extends ResourceCollection
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
            if($row->use_image){
                $profileurl = url("public/images/user-images/".$row->use_image);
            }else{
                $profileurl = url("public/images/user-images/user-profile.png");
            }

            if($row->use_role == 2)
            {
                $userType = "Father";
            }else if($row->use_role == 3){
                $userType = "Mother";
            }else if($row->use_role == 4){
                $userType = "Son";
            }else if($row->use_role == 5){
                $userType = "Daughter";
            }else{
                $userType = "";
            }

            if($row->use_is_admin == 1)
            {
                $isAdmin = "Admin";
            }else{
                $isAdmin = "";
            }

            return [
                "child_id"      => $row->user_id,
                "username"      => $row->use_username,
                "full_name"     => $row->full_name ? $row->full_name:'',
                "email"         => $row->email ? $row->email:'',
                "token"         => $row->token,
                "role"          => $row->use_role,
                "role_type"     => $userType,
                "is_admin"      => $isAdmin,
                "reward_point"  => $row->use_total_point ? $row->use_total_point:0,
                "profile_url"   => $profileurl
            ];
        });
        return $resultCollection;
    }
}
