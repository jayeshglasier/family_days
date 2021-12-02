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
use App\Model\SubBrands;
use App\Model\Brands;
use Auth;
use Session;
use Input;
use PDF;

class BrandsController extends Controller
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
                $data['pageDescSelect'] = "brd_id";
                $pageAsc_Desc = "brd_id";
            }

            if($searchdata)
            {   
                $data['datarecords'] = Brands::where('brd_brand_name','like','%'.$searchdata.'%')->orderBy('brd_brand_name',$pageOrder)->paginate($pages);
            }else{
                $data['datarecords'] = Brands::where('brd_brand_name','<>','')->orderBy('brd_brand_name',$pageOrder)->paginate($pages);
            }
            $data['categorys'] = RewardsCategorys::where('rec_cat_name','<>','')->where('rec_status',0)->orderBy('rec_id','DESC')->get();
            return view('admin.brands-pages.index',$data);
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

            if(Brands::where('brd_brand_name',$request->brd_brand_name)->exists())
            {
                Session::flash('error', 'Brand name already exists!');
                return redirect()->intended('/reward-brand');
            }

            $uniqueId = str_random(15).date("Ymd");
            
            $categorys = RewardsCategorys::where('rec_id',$request->brd_cat_id)->first();
            
            $insertData = new Brands;
            $insertData['brd_unique_id'] = $uniqueId;
            $insertData['brd_cat_id'] = $request->brd_cat_id;
            $insertData['brd_cat_name'] = $categorys->rec_cat_name ? $categorys->rec_cat_name:'';
            $insertData['brd_brand_name'] = $request->brd_brand_name;
            $insertData['brd_status'] = $request->brd_status;
            $insertData['brd_createat'] = date('Y-m-d H:i:s');
            $insertData['brd_updateat'] = date('Y-m-d H:i:s');
            $insertData->save();

            if($insertData)
            {
                Session::flash('success', 'Brand created!');
                return redirect()->intended('/reward-brand');
            }else{
                Session::flash('error', "Brand isn't created!");
                return redirect()->intended('/reward-brand');
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
            $categorys = RewardsCategorys::where('rec_id',$request->brd_cat_id)->first();
            
            if(Brands::where('brd_brand_name',$request->brd_brand_name)->exists())
            {
                Session::flash('error', 'Brand name already exists!');
                return redirect()->intended('/reward-brand');
            }
            $updateData['brd_brand_name'] = $request->brd_brand_name;
            $updateData['brd_cat_name'] = $categorys->rec_cat_name ? $categorys->rec_cat_name:'';
            $updateData['brd_brand_name'] = $request->brd_brand_name;
            $updateData['brd_status'] = $request->brd_status;
            $updateData['brd_updateat'] = date('Y-m-d H:i:s');
            $infoUpdate = Brands::where('brd_id',$request->brd_id)->update($updateData);
            Session::flash('success', 'Brand updated!');
            return redirect()->intended('/reward-brand');
        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    private function validateUpdate($request)
    {
        $this->validate($request, [
        'brd_brand_name' => 'required|max:200',
        ]);   
    }


    public function destroy($id)
    { 
        if(Brands::where('brd_id',$id)->exists())
        {
            Brands::where('brd_id',$id)->delete();
            SubBrands::where('bds_brand_id',$id)->delete();
            Session::flash('success', 'Brand deleted!');
            return redirect()->intended('/reward-brand');
        }else{
          Session::flash('error', "Brand isn't deleted!");
          return redirect()->intended('/reward-brand');
        }
    }


    // User Active = 0 and Inactive = 1 
    public function changestatus(Request $request)
    {
        try
        {  
            if($request->mode == "true")
            {
                $brands = Brands::where('brd_id',$request->brd_id)->update(array('brd_status' => 0));
                $subBrands = SubBrands::where('bds_brand_id',$request->brd_id)->update(array('bds_status' => 0));
                $data['status'] = "true";
                return $data;
            }
            else
            {
                $brands = Brands::where('brd_id',$request->brd_id)->update(array('brd_status' => 1));
                $subBrands = SubBrands::where('bds_brand_id',$request->brd_id)->update(array('bds_status' => 1));
                $data['status'] = "false";
                return $data;
            }

        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }
    
    public function exportExcel()
    {
         return Excel::download(new PreChoreesExport, 'reward-brand.xlsx');
    }

    public function exportPdf()
    {
        $data['datarecords'] = Brands::where('brd_brand_name','<>','')->orderBy('brd_id','ASC')->get();
        $pdf = PDF::loadView('admin.brands-pages.pdf-file', $data);

        $todayDate = date('d-m-Y');
        return $pdf->download('reward-brand-'.$todayDate.'.pdf');
    }

    public function subbrandlist($id)
    {
        $data['brands'] = Brands::where('brd_id',$id)->first();
        $data['datarecords'] = SubBrands::where('bds_brand_id',$id)->get();
        return view('admin.brands-pages.view-brand-icon',$data);
    }

    public function storesubBrand(Request $request)
    {
        if($request->file('bds_brand_icon'))
        {
            $images = $request->file('bds_brand_icon');
            $imagesname = str_replace(' ', '',$images->getClientOriginalName());
            $images->move(public_path('images/brand-icon/'),$imagesname);
        }else{
            $imagesname = 'reward-default-icon.png';   
        }

        $uniqueId = str_random(15).date("Ymd");

        $insertData = new SubBrands;
        $insertData['bds_unique_id'] = $uniqueId;
        $insertData['bds_brand_id'] = $request->bds_brand_id;
        $insertData['bds_brand_icon'] = $imagesname;
        $insertData['bds_status'] = 0;
        $insertData['bds_createat'] = date('Y-m-d H:i:s');
        $insertData['bds_updateat'] = date('Y-m-d H:i:s');
        $insertData->save();

        Session::flash('success', "Brand picture added");
        return redirect()->intended('/reward-sub-brand/'.$request->bds_brand_id);
    }

    public function deleteBrandIcon($id)
    { 
        if(SubBrands::where('bds_id',$id)->exists())
        {
            $brandId = SubBrands::where('bds_id',$id)->first();
            SubBrands::where('bds_id',$id)->delete();
            Session::flash('success', 'Brand Picture deleted!');
            return redirect()->intended('/reward-sub-brand/'.$brandId->bds_brand_id);
        }else{
            $brandId = SubBrands::where('bds_id',$id)->first(); 
            Session::flash('error', "Brand picture isn't deleted!");
            return redirect()->intended('/reward-sub-brand/'.$brandId->bds_brand_id);
        }
    }
}
