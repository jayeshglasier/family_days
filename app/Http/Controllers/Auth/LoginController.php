<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Helper\Exceptions;
use App\User;
use Artisan;
use Session;
use Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public function login(Request $request)
    {   
        $this->loginValidatation($request);

        if(User::where('email',$request->email)->exists())
        {
            $userDetail = DB::table('users')->select('id as user_id','email','use_token as token','use_full_name as full_name','use_image','use_role')->where('email',$request->email)->first();

            $credentials = [
                'email' => $request['email'],
                'password' => $request['password'],
                'use_status' => 0,
                'use_role' =>1,
            ];
        }else{
            $credentials = [
                'email' => $request['email'],
                'password' => $request['password'],
                'use_status' => 1,
                'use_role' =>10,
            ];
        }


        $auth = Auth::attempt($credentials,false);
        
        if($auth)
        {
            return redirect('home');
        } 
        else
        {
            Session::flash('error', 'You have not right to login..!');
            return view('auth.login');
        }
    }

     private function loginValidatation($request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ],
        [
            'email.required' => 'User Email field is required.',
            'password.required' => 'Password field is required.',
        ]);    
    }
    public function __construct()
    {
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        $this->middleware('guest')->except('logout');
    }
}
