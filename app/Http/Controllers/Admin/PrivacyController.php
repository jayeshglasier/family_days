<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Helper\Exceptions;
use App\Helper\UserRights;
use App\Model\AdminContact;
use Auth;
use Session;
use Input;

class PrivacyController extends Controller
{

    public function privacy()
    {
        try
        {
            return view('help.privacy');
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    public function contact()
    {
        try
        {
            return view('help.contact-us');
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    

}
