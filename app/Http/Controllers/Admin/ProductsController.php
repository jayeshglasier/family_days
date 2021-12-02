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

class ProductsController extends Controller
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
                $data['datarecords'] = SubBrands::leftjoin('tbl_rewards_categorys','tbl_sub_brands.bds_cat_id','tbl_rewards_categorys.rec_id')->
                leftjoin('tbl_brands','tbl_sub_brands.bds_brand_id','tbl_brands.brd_id')->where('bds_brand_icon','like','%'.$searchdata.'%')->where('bds_status',0)->orderBy('bds_brand_icon',$pageOrder)->paginate($pages);
            }else{
                $data['datarecords'] = SubBrands::leftjoin('tbl_rewards_categorys','tbl_sub_brands.bds_cat_id','tbl_rewards_categorys.rec_id')->
                leftjoin('tbl_brands','tbl_sub_brands.bds_brand_id','tbl_brands.brd_id')->where('bds_brand_icon','<>','')->where('bds_status',0)->orderBy('bds_brand_icon',$pageOrder)->paginate($pages);
            }

            $data['brands'] = Brands::where('brd_brand_name','<>','')->where('brd_status',0)->orderBy('brd_brand_name','ASC')->get();

            $data['categorys'] = RewardsCategorys::where('rec_cat_name','<>','')->where('rec_status',0)->orderBy('rec_id','DESC')->get();

            return view('admin.products-mgmt.index',$data);
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
        $this->validateStore($request);
        try
        {  
        if($request->file('bds_brand_icon'))
        {
            $fileLink = str_random(40);
            $images = $request->file('bds_brand_icon');
            $imagesname = str_replace(' ', '-',$fileLink.'brand-icon.'. $images->getClientOriginalExtension());
            $images->move(public_path('images/brand-icon/'),$imagesname);
        }else{
            $imagesname = 'reward-default-icon.png';   
        }

        $uniqueId = str_random(15).date("Ymd");

        $insertData = new SubBrands;
        $insertData['bds_unique_id'] = $uniqueId;
        $insertData['bds_cat_id'] = $request->bds_cat_id;
        $insertData['bds_brand_id'] = $request->bds_brand_id;
        $insertData['bds_brand_icon'] = $imagesname;
        $insertData['bds_link'] = $request->bds_link ? $request->bds_link:'';
        $insertData['bds_status'] = 0;
        $insertData['bds_createat'] = date('Y-m-d H:i:s');
        $insertData['bds_updateat'] = date('Y-m-d H:i:s');
        $insertData->save();

        if($insertData)
        {
            Session::flash('success', 'Product created!');
            return redirect()->intended('/products');
        }else{
            Session::flash('error', "Product isn't created!");
            return redirect()->intended('/products');
        }
            
        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    private function validateStore($request)
    {
        $this->validate($request, [
        'bds_brand_icon' => 'required|image|mimes:jpeg,png,jpg|max:2048',
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
            if($request->file('bds_brand_icon'))
            {
                $fileLink = str_random(40);
                $images = $request->file('bds_brand_icon');
                $imagesname = str_replace(' ', '-',$fileLink.'brand-icon.'. $images->getClientOriginalExtension());
                $images->move(public_path('images/brand-icon/'),$imagesname);
            }else{
                $selectImages = SubBrands::where('bds_id',$request->bds_id)->select(['bds_brand_icon'])->first();
                $imagesname = $selectImages->bds_brand_icon;
            }
            $updateData['bds_cat_id'] = $request->bds_cat_id;
            $updateData['bds_brand_id'] = $request->bds_brand_id;
            $updateData['bds_brand_icon'] = $imagesname;
            $updateData['bds_link'] = $request->bds_link ? $request->bds_link:'';
            $updateData['bds_status'] = $request->bds_status;
            $updateData['bds_updateat'] = date('Y-m-d H:i:s');
            $infoUpdate = SubBrands::where('bds_id',$request->bds_id)->update($updateData);
            Session::flash('success', 'Product updated!');
            return redirect()->intended('/products');
        }catch (\Exception $e) {
             Exceptions::exception($e);
        }
    }

    private function validateUpdate($request)
    {
        $this->validate($request, [
        'bds_brand_icon' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);   
    }


    public function destroy($id)
    { 
        if(SubBrands::where('bds_id',$id)->exists())
        {
            SubBrands::where('bds_id',$id)->delete();
            Session::flash('success', 'Product deleted!');
            return redirect()->intended('/products');
        }else{
          Session::flash('error', "Product isn't deleted!");
          return redirect()->intended('/products');
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
        $data['datarecords'] = Brands::where('bds_brand_icon','<>','')->orderBy('brd_id','ASC')->get();
        $pdf = PDF::loadView('admin.products-mgmt.pdf-file', $data);

        $todayDate = date('d-m-Y');
        return $pdf->download('reward-brand-'.$todayDate.'.pdf');
    }

    public function subCategoryBrands(Request $request)
    {
         $data = Brands::where('brd_cat_id',$request->category_id)->orderby('brd_brand_name','ASC')->distinct()->get(['brd_id','brd_brand_name']);
        return $data;
    }
}
