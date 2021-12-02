<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Model\SystemSetting;
use App\Helper\Exceptions;
use Session;
use Auth;

class SystemSettingController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $data['editdata'] = SystemSetting::where('sys_id',1)->first();
        return view('admin.system-setting-mgmt.index',$data);
    }

    public function store(Request $request)
    {
        $this->validateEditInput($request);

        try
        {
            if($request->file('sys_logo'))
            {
                $images = $request->file('sys_logo');
                $imagesname = str_replace(' ', '',$images->getClientOriginalName());
                $images->move(public_path('images/logo'),str_replace(' ', '',$imagesname));
            }else{
                $selectImages = SystemSetting::where('sys_id',1)->select(['sys_logo'])->first();
                $imagesname = $selectImages->sys_logo;
            }

            $updateData['sys_name'] = $request['sys_name'] ? $request['sys_name']:'Family Days';
            $updateData['sys_min_chores'] = $request['sys_min_chores'] ? $request['sys_min_chores'] : 10;
            $updateData['sys_max_chores'] = $request['sys_max_chores'] ? $request['sys_max_chores'] : 100;
            $updateData['sys_min_reward'] = $request['sys_min_reward'] ? $request['sys_min_reward'] : 10;
            $updateData['sys_max_reward'] = $request['sys_max_reward'] ? $request['sys_max_reward'] : 100;
            $updateData['sys_logo'] =  $imagesname;
            $updateData['sys_createdby'] = Auth::user()->id;
            $updateData['sys_updatedby'] = Auth::user()->id;
            $updateData['sys_createdat'] = date('Y-m-d H:i:s');
            $updateData['sys_updatedat'] = date('Y-m-d H:i:s');
            SystemSetting::where('sys_id',1)->update($updateData);
            Session::flash('success', 'System Detail Updated');
            return redirect()->intended('/setting');
        }catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    private function validateEditInput($request)
    {
        $this->validate($request, [
        'sys_name' => 'required',
        'sys_min_chores' => 'required|max:4',
        'sys_min_chores' => 'required|max:4',
        ],
        [   
            'sys_name.required' => 'System Name is required.',
            'sys_min_chores.required' => 'Minimum Chores Point is required.',
            'sys_min_chores.required' => 'Minimum Chores Point is required.',
        ]);  
    }
}