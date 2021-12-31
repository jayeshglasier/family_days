<?php
namespace App\Http\Controllers\RestApi;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Helper\ResponseMessage;
use App\Helper\Exceptions;
use App\Mail\ForgetPassword;
use App\Mail\UserRegistered;
use Carbon\Carbon;
use App\Model\Chores;
use App\Model\Rewards;
use App\User;
use Validator;
use Mail;
use Input;
use DB;
use Hash;

class AuthController extends Controller
{
    public function signUp(Request $request)
    {
        $dataObject = (object) [];
        if(User::where('email',$request->email)->exists())
        {
            $message = "Email already exists";
            return json_encode(['status' => true, 'error' => 401, 'message' => $message, 'data'=> $dataObject],JSON_UNESCAPED_SLASHES);
        }
        else if(User::where('use_username',$request->username)->exists())
        {
            $message = "Username already exists";
            return json_encode(['status' => true, 'error' => 401, 'message' => $message, 'data'=> $dataObject],JSON_UNESCAPED_SLASHES);
        }
        else
        {
            $rules = [
                'username' => 'required|max:100',
                'family_name' => 'required|max:100',
                'fullname' => 'required|max:100',
                'email' => 'required|max:100',
                'phone_no' => 'required|max:15',
                'password' => 'required|max:25',
                'role' => 'required', // 2 = Father 3 = Mother
                ];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $errors = $validator->errors();
                foreach ($errors->all() as $message) {
                    return json_encode(['status' => false, 'error' => 401, 'message' => $message,'data'=> array()],JSON_UNESCAPED_SLASHES);
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
                        $display_unique_id =  'FAMDAYS'.$letters[$letter] . $num;
                    }else{
                        $display_id = 'FAM'.date('YmdHis');
                    }

                // *********************** End Default Store Dispaly Id ***********************

                if(User::where('use_fcm_token',$request->device_token)->where('use_status',0)->exists())
                {
                    $update_req['use_fcm_token'] = '';
                    $update = User::where('use_fcm_token',$request->device_token)->update($update_req);
                }

                $insertData = new User;
                $insertData->use_family_id = $display_id;
                $insertData->use_fam_unique_id = $display_unique_id;
                $insertData->use_family_name = $request->family_name ? $request->family_name:'';
                $insertData->use_username  = $request->username;
                $insertData->use_full_name = $request->fullname ? $request->fullname:'';
                $insertData->email = $request->email ? $request->email:'';
                $insertData->use_phone_no = $request->phone_no ? $request->phone_no:'';
                $insertData->password = bcrypt($request->password);
                $insertData->use_token = str_random(90);
                $insertData->remember_token = str_random(90);
                if($request->birth_date)
                {
                    $birthDate = date('Y-m-d', strtotime(str_replace('/', '-', $request['birth_date'])));
                }else{
                    $birthDate = null;
                }
                $insertData->use_dob = $birthDate;
                $insertData->use_role = $request->role;
                $insertData->use_is_admin = 1;
                $insertData->use_is_family_head = 1;
                $insertData->use_parents_id = 0;
                $insertData->use_status = 0;  // 0 = Active 1 = Inactive
                $insertData->use_image = $imagesname;
                $insertData->use_total_member = 1;
                $insertData->use_total_point = 0;
                $insertData->use_fcm_token = $request->device_token ? $request->device_token:'';
                $insertData->use_device_type = $request->device_type ? $request->device_type:0;
                $insertData->created_at = date('Y-m-d H:i:s');
                $insertData->updated_at = date('Y-m-d H:i:s');
                $insertData->email_verified_at = date('Y-m-d H:i:s');
                $insertData->save();

               $userDetail = DB::table('users')->select('id as user_id','email','use_token as token','use_username','use_full_name as full_name','use_image','use_role','use_is_admin','use_family_name','use_dob','use_phone_no','created_at')->orderBy('id','DESC')->first();

                $profileurl = url("public/images/user-images/".$userDetail->use_image);

                $userDetails = array("user_id" => $userDetail->user_id,"email" => $userDetail->email, "token" => $userDetail->token,"username" => $userDetail->use_username,"full_name" => $userDetail->full_name,"role" => $userDetail->use_role,"is_admin" => $userDetail->use_is_admin,"profile_url" => $profileurl);

                $adminDetails = array("user_id" => $userDetail->user_id,"email" => $userDetail->email, "token" => $userDetail->token,"username" => $userDetail->use_username,"full_name" => $userDetail->full_name,"role" => $userDetail->use_role,"is_admin" => $userDetail->use_is_admin,"use_family_name" => $userDetail->use_family_name,"use_dob" => $userDetail->use_dob,"use_phone_no" => $userDetail->use_phone_no,"created_at" => $userDetail->created_at,"profile_url" => $profileurl);

                $data = ['userdetail' => $adminDetails];
                $email = [$userDetail->email];

                Mail::to($email)->send(new UserRegistered($data));

                $message = "You are successfully logged in";
                return json_encode(['status' => true, 'error' => 200, 'message' => $message, 'data'=> $userDetails],JSON_UNESCAPED_SLASHES);
            }
        }
    }

    public function signIn(Request $request)
    {
        try
        {
            $rules = [
                'username'  => 'required',
                'password'  => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);

            if($validator->fails())
            {
                $errors = $validator->errors();
                $emptyObject = (object) [];
                foreach ($errors->all() as $message) {                
                   return response()->json(['success' => false, 'error' => 401, 'message' => $message]);
                }
            }

            $username = $request->username;
            $password = $request->password;
            $user = User::select('email','password','use_token as token')->where('email','=',$username)->orwhere('use_username',$request->username)->first();

            if($user)
            {
                if(User::where('email',$request->username)->orwhere('use_username',$request->username)->where('use_status',0)->exists())
                {
                    $pass = $user->password;

                    if(Hash::check($password,$pass))
                    {
                        $userDetail = DB::table('users')->select('id as user_id','use_username','email','use_token as token','use_full_name as full_name','use_image','use_role','use_is_admin','use_is_reset')->where('email','=',$username)->orwhere('use_username',$request->username)->first();

                        if(User::where('use_fcm_token',$request['device_token'])->exists())
                        {
                            $update_fcm['use_fcm_token'] = null;
                            $update = User::where('use_fcm_token',$request['device_token'])->update($update_fcm);
                        }
                        
                        $update_req['use_fcm_token'] = $request['device_token'];
                        $update_req['use_device_type'] = $request['device_type'] ? $request['device_type']:0;
                        $update = User::where('use_username',$userDetail->use_username)->update($update_req);

                        if($userDetail->use_image)
                        {
                            $profileurl = url("public/images/user-images/".$userDetail->use_image);
                        }else{
                            $profileurl = url("public/images/user-images/user-profile.png");
                        }

                        $userDetails = array("user_id" => $userDetail->user_id,"username" => $userDetail->use_username,"full_name" => $userDetail->full_name,"email" => $userDetail->email, "token" => $userDetail->token,"role" => $userDetail->use_role,"is_admin" => $userDetail->use_is_admin,"profile_url" => $profileurl,'is_reset' => $userDetail->use_is_reset);

                        $todayDate = date('Y-m-d', strtotime("-3 days"));
                        $adminChores = Chores::select('cho_id')->whereDate('cho_date', '<',$todayDate)->where('cho_status',0)->where('cho_is_expired','<>','Completed')->get();

                        if(!$adminChores->isEmpty())
                        {
                            foreach ($adminChores as $key => $value)
                            {
                                $updateData['cho_status'] = 1; // 0 = Assigned Chore / 1 = Finished
                                $updateData['cho_is_complete'] = 0; // 0 = Complete 1 = Incompletes 2 = No any action
                                $updateData['cho_is_confirmation'] = 0;  // 0 = Not conform 1 = Conform
                                $updateData['cho_is_admin_complete'] = 2; // 0 = Complete 1 = Incompletes 2 = No any action
                                $updateData['cho_is_expired'] = "Expired"; //0 = Complete 1 = Incompletes 2 = No any action
                                $update = Chores::where('cho_id',$value->cho_id)->update($updateData);
                            }
                        }

                    $rewardExpiredRecords = Rewards::where('red_status',0)->whereDate('red_frame_date', '<',$todayDate)->orderby('red_frame_date','ASC')->get();

                    if(!$rewardExpiredRecords->isEmpty())
                    {
                        foreach ($rewardExpiredRecords as $key => $expvalue)
                        {
                            $updateReward['red_status'] = 1; // 0 = Active / 1 = Inactive
                            $updateReward['red_is_confirmation'] = 1;  // 0 = Yes 1 = No
                            $updateReward['red_is_expired'] = "Expired";
                            $updateReward['red_expired_date'] = $todayDate;
                            $update = Rewards::where('red_id',$expvalue->red_id)->update($updateReward);
                        }
                    }
                    $message = "You are successfully logged in";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $message, 'data'=> $userDetails],JSON_UNESCAPED_SLASHES);
                    }else{
                        ResponseMessage::error("Password isn't correct");
                    }
                }
                else{
                    ResponseMessage::error("Your account isn't active.");
                }
            }else{
                ResponseMessage::error("Username isn't correct.");
            }

        }catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    public function updatePassword(Request $request)
    {
        $rules = [
            'token' => 'required',
            'password' => 'required'
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
            if(User::where('use_token',$request->token)->exists())
            {
                if(User::where('use_token',$request->token)->where('use_status',0)->exists())
                {
                    $update_req['password'] = bcrypt($request->password);
                    $update = User::where('use_token',$request->token)->update($update_req);
                    $password = $request->password;

                    if($update){
                        $userDetail = DB::table('users')->select('id as user_id','email','use_token as token','use_username','use_full_name as full_name','use_image','use_role','use_is_admin','use_parents_id','use_fam_unique_id')->where('use_token',$request->token)->first();

                        $userDetails = array("user_id" => $userDetail->user_id,"email" => $userDetail->email, "token" => $userDetail->token,"username" => $userDetail->use_username,"full_name" => $userDetail->full_name,"role" => $userDetail->use_role,"is_admin" => $userDetail->use_is_admin);

                        if($userDetail->use_role == 2 || $userDetail->use_role == 3)
                        {
                            $uemailRecord = DB::table('users')->select('email')->whereBetween('use_role',[2,3])->where('use_fam_unique_id',$userDetail->use_fam_unique_id)->get();
                        }

                        else if($userDetail->use_role == 4)
                        {
                            $uemailRecord = DB::table('users')->select('email')->whereIn('use_role',[2,3,4])->where('use_fam_unique_id',$userDetail->use_fam_unique_id)->get();
                        }

                        else if($userDetail->use_role == 5)
                        {
                            $uemailRecord = DB::table('users')->select('email')->whereIn('use_role',[2,3,5])->where('use_fam_unique_id',$userDetail->use_fam_unique_id)->get();
                        }

                        $userEmails = array();

                        foreach ($uemailRecord as $key => $value) {
                            if($value->email)
                            {
                                $userEmails[] = $value->email;
                            }
                        }

                        $data = ['userdetail' => $userDetails,'password' => $password];
                        $email = $userEmails;
                        Mail::to($email)->send(new ForgetPassword($data));

                        $msg = "Password updated!";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                    }else{
                        ResponseMessage::error("Change password failed!");
                    }
                }else{
                    $msg = "Your account isn't active.";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{
                ResponseMessage::error("Token isn't valid.");
            }
        }
    }

    public function ForgetPassword(Request $request)
    {

        $rules = [
            'email' => 'required',
            ];
        $validator = Validator::make($request->all(), $rules);

        if($validator->fails())
        {
            $errors = $validator->errors();
            foreach ($errors->all() as $message) {
                ResponseMessage::error($message);
            }
        }else
        {
            if(User::where('email',$request->email)->orwhere('use_username',$request->email)->exists())
            {
                if(User::where('email',$request->email)->orwhere('use_username',$request->email)->where('use_status',0)->exists())
                {
                    $password = str_random(8);
                    $update_req['password'] = bcrypt($password);
                    $update_req['use_is_reset'] = 1;
                    $update = User::where('email',$request->email)->orwhere('use_username',$request->email)->update($update_req);

                    if($update){

                         $userDetail = DB::table('users')->select('id as user_id','use_fam_unique_id','email','use_token as token','use_username','use_full_name as full_name','use_image','use_role','use_is_admin','use_parents_id')->where('email',$request->email)->orwhere('use_username',$request->email)->first();

                        $userDetails = array("user_id" => $userDetail->user_id,"email" => $userDetail->email, "token" => $userDetail->token,"username" => $userDetail->use_username,"full_name" => $userDetail->full_name,"role" => $userDetail->use_role,"is_admin" => $userDetail->use_is_admin);

                        if($userDetail->use_role == 2 || $userDetail->use_role == 3)
                        {
                            $uemailRecord = DB::table('users')->select('email')->whereBetween('use_role',[2,3])->where('use_fam_unique_id',$userDetail->use_fam_unique_id)->get();
                        }

                        else if($userDetail->use_role == 4)
                        {
                            $uemailRecord = DB::table('users')->select('email')->whereIn('use_role',[2,3,4])->where('use_fam_unique_id',$userDetail->use_fam_unique_id)->get();
                        }

                        else if($userDetail->use_role == 5)
                        {
                            $uemailRecord = DB::table('users')->select('email')->whereIn('use_role',[2,3,5])->where('use_fam_unique_id',$userDetail->use_fam_unique_id)->get();
                        }

                        $userEmails = array();

                        foreach ($uemailRecord as $key => $value) {
                            if($value->email)
                            {
                                $userEmails[] = $value->email;
                            }
                        }

                        $data = ['userdetail' => $userDetails,'password' => $password];
                        $email = $userEmails;
                        Mail::to($email)->send(new ForgetPassword($data));
                        $msg = "Success! Please check your email for password";
                        return json_encode(['status' => true, 'error' => 200, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                    }else{
                        ResponseMessage::error("Failed to reset Password!");
                    }
                }else{
                    $msg = "Your account isn't active.";
                    return json_encode(['status' => false, 'error' => 401, 'message' => $msg],JSON_UNESCAPED_SLASHES);
                }
            }else{
                ResponseMessage::error("Username you entered is not valid.");
            }
        }
    }
}
