<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\UserType;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\ForgetPassword;
use App\Exports\PreRewardNameExport;
use App\Helper\Exceptions;
use App\Helper\UserRights;
use App\User;
use App\Model\Chores;
use App\Model\PresetReward;
use Auth;
use Session;
use Input;
use PDF;

class RewardNameController extends Controller
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
                $data['pageOrder'] = "ASC";
                $pageOrder = "ASC";
            }

            $pageOrderBySelect = Input::get('Asc_Desc_Select');
            if($pageOrderBySelect)
            {
                $data['pageDescSelect'] = Input::get('Asc_Desc_Select');
                $pageAsc_Desc = Input::get('Asc_Desc_Select');
            }else{
                $data['pageDescSelect'] = "per_id";
                $pageAsc_Desc = "per_id";
            }

            if($searchdata)
            {   
                $data['datarecords'] = PresetReward::where('per_name','like','%'.$searchdata.'%')->orderBy('per_name',$pageOrder)->paginate($pages);
            }else{
                $data['datarecords'] = PresetReward::where('per_name','<>','')->orderBy('per_name',$pageOrder)->paginate($pages);
            }
            return view('admin.reward-name-pages.index',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        try
        {  
            $this->validateUpdate($request);
            $uniqueId = str_random(15).date("Ymd");
            
            $insertData = new PresetReward;
            $insertData['per_unique_id'] = $uniqueId;
            $insertData['per_name'] = $request->per_name;
            $insertData['per_status'] = $request->per_status ? $request->per_status:0;
            $insertData['per_createat'] = date('Y-m-d H:i:s');
            $insertData['per_updateat'] = date('Y-m-d H:i:s');
            $insertData->save();

            if($insertData)
            {
                Session::flash('success', 'Reward name created!');
                return redirect()->intended('/reward-name');
            }else{
                Session::flash('error', "Reward name isn't created!");
                return redirect()->intended('/reward-name');
            }
            
        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
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
            $updateData['per_name'] = $request->per_name;
            $updateData['per_status'] = $request->per_status;
            $updateData['per_updateat'] = date('Y-m-d H:i:s');
            $infoUpdate = PresetReward::where('per_id',$request->per_id)->update($updateData);
            Session::flash('success', 'Reward name updated!');
            return redirect()->intended('/reward-name');
        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    private function validateUpdate($request)
    {
        $this->validate($request, [
        'per_name' => 'required|max:200',
        ]);   
    }


    public function destroy($id)
    { 
        if(PresetReward::where('per_id',$id)->exists())
        {
            PresetReward::where('per_id',$id)->delete();
            Session::flash('success', 'Reward name deleted!');
            return redirect()->intended('/reward-name');
        }else{
          Session::flash('error', "Reward name isn't deleted!");
          return redirect()->intended('/reward-name');
        }
    }


    // User Active = 0 and Inactive = 1 
    public function changestatus(Request $request)
    {
        try
        {  
            if($request->mode == "true")
            {
                $presetStatus = PresetReward::where('per_id',$request->per_id)->update(array('per_status' => 0));
                $data['status'] = "true";
                return $data;
            }
            else
            {
                $presetStatus = PresetReward::where('per_id',$request->per_id)->update(array('per_status' => 1));
                 $data['status'] = "false";
                return $data;
            }

        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }
    
    public function exportExcel()
    {
         return Excel::download(new PreRewardNameExport, 'reward-name.xlsx');
    }

    public function exportPdf()
    {
        $data['datarecords'] = PresetReward::where('per_name','<>','')->orderBy('per_id','ASC')->get();
        $pdf = PDF::loadView('admin.reward-name-pages.pdf-file', $data);

        $todayDate = date('d-m-Y');
        return $pdf->download('reward-name-'.$todayDate.'.pdf');

    }
}
