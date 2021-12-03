<?php

namespace App\Http\Controllers\RestApi;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Validator;
use Illuminate\Http\Request;
use App\Helper\ResponseMessage;
use App\Helper\Exceptions;
use App\Mail\UserRegistered;
use App\Model\AdminContact;
use App\User;
use Mail;
use DB;

class UserController extends Controller
{

    public function profileEdit(Request $request) 
    {
        try
        {
            $rules = [
                'fullname' => 'required',
                'email' => 'required',
                'phone_no' => 'required',
                'role' => 'required',
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
                $header = $request->header('token');

                if($header)
                {
                    if(User::where('use_token',$header)->exists())
                    {   
                        if(User::where('use_token',$header)->where('use_status',0)->exists())
                        {
                            $userDetail = User::where('use_token',$header)->where('use_status',0)->first();

                            $update_req['use_full_name'] = $request->fullname ? $request->fullname:$userDetail->use_full_name;
                            $update_req['email'] = $request->email ? $request->email:$userDetail->email;
                            if($request->password)
                            {
                             $update_req['password'] = bcrypt($request->password);   
                            }
                            $update_req['use_phone_no'] = $request->phone_no ? $request->phone_no:$userDetail->use_phone_no;
                            $update_req['use_dob'] = $request->birth_date ? $request->birth_date:$userDetail->use_dob;
                            $update_req['use_role'] = $request->role ? $request->role:$userDetail->use_role;
                            $update_req['use_is_admin'] = $request->is_admin ? $request->is_admin:$userDetail->use_is_admin;
                            $update_req['updated_at'] = date('Y-m-d H:i:s');
                            $update = User::where('use_token',$header)->update($update_req);

                            if($update)
                            {   
                                $usertDetail = DB::table('users')->select('id as user_id','email','use_token as token','use_full_name as full_name','use_image','use_role','use_is_admin')->where('use_token',$header)->first();

                        if($usertDetail->use_image)
                        {
                            $profileurl = url("public/images/user-images/".$usertDetail->use_image);
                        }else{
                            $profileurl = url("public/images/user-images/user-profile.png");
                        }

                         $userDetails = array("email" => $usertDetail->email, "token" => $usertDetail->token,"full_name" => $usertDetail->full_name,"role" => $usertDetail->use_role,"is_admin" => $usertDetail->use_is_admin,"profile_url" => $profileurl);

                        $message = "Profile update successfully.";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $message, 'data'=> $userDetails],JSON_UNESCAPED_SLASHES);

                            }else{
                                $msg = "Failed to update. please try again.";
                                return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                            }
                        }else{
                            $msg = "Your account isn't active.";
                            return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                        }
                    }
                    else{
                        $msg = "Token isn't valid!";
                        return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                    }
                }else{
                    $msg = "Token isn't found!";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }

        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function editProfileImage(Request $request) 
    {
        try
        {
            $header = $request->header('token');

            if($header)
            {
                if(User::where('use_token',$header)->exists())
                {
                    if(User::where('use_token',$header)->where('use_status',0)->exists())
                    {
                        $rules = [
                            'profile_file' => 'required|mimes:jpeg,png,jpg|max:5024',
                            ];

                        $validator = Validator::make($request->all(), $rules);

                        if($validator->fails())
                        {
                            $errors = $validator->errors();
                            foreach ($errors->all() as $message) {                
                                return json_encode(['status' => false, 'error' => 401, 'message' => $message],JSON_UNESCAPED_SLASHES);
                            }
                        }else{

                            if($request->file('profile_file'))
                            {
                                $images = $request->file('profile_file');
                                $imagesname = "USER_IMG_".date('Ymd')."_".time().'.'.$images->getClientOriginalExtension();
                                $images->move(public_path('images/user-images/'),$imagesname);
                            }
                            $update_req['use_image'] = $imagesname;
                            $update_req['updated_at'] = date('Y-m-d H:i:s');
                            $update = User::where('use_token',$header)->update($update_req);


                        $usertDetail = DB::table('users')->select('id as user_id','email','use_token as token','use_full_name as full_name','use_image','use_role','use_is_admin')->where('use_token',$header)->first();

                        if($usertDetail->use_image)
                        {
                            $profileurl = url("public/images/user-images/".$usertDetail->use_image);
                        }else{
                            $profileurl = url("public/images/user-images/user-profile.png");
                        }

                         $userDetails = array("profile_url" => $profileurl);

                        $message = "Profile picture is updated.";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $message, 'data'=> $userDetails],JSON_UNESCAPED_SLASHES);
                        }
                    }else
                    {
                        $msg = "Your account isn't active.";
                        return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                    }
                }
                else{
                    $msg = "Token isn't valid!";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{
                $msg = "Token is required!";
                return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }

    }

    public function addfamilyMember(Request $request)
    {
        try
        {
            $header = $request->header('token');

            if($header)
            {
                if(User::where('use_token',$header)->exists())
                {
                    if(User::where('use_token',$header)->where('use_status',0)->exists())
                    {
                        if(User::where('use_username',$request->username)->exists())
                        {
                            $dataObject = (object) [];
                            $message = "Username already exist";
                            return json_encode(['status' => true, 'error' => 401, 'message' => $message, 'data'=> $dataObject],JSON_UNESCAPED_SLASHES);
                        }
                        else
                        {
                            $rules = [
                                'fullname' => 'required|max:100',
                                'username' => 'required|max:100',
                                'password' => 'required|max:30',
                                'role' => 'required',
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
                                if($request->file('profile_file'))
                                {
                                    $images = $request->file('profile_file');
                                    $imagesname = "USER_IMG_".date('Ymd')."_".time().'.'.$images->getClientOriginalExtension();
                                    $images->move(public_path('images/user-images/'),$imagesname);
                                }else{
                                    $imagesname = 'user-profile.png';
                                }

                                $usersRecord = DB::table('users')->select('id as user_id','use_fam_unique_id','email','use_token as token','use_full_name as full_name','use_image','use_role','use_family_name','use_total_member','use_parents_id','use_is_admin')->where('use_token',$header)->first();

                                // *********************** Begin Default Store Dispaly Id ***********************
                                // Display_id Function
                                $lastId = User::select('id as user_id')->orderBy('id','DESC')->limit(1)->first();
                                
                                if($lastId)
                                {
                                    $id = $lastId->user_id;
                                }else{
                                    $id = "00000";
                                }

                                if ($id<=129999)
                                {
                                    $num     = $id;
                                    $letters = range('A', 'Z');
                                    $letter  = (int) $num / 5000;
                                    $num     = $num % 5000 + 1;
                                    $num     = str_pad($num, 4, 0, STR_PAD_LEFT);
                                    $display_id =  'FAM'.$letters[$letter] . $num;
                                }

                            // *********************** End Default Store Dispaly Id ***********************

                                $insertData = new User;
                                $insertData->use_family_id = $display_id;
                                $insertData->use_fam_unique_id = $usersRecord->use_fam_unique_id;
                                $insertData->use_family_name = $usersRecord->use_family_name;
                                $insertData->use_username  = $request->username;
                                $insertData->use_full_name = $request->fullname ? $request->fullname:'';
                                $insertData->email = $request->email ? $request->email:'';
                                $insertData->use_phone_no = $request->phone_no ? $request->phone_no:'';
                                $insertData->password = bcrypt($request->password);
                                $insertData->use_token = str_random(90);
                                $insertData->use_total_member = 0;
                                $insertData->remember_token = str_random(90);             
                                $insertData->use_fcm_token = '';
                                if($request->birth_date)
                                {
                                    $birthDate = date('Y-m-d', strtotime(str_replace('/', '-', $request['birth_date'])));
                                }else{
                                    $birthDate = null;
                                }
                                $insertData->use_dob = $birthDate;
                                $insertData->use_role = $request->role;
                                $insertData->use_status = 0;
                                $insertData->use_total_point = 0;
                                $insertData->use_parents_id = $usersRecord->user_id;
                                $insertData->use_is_admin = $request->is_admin ? $request->is_admin:0;
                                $insertData->use_image = $imagesname;
                                $insertData->created_at = date('Y-m-d H:i:s');
                                $insertData->updated_at = date('Y-m-d H:i:s');
                                $insertData->email_verified_at = date('Y-m-d H:i:s');
                                $insertData->save();

                                $updateData['use_total_member'] = $usersRecord->use_total_member +1;
                                $update = User::where('use_fam_unique_id',$usersRecord->use_fam_unique_id)->update($updateData);

                               $usertDetail = DB::table('users')->select('id as user_id','use_fam_unique_id','email','use_token as token','use_username','use_full_name as full_name','use_image','use_role','use_is_admin','use_family_name','use_dob','use_phone_no','created_at')->orderBy('id','DESC')->first();

                                if($usertDetail->use_image)
                                {
                                    $profileurl = url("public/images/user-images/".$usertDetail->use_image);
                                }else{
                                    $profileurl = url("public/images/user-images/user-profile.png");
                                }

                                $userDetails = array("username" => $usertDetail->use_username,"full_name" => $usertDetail->full_name,"email" => $usertDetail->email, "token" => $usertDetail->token,"role" => $usertDetail->use_role,"profile_url" => $profileurl);

                                $adminDetails = array("user_id" => $usertDetail->user_id,"email" => $usertDetail->email, "token" => $usertDetail->token,"username" => $usertDetail->use_username,"full_name" => $usertDetail->full_name,"role" => $usertDetail->use_role,"is_admin" => $usertDetail->use_is_admin,"use_family_name" => $usertDetail->use_family_name,"use_dob" => $usertDetail->use_dob,"use_phone_no" => $usertDetail->use_phone_no,"created_at" => $usertDetail->created_at,"profile_url" => $profileurl);

                                if($usertDetail->use_role == 2 || $usertDetail->use_role == 3)
                                {
                                    $uemailRecord = DB::table('users')->select('email')->whereBetween('use_role',[2,3])->where('use_fam_unique_id',$usertDetail->use_fam_unique_id)->get();
                                }

                                else if($usertDetail->use_role == 4)
                                {
                                    $uemailRecord = DB::table('users')->select('email')->whereIn('use_role',[2,3,4])->where('use_fam_unique_id',$usertDetail->use_fam_unique_id)->get();
                                }

                                else if($usertDetail->use_role == 5)
                                {
                                    $uemailRecord = DB::table('users')->select('email')->whereIn('use_role',[2,3,5])->where('use_fam_unique_id',$usertDetail->use_fam_unique_id)->get();
                                }

                                $userEmails = array();

                                foreach ($uemailRecord as $key => $value) {
                                    if($value->email)
                                    {
                                        $userEmails[] = $value->email;
                                    }
                                }

                                $data = ['userdetail' => $adminDetails];
                                $email = $userEmails;
                                Mail::to($email)->send(new UserRegistered($data));

                                $message = "Family member added successfully";
                                return json_encode(['status' => true, 'error' => 200, 'message' => $message, 'data'=> $userDetails],JSON_UNESCAPED_SLASHES);
                            }
                        }
                    }else
                    {
                        $msg = "Your account isn't active.";
                        return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                    }
                }
                else{
                    $msg = "Token isn't valid!";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{
                $msg = "Token is required!";
                return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function familymemberList(Request $request)
    {
        try
        {
            $header = $request->header('token');

            if($header)
            {
                if(User::where('use_token',$header)->exists())
                {
                    if(User::where('use_token',$header)->where('use_status',0)->exists())
                    {   
                        $usertDetail = DB::table('users')->select('id as user_id','email','use_token as token','use_full_name as full_name','use_image','use_role','use_parents_id','use_is_admin','use_fam_unique_id')->where('use_token',$header)->first();
                        
                        $userRecords = DB::table('users')->select('id as user_id','email','use_token as token','use_full_name as full_name','use_image','use_role','use_is_admin','use_username','use_total_point')->where('use_fam_unique_id',$usertDetail->use_fam_unique_id)->orderBy('use_role','ASC')->get();

                        if(!$userRecords->isEmpty())
                        { 
                        $userDetails = array();
                        foreach ($userRecords as $key => $value)
                        { 
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

                            $userDetails[] = array(
                                "child_id" => $value->user_id,
                                "username" => $value->use_username,
                                "full_name" => $value->full_name,
                                "email" => $value->email,
                                "token" => $value->token,
                                "role" => $value->use_role,
                                "role_type" => $userType,
                                "is_admin" => $isAdmin,
                                "reward_point" => $value->use_total_point,
                                "profile_url" => $profileurl);
                        }

                        array_walk_recursive($userDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $userDetails;

                        $message = "Family member list";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $message, 'data'=> $this->data[$key]],JSON_UNESCAPED_SLASHES);

                        }else{
                          $msg = "No any child found";
                          return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => array()],JSON_UNESCAPED_SLASHES);
                    }
                    }else
                    {
                        $msg = "Your account isn't active.";
                        return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                    }
                }
                else{
                    $msg = "Token isn't valid!";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{
                $msg = "Token is required!";
                return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function childList(Request $request)
    {
        try
        {
            $header = $request->header('token');

            if($header)
            {
                if(User::where('use_token',$header)->exists())
                {
                    if(User::where('use_token',$header)->where('use_status',0)->exists())
                    {   
                        $usertDetail = DB::table('users')->select('id as user_id','use_fam_unique_id')->where('use_token',$header)->first();

                        $userRecords = DB::table('users')->select('id as user_id','email','use_token as token','use_full_name as full_name','use_image','use_role','use_is_admin')->where('use_fam_unique_id',$usertDetail->use_fam_unique_id)->where('use_role','>',3)->get();

                        if(!$userRecords->isEmpty())
                        { 
                        $userDetails = array();
                        foreach ($userRecords as $key => $value)
                        { 
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

                            $userDetails[] = array("child_id" => $value->user_id,"full_name" => $value->full_name);
                        }

                        array_walk_recursive($userDetails, function (&$item, $key) {
                        $item = null === $item ? '' : $item;
                        });
                        $this->data[$key] = $userDetails;

                        $message = "Family member list";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $message, 'data'=> $this->data[$key]],JSON_UNESCAPED_SLASHES);

                        }else{
                          $msg = "No any child found";
                          return json_encode(['status' => true, 'error' => 200, 'message' => $msg,'data' => array()],JSON_UNESCAPED_SLASHES);
                    }
                    }else
                    {
                        $msg = "Your account isn't active.";
                        return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                    }
                }
                else{
                    $msg = "Token isn't valid!";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{
                $msg = "Token is required!";
                return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }


    public function getUserdetails(Request $request)
    {
        try
        {   
            $header = $request->header('token');

            if($header)
            {
                if(User::where('use_token',$header)->exists())
                {
                    if(User::where('use_token',$header)->where('use_status',0)->exists())
                    {   
                        $users = DB::table('users')->select('use_family_name','id as user_id','email','use_token as token','use_full_name as full_name','use_username','use_image','use_role','use_dob','use_is_admin','use_phone_no','use_total_point')->where('use_token',$header)->first();

                            if($users->use_image)
                            {
                                $profileurl = url("public/images/user-images/".$users->use_image);
                            }else{
                                $profileurl = url("public/images/user-images/user-profile.png");
                            }

                            if($users->use_dob)
                            {
                                $dobdate = $users->use_dob;
                            }else{
                                $dobdate = '';
                            }
                       
                            $userDetails = array("family_name" => $users->use_family_name,"user_id" => $users->user_id,"username" => $users->use_username,"full_name" => $users->full_name,"email" => $users->email,"phone_no" => $users->use_phone_no, "token" => $users->token,"role" => $users->use_role,"is_admin" => $users->use_is_admin,"birth_date" => $dobdate,"total_point" => $users->use_total_point,"profile_url" => $profileurl);

                        $message = "User personal details";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $message, 'data'=> $userDetails],JSON_UNESCAPED_SLASHES);
                    }else
                    {
                        $msg = "Your account isn't active.";
                        return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                    }
                }
                else{
                    $msg = "Token isn't valid!";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{
                $msg = "Token is required!";
                return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
            }
        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function childProfileUpdate(Request $request) 
    {
        try
        {
            $rules = [
                'fullname' => 'required',
                'role' => 'required',
                'child_id'=> 'required'
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
                $header = $request->header('token'); // Admin Token 

                if($header)
                {
                    if(User::where('use_token',$header)->exists())
                    {   
                        if(User::where('use_token',$header)->where('use_status',0)->exists())
                        {

                            $userDetail = User::where('use_token',$header)->where('use_status',0)->first();

                            $childDetail = User::where('id',$request->child_id)->where('use_status',0)->first();

                            if($userDetail->use_role == 2 || $userDetail->use_role == 3)
                            {
                                if($request->file('profile_file'))
                                {
                                    $images = $request->file('profile_file');
                                    $imagesname = "USER_IMG_".date('Ymd')."_".time().'.'.$images->getClientOriginalExtension();
                                    $images->move(public_path('images/user-images/'),$imagesname);
                                }else{
                                    $imagesname = $childDetail->use_image;
                                }
                                $update_req['use_username'] = $request->username ? $request->username:$childDetail->use_username;
                                $update_req['use_image'] = $imagesname;
                                $update_req['use_full_name'] = $request->fullname ? $request->fullname:$childDetail->use_full_name;
                                $update_req['email'] = $request->email ? $request->email:$childDetail->email;

                                $update_req['use_phone_no'] = $request->phone_no ? $request->phone_no:$childDetail->use_phone_no;
                                if($request->password)
                                {
                                    $update_req['password'] = bcrypt($request->password);   
                                }
                                $update_req['use_dob'] = $request->birth_date ? $request->birth_date:$childDetail->use_dob;
                                $update_req['use_role'] = $request->role ? $request->role:$childDetail->use_role;
                                $update_req['use_is_admin'] = $request->is_admin ? $request->is_admin:$childDetail->use_is_admin;
                                $update_req['updated_at'] = date('Y-m-d H:i:s');
                                $update = User::where('id',$request->child_id)->update($update_req);

                                if($update)
                                {   
                                    $message = "Profile update successfully.";
                                    return json_encode(['status' => true, 'error' => 200, 'message' => $message],JSON_UNESCAPED_SLASHES);

                                }else{
                                    $msg = "Failed to update. Please try again.";
                                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                                }
                            }else{
                                $msg = "You have not permission.";
                                return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                            }
                            
                        }else{
                            $msg = "Your account isn't active.";
                            return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                        }
                        
                    }
                    else{
                        $msg = "Token isn't valid!";
                        return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                    }

                }else{
                    $msg = "Token isn't found!";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }

        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }

    public function contactAdmin(Request $request,$userId=0,$imagesname='')
    {
        try
        {
            $rules = [
                'full_name' => 'required',
                'message' => 'required',
                ];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $errors = $validator->errors();
                foreach ($errors->all() as $message) {                
                    return json_encode(['status' => false, 'error' => 401, 'message' => $message],JSON_UNESCAPED_SLASHES);
                }
            }
            
            $header = $request->header('token'); // Admin Token 

            $userDetail = User::select('id')->where('use_token',$header)->where('use_status',0)->first();

            if($userDetail)
            {
                $userId = $userDetail->id;
            }

            if($request->file('media_file'))
            {
                $fileLink = str_random(20);
                $images = $request->file('media_file');
                $imagesname = str_replace(' ', '-',$fileLink.'admin.'. $images->getClientOriginalExtension());
                $images->move(public_path('images/contract-admin/'),$imagesname);
            }

            $insertData = new AdminContact;
            $insertData['amc_use_id'] = $userId;
            $insertData['amc_full_name'] = $request->full_name;
            $insertData['amc_phone'] = $request->phone ? $request->phone:'';
            $insertData['amc_email'] = $request->email ? $request->email:'';
            $insertData['amc_subject'] = $request->subject ? $request->subject:'';
            $insertData['amc_message'] = $request->message;
            $insertData['amc_media_file'] = $imagesname;
            $insertData['amc_status'] = 1;
            $insertData['amc_createat'] = date('Y-m-d H:i:s');
            $insertData['amc_updateat'] = date('Y-m-d H:i:s');
            $insertData->save();

            if($insertData)
            {   
                ResponseMessage::successMessage("Contact admin successfully.");
            }else{
                ResponseMessage::error("Failed to contact admin.");
            }

        }catch (\Exception $e) {    
            Exceptions::exception($e);
        }
    }
   
}
