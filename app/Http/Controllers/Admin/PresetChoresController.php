<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Models\Admin\UserType;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\ForgetPassword;
use App\Exports\PreChoreesExport;
use App\Helper\Exceptions;
use App\Helper\UserRights;
use App\User;
use App\Model\Chores;
use App\Model\SubBrands;
use App\Model\PresetChores;
use App\Model\ChoreIcon;
use Auth;
use Session;
use Input;
use PDF;

class PresetChoresController extends Controller
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
                $data['pageDescSelect'] = "Cli_Id";
                $pageAsc_Desc = "Cli_Id";
            }

            if($searchdata)
            {   
                $data['datarecords'] = PresetChores::where('pre_title','like','%'.$searchdata.'%')->orderBy('pre_title',$pageOrder)->paginate($pages);
            }else{
                $data['datarecords'] = PresetChores::where('pre_title','<>','')->orderBy('pre_title',$pageOrder)->paginate($pages);
            }
            return view('admin.predefined-chores-pages.index',$data);
        } catch (\Exception $e) {
            Exceptions::exception($e);
        }
    }

    public function store(Request $request)
    {
        $this->validateStore($request);
        try
        {  
            if(PresetChores::where('pre_title',$request->pre_title)->exists())
            {
                Session::flash('error', "Preset already exists");
                return redirect()->intended('/preset-chores');
            }

            if($request->file('pre_icon'))
            {
                $fileLink = str_random(40);
                $images = $request->file('pre_icon');
                $imagesname = str_replace(' ', '-',$fileLink.'chore-icon.'. $images->getClientOriginalExtension());
                $images->move(public_path('images/chore-icon/'),$imagesname);
            }else{
                $imagesname = 'default-icon.png';   
            }
            $insertData = new PresetChores;
            $insertData['pre_title'] = $request->pre_title;
            $insertData['pre_icon'] = $imagesname;
            $insertData['pre_status'] = $request->pre_status;
            $insertData['pre_createat'] = date('Y-m-d H:i:s');
            $insertData['pre_updateat'] = date('Y-m-d H:i:s');
            $insertData->save();

            if($insertData)
            {
                Session::flash('success', 'Preset chores created!');
                return redirect('/preset-chores');
            }else{
                Session::flash('error', "Preset chores isn't created!");
                return redirect('/preset-chores');
            }
            
        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    // Validation to check here..
    private function validateStore($request)
    {
        $this->validate($request, [
        'pre_title' => 'required|max:100',
        'pre_icon' => 'required|image|mimes:jpeg,png,gif,jpg|max:2040|dimensions:max_width=50,max_height=50',
        ]);   
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
        $this->validateUpdate($request);
        try
        {  
            if($request->file('pre_icon'))
            {
                $images = $request->file('pre_icon');

                if($request->pre_id ==1)
                {
                     $imagesname = "other-icon.png";
                }else{
                    $fileLink = str_random(40);
                    $imagesname = str_replace(' ', '-',$fileLink.'chore-icon.'. $images->getClientOriginalExtension());
                }
                $images->move(public_path('images/chore-icon/'),$imagesname);
            }else{
                $selectImages = PresetChores::where('pre_id',$request->pre_id)->select(['pre_icon'])->first();
                $imagesname = $selectImages->pre_icon;
            }

            $updateData['pre_title'] = $request->pre_title;
            $updateData['pre_icon'] = $imagesname;
            $updateData['pre_status'] = $request->pre_status;
            $updateData['pre_updateat'] = date('Y-m-d H:i:s');
            $infoUpdate = PresetChores::where('pre_id',$request->pre_id)->update($updateData);
            Session::flash('success', 'Preset chores updated!');
            return redirect()->intended('/preset-chores');
        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    private function validateUpdate($request)
    {
        $this->validate($request, [
        'pre_title' => 'required|max:200',
        'pre_icon' => 'image|mimes:jpeg,png,gif,jpg|max:2040|dimensions:max_width=50,max_height=50',
        ]);   
    }


    public function destroy($id)
    { 
        if(PresetChores::where('pre_id',$id)->exists())
        {
            PresetChores::where('pre_id',$id)->delete();
            Session::flash('success', 'Preset chores deleted!');
            return redirect()->intended('/preset-chores');
        }else{
          Session::flash('error', "Preset chores isn't deleted!");
          return redirect()->intended('/preset-chores');
        }
    }


    // User Active = 0 and Inactive = 1 
    public function changestatus(Request $request)
    {
        try
        {  
            if($request->mode == "true")
            {
                $presetStatus = PresetChores::where('pre_id',$request->pre_id)->update(array('pre_status' => 0));
                $data['status'] = "true";
                return $data;
            }
            else
            {
                $presetStatus = PresetChores::where('pre_id',$request->pre_id)->update(array('pre_status' => 1));
                 $data['status'] = "false";
                return $data;
            }

        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }
    
    public function exportExcel()
    {
         return Excel::download(new PreChoreesExport, 'preset-chores.xlsx');
    }

    public function exportPdf()
    {
        $data['datarecords'] = PresetChores::where('pre_title','<>','')->orderBy('pre_id','ASC')->get();
        $pdf = PDF::loadView('admin.predefined-chores-pages.pdf-file', $data);

        $todayDate = date('d-m-Y');
        return $pdf->download('preset-chores-'.$todayDate.'.pdf');
    }

    public function choresIcons()
    {
        $data['datarecords'] = ChoreIcon::orderBy('chi_id','DESC')->get();
        return view('admin.predefined-chores-pages.chores-icon',$data);
    }

    public function storechoresIcons(Request $request)
    {
        if($request->file('chi_image'))
        {
            $images = $request->file('chi_image');
            $imagesname = str_replace(' ', '',$images->getClientOriginalName());
            $images->move(public_path('images/chore-icon/'),$imagesname);
        }else{
            $imagesname = 'reward-default-icon.png';   
        }

        $uniqueId = str_random(15).date("Ymd");

        $insertData = new ChoreIcon;
        $insertData['chi_image'] = $imagesname;
        $insertData['chi_status'] = 0;
        $insertData['chi_createat'] = date('Y-m-d H:i:s');
        $insertData['chi_updateat'] = date('Y-m-d H:i:s');
        $insertData->save();

        Session::flash('success', "Chores icon added");
        return redirect()->intended('/chores-icons');
    }

    public function deletechoresIcons($id)
    {
         if(ChoreIcon::where('chi_id',$id)->exists())
        {
            ChoreIcon::where('chi_id',$id)->delete();
            Session::flash('success', 'Chores Icon deleted!');
            return redirect()->intended('/chores-icons');
        }else{
          Session::flash('error', "Chores Icon isn't deleted!");
          return redirect()->intended('/chores-icons');
        }
    }
}
