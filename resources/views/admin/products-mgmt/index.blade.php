@include('layouts.header') 
@include('layouts.gridview-css') 
@include('layouts.sidebar')
<style type="text/css">
    @media only screen and (max-width: 2500px) and (min-width: 500px)
    {
        .modal-dialog {
        width: 478px;
        margin: 30px auto;
    }
    }
    option{
        height: 25px;
    }
</style>
<div id="page-wrapper">
    <!-- ---------------------------USER LIST TITLE BAR CODE START HERE -------------------------->
    <div class="row">
        <div class="col-lg-6" style="margin-bottom: 30px;margin-top: 30px;margin-left: -12px;">
            <button class="btn btn-primary" data-toggle="modal" data-target="#createNewModel"><b>Add New</b> <i class="fa fa-plus"></i></button>
        </div>
         <!------------------------------------------------ Success Message Display Start Here ------------------------>
       @include('layouts.success-error-message')
        <!---------------------------------------------- Success Message Display Start Here ------------------------------->
    </div>
    <div class="row">
        <div class="col-lg-12" style="padding-left: 1px !important;">
            <div class="panel panel-default" style="margin-top: -25px !important;">
                <div class="panel-heading"><b> <i class="fa fa-dashboard fa-fw"></i> Product List</b></div>
                <!-- /.panel-heading -->
                <div class="panel-body" style="overflow:auto;"> <!-- <a class="btn btn-primary" style="float: right;" href="{{ url('export-reward-brand',['type'=>'xls']) }}" onclick="return confirm('Are you sure? You want to download this record.')" data-toggle="tooltip" title="To download excel file!!">Export Excel</a>
                    <a class="btn btn-primary" style="float: right;margin-right: 5px;" href="{{ url('export-pdf-reward-brand') }}" onclick="return confirm('Are you sure? You want to download this record.')" data-toggle="tooltip" title="To download pdf file!">Export Pdf</a> -->
                    <div class="dataTable_wrapper">
                        <form class="form-horizontal" role="form" method="get" action="{{ url('/reward-brand') }}">
                            <input class="form-control" type="text" name="searchdata" value="{{ $searchdata ? $searchdata : '' }}" placeholder="Search Here.." id="searchdata-here">
                            @include('support.table-header-manu')
                            <select class="form-control" name="Asc_Desc_Select" required id="ascdesc-record">
                                <option value="bds_id" @if($pageDescSelect=='bds_id' ) selected="true" @endif>Sort By Sr.No</option>
                            </select>
                            <input class="form-control" type="text" name="page" value="{{ $pageGoto ? $pageGoto : '' }}" placeholder="Go To" id="go-to-page">
                        </form>
                        <table class="table table-striped table-bordered table-hover responsive">
                            <thead class="thead-table">
                                <tr>
                                    <th class="text-center">Sr No.</th>
                                    <th>Category</th>
                                    <th>Brand Name</th>
                                    <th class="text-center">Product Pricture</th>
                                    <th class="text-center">Link</th>
                                    <th class="text-center">Create Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=0 ; ?>@foreach($datarecords as $data) <?php $i++; ?>
                                <tr class="odd gradeX">@if($pageOrder == "ASC")
                                    <td class="text-center" width="5%">{{ ($datarecords->currentpage()-1) * $datarecords->perpage() + $i}}</td>@endif @if($pageOrder == "DESC")
                                    <td class="text-center" width="5%">{{ substr(($datarecords->currentpage()-1) * $datarecords->perpage() + $i - ($datarecords->total()+1),1)}}</td>@endif
                                    <td>{{ $data->rec_cat_name }}</td>
                                    <td>{{ $data->brd_brand_name }}</td>
                                    <td width="15%" class="text-center"><img src="{{ asset('public/images/brand-icon').'/'.$data->bds_brand_icon }}" style="height: 40px;" /></td>
                                    <td width="10%" class="text-center">@if($data->bds_link)<a href="{{ $data->bds_link }}" target="_blank">Link</a>@else {{ '---' }} @endif</td>
                                    <td width="10%" class="text-center">{{ date('M-d-Y',strtotime($data->bds_createat)) }}</td>
                                    <td width="10%">
                                        <div class="material-switch">
                                            <input id="switch-primary-{{$data->bds_id}}" value="{{$data->bds_id}}" name="toggle" type="checkbox" {{ $data->bds_status === 0 ? 'checked' : '' }}>
                                            <label for="switch-primary-{{$data->bds_id}}" class="btn-success"></label>
                                        </div>
                                    </td>
                                    <td class="text-center" width="10%">
                                        <button class="btn btn-primary record-btn" data-toggle="modal" data-target="#updaterecordModel<?php echo $i; ?>" style="background-color: #2a343c;"><i class="fa fa-edit" title="Edit Product!"></i></button>
                                        
                                        <a href="{{ url('delete-products',$data->bds_id) }}" onclick="return confirm('Are you sure you want to delete this record?')" data-toggle="tooltip" title="Delete Product!">
                                            <button class="btn btn-danger record-btn"><i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </a></td>
                                </tr>
                                <!-- ------------------------------ BEGIN Product MODEL ------------------------------- -->

                                    <div class="modal fade" id="updaterecordModel<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" style="width: 600px;">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title" id="myModalLabel">Edit Product</h4>
                                                </div>
                                                <div class="modal-body">
                                                <form class="form-horizontal" role="form" method="POST" action="{{ url('update-products') }}" style="margin-top: 10px;" enctype="multipart/form-data">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="bds_id" value="{{ $data->bds_id }}">
                                                <div class="row" style="margin-bottom: 5px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('bds_cat_id') ? ' has-error' : '' }}">
                                                       <label for="bds_cat_id" class="col-md-3 control-label" style="text-align: right;">Category<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                        <select id="updCategoryId" class="form-control" name="bds_cat_id" required>
                                                        @foreach($categorys as $category)
                                                        <option value="{{ $category->rec_id }}" @if($data->bds_cat_id == $category->rec_id) selected="true" @endif>{{ $category->rec_cat_name }}</option>
                                                        @endforeach
                                                        </select>
                                                         @if ($errors->has('bds_cat_id'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('bds_cat_id') }}</strong>
                                                        </span> @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 5px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('bds_brand_id') ? ' has-error' : '' }}">
                                                       <label for="bds_brand_id" class="col-md-3 control-label" style="text-align: right;">Brand<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                        <select id="upBrandId" class="form-control updateBrand" name="bds_brand_id" required>
                                                        @foreach($brands as $brand)
                                                        @if($data->bds_cat_id == $brand->brd_cat_id)
                                                        <option value="{{ $brand->brd_id }}" @if($data->bds_brand_id == $brand->brd_id) selected="true" @endif>{{ $brand->brd_brand_name }}</option>
                                                        @endif
                                                        @endforeach
                                                        </select>
                                                         @if ($errors->has('bds_brand_id'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('bds_brand_id') }}</strong>
                                                        </span> @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 5px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('bds_brand_icon') ? ' has-error' : '' }}">
                                                       <label for="bds_brand_icon" class="col-md-3 control-label" style="text-align: right;">Product Picture</label>
                                                        <div class="col-md-8">
                                                        <input id="bds_brand_icon" type="file" class="form-control" name="bds_brand_icon"/>
                                                         @if ($errors->has('bds_brand_icon'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('bds_brand_icon') }}</strong>
                                                        </span> @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 5px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('bds_link') ? ' has-error' : '' }}">
                                                       <label for="bds_link" class="col-md-3 control-label" style="text-align: right;">Product Link</label>
                                                        <div class="col-md-8">
                                                        <textarea id="bds_link"  class="form-control" name="bds_link"/>{{$data->bds_link}}</textarea>
                                                         @if ($errors->has('bds_link'))
                                                        <span class="help-block">
                                                            <strong>{{ $errors->first('bds_link') }}</strong>
                                                        </span> @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 5px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('bds_status') ? ' has-error' : '' }}">
                                                       <label for="bds_status" class="col-md-3 control-label" style="text-align: right;">Status<font color="red">*</font></label>
                                                    <div class="col-md-8">
                                                    <select class="form-control" name="bds_status" required>
                                                        <option value="0" @if($data->bds_status == 0) selected="true" @endif>Active</option>
                                                        <option value="1" @if($data->bds_status == 1) selected="true" @endif>Inactive</option>
                                                    </select>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o" aria-hidden="true"></i> Cancle</button>
                                                        <button type="submit" class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update</button>
                                                    </div>
                                                </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <!-- ------------------------------ END Brand MODEL ------------------------------- -->
                                @endforeach
                            </tbody>
                        </table>@include('support.pagination')</div>
                </div>
            </div>
        </div>
    </div>
    <!-- ---------------------------USER LIST TABLE RECORD CODE END HERE -------------------------->
</div>
</div>

<!-- ------------------------------ BEGIN Brand MODEL ------------------------------- -->

<div class="modal fade" id="createNewModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Create Product</h4>
            </div>
            <div class="modal-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('create-products') }}" style="margin-top: 10px;" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group{{ $errors->has('bds_cat_id') ? ' has-error' : '' }}">
                           <label for="bds_cat_id" class="col-md-3 control-label" style="text-align: right;">Category<font color="red">*</font></label>
                            <div class="col-md-8">
                            <select id="categoryId" class="form-control" name="bds_cat_id" required>
                            @foreach($categorys as $category)
                            <option value="{{ $category->rec_id }}">{{ $category->rec_cat_name }}</option>
                            @endforeach
                            </select>
                             @if ($errors->has('bds_cat_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('bds_cat_id') }}</strong>
                            </span> @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group{{ $errors->has('bds_brand_id') ? ' has-error' : '' }}">
                           <label for="bds_brand_id" class="col-md-3 control-label" style="text-align: right;">Brand<font color="red">*</font></label>
                            <div class="col-md-8">
                            <select id="cbrand_id" class="form-control cbrandId" name="bds_brand_id" required>
                            <option value="">--- Select First Category ---</option>
                            </select>
                             @if ($errors->has('bds_brand_id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('bds_brand_id') }}</strong>
                            </span> @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group{{ $errors->has('bds_brand_icon') ? ' has-error' : '' }}">
                           <label for="bds_brand_icon" class="col-md-3 control-label" style="text-align: right;">Product Picture<font color="red">*</font></label>
                            <div class="col-md-8">
                            <input id="bds_brand_icon" type="file" class="form-control" name="bds_brand_icon" placeholder="Brand Name"  required />
                             @if ($errors->has('bds_brand_icon'))
                            <span class="help-block">
                                <strong>{{ $errors->first('bds_brand_icon') }}</strong>
                            </span> @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group{{ $errors->has('bds_link') ? ' has-error' : '' }}">
                           <label for="bds_link" class="col-md-3 control-label" style="text-align: right;">Product Link</label>
                            <div class="col-md-8">
                            <textarea id="bds_link" class="form-control" name="bds_link" placeholder="Product Link ex: https://www.amazon.in/product-item-1...." /></textarea>
                             @if ($errors->has('bds_link'))
                            <span class="help-block">
                                <strong>{{ $errors->first('bds_link') }}</strong>
                            </span> @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group{{ $errors->has('bds_status') ? ' has-error' : '' }}">
                           <label for="bds_status" class="col-md-3 control-label" style="text-align: right;">Status<font color="red">*</font></label>
                        <div class="col-md-8">
                        <select class="form-control" name="bds_status" required>
                            <option value="0">Active</option>
                            <option value="1">Inactive</option>
                        </select>
                        </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o" aria-hidden="true"></i> Cancle</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Save</button>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
    <!-- ------------------------------ END Brand MODEL ------------------------------- -->
</body>

</html>@include('layouts.gridview-js')
<script type="text/javascript">
    $('input[name=toggle]').change(function(){
        var mode= $(this).prop('checked');
        var id= $(this).val();
        $.ajax({
          type:"POST",
          dataType:"JSON",
          url:"{{ url('/status-reward-brand') }}",
          data : {_token: '{{ csrf_token() }}',mode : mode,bds_id:id},
          success:function(data)
          {
            if(data.status == "true")
            {
              alert("Brand active");
            }
            else if(data.status == "false")
            {
              alert("Brand inactive");
            }
          }
        });
      });
</script>

<script>
  $("#categoryId").on('change',function(){
          var categoryId = $(this).val();
          $.ajax({
              type : "POST",
              url : "{{ url('sub-category-brands') }}",
              data : {
                  _token: '{{ csrf_token() }}',
                  category_id : categoryId
              },
              dataType : "JSON",
              success : function(data){

                      $(".cbrandId option").each(function() {
                          $(this).remove();
                      });
                      var items = [];
                       $.each( data, function( key, val ) {
                          items.push( "<option value='" +this['brd_id']+"' class='category-option'>" + this['brd_brand_name'] +"</option>" );
                       });
                      $("#cbrand_id").append('<option value="">-- Select Brand --</option>');
                      $("#cbrand_id").append(items);
                  },
              error : function(error){
                  console.log(error);
              }
          });
      });
</script>

<script>
  $("#updCategoryId").on('change',function(){
          var categoryId = $(this).val();
          $.ajax({
              type : "POST",
              url : "{{ url('sub-category-brands') }}",
              data : {
                  _token: '{{ csrf_token() }}',
                  category_id : categoryId
              },
              dataType : "JSON",
              success : function(data){

                      $(".updateBrand option").each(function() {
                          $(this).remove();
                      });
                      var items = [];
                       $.each( data, function( key, val ) {
                          items.push( "<option value='" +this['brd_id']+"' class='category-option'>" + this['brd_brand_name'] +"</option>" );
                       });
                      $("#upBrandId").append('<option value="">-- Select Brand --</option>');
                      $("#upBrandId").append(items);
                  },
              error : function(error){
                  console.log(error);
              }
          });
      });
</script>