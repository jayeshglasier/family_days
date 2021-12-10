<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\UserType;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\ForgetPassword;
use App\Exports\UsersExport;
use App\Helper\Exceptions;
use App\Helper\UserRights;
use App\User;
use App\Model\Chores;
use App\Model\Rewards;
use Auth;
use Session;
use Input;
use Mail;
use PDF;

class UsersController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try
        {
            $data['i'] = 1;
            $data['searchdata'] = Input::get('searchdata');
            $searchdata = Input::get('searchdata');
            $data['pageGoto'] = Input::get('page');
            $pageFilter = Input::get('pagefilter');
            if($pageFilter)
            {
                $data['pages'] = Input::get('pagefilter');
                $pages = Input::get('pagefilter');
            }else{
                $data['pages'] = 10;
                $pages = 10;
            }

            $pageOrderBy = Input::get('Asc_Desc_Record');
            if($pageOrderBy)
            {
                $data['pageOrder'] = Input::get('Asc_Desc_Record');
                $pageOrder = Input::get('Asc_Desc_Record');
            }else{
                $data['pageOrder'] = "DESC";
                $pageOrder = "DESC";
            }

            $pageOrderBySelect = Input::get('Asc_Desc_Select');
            if($pageOrderBySelect)
            {
                $data['pageDescSelect'] = Input::get('Asc_Desc_Select');
                $pageAsc_Desc = Input::get('Asc_Desc_Select');
            }else{
                $data['pageDescSelect'] = "id";
                $pageAsc_Desc = "id";
            }

            if($searchdata)
            {   
                $data['datarecords'] = User::join('tbl_roles','users.use_role','tbl_roles.rol_id')
                ->where('use_username','like','%'.$searchdata.'%')
                ->Orwhere('email','like','%'.$searchdata.'%')
                ->Orwhere('use_family_name','like','%'.$searchdata.'%')
                ->Orwhere('use_full_name','like','%'.$searchdata.'%')
                ->Orwhere('use_phone_no','like','%'.$searchdata.'%')
                ->where('use_role','<>',1)
                ->orderBy($pageAsc_Desc,$pageOrder)
                ->paginate($pages);

            }else{
                $data['datarecords'] = User::join('tbl_roles','users.use_role','tbl_roles.rol_id')->where('use_role','<>',1)->orderBy($pageAsc_Desc,$pageOrder)->paginate($pages);
            }
            return view('users-mgmt.index',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {   
        $data['updatedata'] = User::join('tbl_roles','users.use_role','tbl_roles.rol_id')->where('use_family_id',$id)->first();
        return view('users-mgmt/edit',$data);
    }

    public function changepassword($id)
    {
        $data['user'] = User::where('Use_Id',$id)->first();
        $data['menu'] = UserRights::manu();
        $data['CURight'] = UserRights::rights();
        return view('users-mgmt/password',$data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function update(Request $request)
    {
        try
        {  
            $this->validateUpdate($request);
            if($request->file('profile_file'))
            {
                $images = $request->file('profile_file');
                $imagesname = str_replace(' ', '',$images->getClientOriginalName());
                $images->move(public_path('images/user-images/'),$imagesname);
            }else{
                $selectImages = User::where('id',$request->use_id)->select(['use_image'])->first();
                if($selectImages)
                {
                    $imagesname = $selectImages->use_image;
                }else{
                    $imagesname = '';   
                }
            }
                $updateData['use_full_name'] = $request->use_full_name ? $request->use_full_name:'';
                $updateData['email'] = $request->email ? $request->email:'';
                $updateData['use_phone_no'] = $request->use_phone_no ? $request->use_phone_no:'';

                if($request->use_dob)
                {
                    $birthDate = date('Y-m-d', strtotime(str_replace('/', '-', $request['use_dob'])));
                }else{
                    $birthDate = null;
                }
                $updateData['use_dob'] = $birthDate;
                $updateData['use_image'] = $imagesname;
                $updateData['updated_at'] = date('Y-m-d H:i:s');
                User::where('id',$request->use_id)->update($updateData);
                Session::flash('success', 'User Profile Update SuccessFully..!');

                if($request->user_profile == "user_profile")
                {
                    return redirect()->intended('/users');
                }else if($request->use_family == "use_family"){
                    return redirect()->intended('/familys');
                }else{
                     return redirect()->intended('/users');
                }
                

        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    private function validateUpdate($request)
    {
        $this->validate($request, [
        'use_full_name' => 'required',
        ]);   
    }

    public function updatepassword(Request $request)
    {
        $this->validatePassword($request);

        $usersData['password'] = bcrypt($request['password']);
        $usersData['updated_at'] = date('Y-m-d H:i:s');
        $usersData['use_is_reset'] = 1;
        $userUpdate = User::where('id',$request->use_id)->update($usersData);
        $password = $request['password'];

        if($userUpdate)
        {
            $userDetail = DB::table('users')->select('id as user_id','email','use_token as token','use_username','use_full_name as full_name','use_image','use_role','use_is_admin','use_parents_id','use_fam_unique_id')->where('id',$request->use_id)->first();

            $userDetails = array("user_id" => $userDetail->user_id,"email" => $userDetail->email,"username" => $userDetail->use_username,"full_name" => $userDetail->full_name,"role" => $userDetail->use_role,"is_admin" => $userDetail->use_is_admin);

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
            Session::flash('success', 'Success! user password changed and mail send to user');
           return redirect('users');
        }else{
            return redirect('users')->with('User password updation Fail');
        }
            
    }

    private function validatePassword($request)
    {
        $this->validate($request, [
        'password' => 'required|min:5',
        ]);    
    }

    public function destroy($id)
    { 
        if(User::where('use_family_id',$id)->exists())
        {
            $userData = User::select('id','use_fam_unique_id','use_total_member')->where('use_family_id',$id)->first();
            User::where('use_family_id',$id)->delete();
            Chores::where('cho_child_id',$userData->id)->delete();
            Rewards::where('red_child_id',$userData->id)->delete();

            $updateData['use_total_member'] = $userData->use_total_member -1;
            $update = User::where('use_fam_unique_id',$userData->use_fam_unique_id)->update($updateData);
            Session::flash('success', 'User delete SuccessFully..!');
            return redirect()->intended('/users');
        }else{
          Session::flash('error', 'User is not Deleted..!');
          return redirect()->intended('/users');
        }
    }

    public function memberDestroy($id)
    {   
        if(User::where('use_family_id',$id)->exists())
        {
            $userData = User::select('id','use_parents_id','use_fam_unique_id','use_total_member')->where('use_family_id',$id)->first();
            $parentsData = User::select('use_family_id')->where('id',$userData->use_parents_id)->first();
            User::where('use_family_id',$id)->delete();
           
            $updateData['use_total_member'] = $userData->use_total_member -1;
            $update = User::where('use_fam_unique_id',$userData->use_fam_unique_id)->update($updateData);

            Session::flash('success', 'User delete SuccessFully..!');
            return redirect()->intended('/view-family-detail/'.$parentsData->use_family_id);
        }else{
          Session::flash('error', 'User is not Deleted..!');
          return redirect()->intended('/view-family-detail/'.$id);
        }
    }

    // User Active = 0 and Inactive = 1 
    public function updateStatus(Request $request)
    {
        try
        {  
            if($request->mode == "true")
            {
                $userStatus = User::where('id',$request->user_id)->update(array('use_status' => 0));
                $data['status'] = "true";
                return $data;
            }
            else
            {
                $userStatus = User::where('id',$request->user_id)->update(array('use_status' => 1));
                 $data['status'] = "false";
                return $data;
            }

        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    public function view($id)
    {
        $data['viewdata'] = User::where('Use_User_Id',$id)->join('tbl_types','tbl_users.Use_User_Type','=','tbl_types.Utp_Id')->orderBy('Use_Id','DSC')->first();
        $data['menu'] = UserRights::manu();
        $data['CURight'] = UserRights::rights();
        return view('users-mgmt.view-user-detail',$data);
    }
    
    public function exportExcel()
    {
         return Excel::download(new UsersExport, 'users.xlsx');
    }

    public function exportPdf()
    {
        $data['datarecords'] = User::join('tbl_roles','users.use_role','tbl_roles.rol_id')->where('use_role','<>',1)->orderBy('id','desc')->get();
        $pdf = PDF::loadView('users-mgmt.user-report-pdf', $data);

        $todayDate = date('d-m-Y');
        return $pdf->download('user-report-'.$todayDate.'.pdf');
    }

    public function userprofile()
    {
        $data['user'] = User::where('id',Auth::user()->id)->first(); 
        return view('users-mgmt.user_profile',$data);
    }

    public function updateprofile(Request $request)
    {
        $this->validateProfileUpdate($request);
        try
        {  
            $updateData['use_full_name'] = $request['use_full_name'];
            $updateData['email'] = $request['email'];
            $updateData['use_phone_no'] = $request['use_phone_no'];
            $updateData['use_status'] = 0;
            $updateData['updated_at'] = date('Y-m-d H:i:s');

            Session::flash('success', 'Profile Update SuccessFully..!');
            User::where('id',$request->id)->update($updateData);

            return redirect()->intended('/user-profile');
            
        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    private function validateProfileUpdate($request)
    {
        $this->validate($request, [
        'use_full_name' => 'required|max:200',
        'email' => 'required|max:200',
        'use_phone_no' => 'required|max:20',
        ]);   
    }

    public function updateAdminpassword(Request $request)
    {
        $this->validateAdminPassword($request);

        $usersData['password'] = bcrypt($request['password']);
        $usersData['updated_at'] = date('Y-m-d H:i:s');
        $passwordUpdate = User::where('id',$request->id)->update($usersData);
        if($passwordUpdate)
        {
            Session::flash('success', 'Password updated');
           return redirect('user-profile');
        }else{
            return redirect('user-profile')->with("Password isn't updated");
        }
            
    }

    private function validateAdminPassword($request)
    {
        $this->validate($request, [
        'password' => 'required|min:5|confirmed',
        ]);    
    }
}
