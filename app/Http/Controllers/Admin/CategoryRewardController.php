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
use App\Model\RewardsCategorys;
use Auth;
use Session;
use Input;
use PDF;

class CategoryRewardController extends Controller
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
                $data['pageDescSelect'] = "rec_id";
                $pageAsc_Desc = "rec_id";
            }

            if($searchdata)
            {   
                $data['datarecords'] = RewardsCategorys::where('rec_cat_name','like','%'.$searchdata.'%')->orderBy($pageAsc_Desc,$pageOrder)->paginate($pages);
            }else{
                $data['datarecords'] = RewardsCategorys::where('rec_cat_name','<>','')->orderBy($pageAsc_Desc,$pageOrder)->paginate($pages);
            }
            return view('admin.reward-category-pages.index',$data);
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
        $this->validateCreate($request);
        try
        {  
            if($request->file('rec_icon'))
            {
                $fileLink = str_random(40);
                $images = $request->file('rec_icon');
                $imagesname = str_replace(' ', '-',$fileLink.'reward-category.'. $images->getClientOriginalExtension());
                $images->move(public_path('images/reward-icon/'),$imagesname);
            }else{
                $imagesname = 'reward-default-icon.jpg';
            }

            $uniqueId = str_random(15).date("Ymd");

            $insertData = new RewardsCategorys;
            $insertData['rec_unique_id'] = $uniqueId;
            $insertData['rec_cat_name'] = $request->rec_cat_name;
            $insertData['rec_icon'] = $imagesname;
            $insertData['rec_status'] = $request->rec_status;
            $insertData['rec_createat'] = date('Y-m-d H:i:s');
            $insertData['rec_updateat'] = date('Y-m-d H:i:s');
            $insertData->save();

            if($insertData)
            {
                Session::flash('success', 'Reward categorys created!');
                return redirect()->intended('/reward-category');
            }else{
                Session::flash('error', "Reward categorys isn't created!");
                return redirect()->intended('/reward-category');
            }
            
        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    private function validateCreate($request)
    {
        $this->validate($request, [
        'rec_cat_name' => 'required|max:200',
        'rec_icon' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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
            if($request->file('rec_icon'))
            {
                $fileLink = str_random(40);
                $images = $request->file('rec_icon');
                $imagesname = str_replace(' ', '-',$fileLink.'reward-category.'. $images->getClientOriginalExtension());
                $images->move(public_path('images/reward-icon/'),$imagesname);
            }else{
                $selectImages = RewardsCategorys::where('rec_id',$request->rec_id)->select(['rec_icon'])->first();
                $imagesname = $selectImages->rec_icon;
            }

            $updateData['rec_cat_name'] = $request->rec_cat_name;
            $updateData['rec_icon'] = $imagesname;
            $updateData['rec_status'] = $request->rec_status;
            $updateData['rec_updateat'] = date('Y-m-d H:i:s');
            $infoUpdate = RewardsCategorys::where('rec_id',$request->rec_id)->update($updateData);
            Session::flash('success', 'Reward categorys updated!');
            return redirect()->intended('/reward-category');
        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    private function validateUpdate($request)
    {
        $this->validate($request, [
        'rec_cat_name' => 'required|max:200',
        'rec_icon' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);   
    }


    public function destroy($id)
    { 
        if(RewardsCategorys::where('rec_id',$id)->exists())
        {
            RewardsCategorys::where('rec_id',$id)->delete();
            Session::flash('success', 'Reward categorys deleted!');
            return redirect()->intended('/reward-category');
        }else{
          Session::flash('error', "Reward categorys isn't deleted!");
          return redirect()->intended('/reward-category');
        }
    }


    // User Active = 0 and Inactive = 1 
    public function changestatus(Request $request)
    {
        try
        {  
            if($request->mode == "true")
            {
                $presetStatus = RewardsCategorys::where('rec_id',$request->rec_id)->update(array('rec_status' => 0));
                $data['status'] = "true";
                return $data;
            }
            else
            {
                $presetStatus = RewardsCategorys::where('rec_id',$request->rec_id)->update(array('rec_status' => 1));
                 $data['status'] = "false";
                return $data;
            }

        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }
    
    public function exportExcel()
    {
         return Excel::download(new PreChoreesExport, 'reward-category.xlsx');
    }

    public function exportPdf()
    {
        $data['datarecords'] = RewardsCategorys::where('rec_cat_name','<>','')->orderBy('rec_id','ASC')->get();
        $pdf = PDF::loadView('admin.reward-category-pages.pdf-file', $data);

        $todayDate = date('d-m-Y');
        return $pdf->download('reward-category-'.$todayDate.'.pdf');

    }
}
