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
                <div class="panel-heading"><b> <i class="fa fa-dashboard fa-fw"></i> Category List</b></div>
                <!-- /.panel-heading -->
                <div class="panel-body" style="overflow:auto;">
                    <div class="dataTable_wrapper">
                        <form class="form-horizontal" role="form" method="get" action="{{ url('/reward-category') }}">
                            <input class="form-control" type="text" name="searchdata" value="{{ $searchdata ? $searchdata : '' }}" placeholder="Search Here.." id="searchdata-here">
                            @include('support.table-header-manu')
                            <select class="form-control" name="Asc_Desc_Select" required id="ascdesc-record">
                                <option value="rec_id" @if($pageDescSelect=='rec_id' ) selected="true" @endif>Sort By Sr.No</option>
                            </select>
                            <input class="form-control" type="text" name="page" value="{{ $pageGoto ? $pageGoto : '' }}" placeholder="Go To" id="go-to-page">
                        </form>
                        <table class="table table-striped table-bordered table-hover responsive">
                            <thead class="thead-table">
                                <tr>
                                    <th class="text-center">Sr No.</th>
                                    <th class="text-center">Icon</th>
                                    <th>Reward Category</th>
                                    <th class="text-center">Create Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=0 ; ?>@foreach($datarecords as $data) <?php $i++; ?>
                                <tr class="odd gradeX">@if($pageOrder == "ASC")
                                    <td class="text-center" width="5%" style="padding-top: 15px;">{{ ($datarecords->currentpage()-1) * $datarecords->perpage() + $i}}</td>@endif @if($pageOrder == "DESC")
                                    <td class="text-center" width="5%" style="padding-top: 15px;">{{ substr(($datarecords->currentpage()-1) * $datarecords->perpage() + $i - ($datarecords->total()+1),1)}}</td>@endif
                                    <td width="10%" class="text-center"><img src="{{ asset('public/images/reward-icon').'/'.$data->rec_icon }}" style="height: 40px;" /></td>
                                    <td style="padding-top: 15px;">{{ $data->rec_cat_name }}</td>
                                    <td width="10%" class="text-center" style="padding-top: 15px;">{{ date('M-d-Y',strtotime($data->rec_createat)) }}</td>
                                    <td width="10%" style="padding-top: 15px;">
                                        <div class="material-switch">
                                             @if($data->rec_id != 1)
                                            <input id="switch-primary-{{$data->rec_id}}" value="{{$data->rec_id}}" name="toggle" type="checkbox" {{ $data->rec_status === 0 ? 'checked' : '' }}>
                                            <label for="switch-primary-{{$data->rec_id}}" class="btn-success"></label>
                                            @else
                                            <p style="text-align: center;">Fixed</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center" width="8%" style="padding-top: 15px;">
                                        <button class="btn btn-primary record-btn" data-toggle="modal" data-target="#updaterecordModel<?php echo $i; ?>" style="background-color: #2a343c;"><i class="fa fa-edit" title="Edit Reward Category!"></i></button>
                                        @if($data->rec_id != 1)
                                        <a href="{{ url('delete-reward-category',$data->rec_id) }}" onclick="return confirm('Are you sure you want to delete this record?')" data-toggle="tooltip" title="Delete Reward Category!">
                                            <button class="btn btn-danger record-btn"><i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                <!-- ------------------------------ BEGIN Reward Category MODEL ------------------------------- -->

                                    <div class="modal fade" id="updaterecordModel<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" style="width: 600px;">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title" id="myModalLabel">Edit Reward Category</h4>
                                                </div>
                                                <div class="modal-body">
                                                <form class="form-horizontal" role="form" method="POST" action="{{ url('update-reward-category') }}" style="margin-top: 10px;" enctype="multipart/form-data">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="rec_id"  value="{{ $data->rec_id ? $data->rec_id : '' }}">
                                                    @if($data->rec_id == 1)
                                                    <div class="row" style="margin-bottom: 5px;">
                                                    <div class="col-lg-12">
                                                        <div class="form-group{{ $errors->has('rec_cat_name') ? ' has-error' : '' }}">
                                                           <label for="rec_cat_name" class="col-md-3 control-label" style="text-align: right;">Category<font color="red">*</font></label>
                                                            <div class="col-md-8">
                                                            <input id="rec_cat_name" type="text" class="form-control" name="rec_cat_name" value="{{ $data->rec_cat_name }}" placeholder="Reward Category" required maxlength="100" readonly="">
                                                             @if($errors->has('rec_cat_name'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('rec_cat_name') }}</strong>
                                                            </span> 
                                                            @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-bottom: 5px;">
                                                    <div class="col-lg-12">
                                                        <div class="form-group{{ $errors->has('rec_icon') ? ' has-error' : '' }}">
                                                           <label for="rec_icon" class="col-md-3 control-label" style="text-align: right;">Upload Icon</label>
                                                            <div class="col-md-8">
                                                            <input id="rec_icon" type="file" class="form-control" name="rec_icon">
                                                             @if($errors->has('rec_icon'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('rec_icon') }}</strong>
                                                            </span> 
                                                            @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="row" style="margin-bottom: 15px;">
                                                        <div class="col-lg-12">
                                                            <div class="form-group{{ $errors->has('rec_status') ? ' has-error' : '' }}">
                                                               <label for="rec_status" class="col-md-3 control-label" style="text-align: right;">Status<font color="red">*</font></label>
                                                            <div class="col-md-8">
                                                            <select class="form-control" name="rec_status" required readonly>
                                                                <option value="0" @if($data->rec_status == 0) selected="true" @endif>Active</option>
                                                                <option value="1" @if($data->rec_status == 1) selected="true" @endif>Inactive</option>
                                                            </select>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @else
                                                    <div class="row" style="margin-bottom: 5px;">
                                                    <div class="col-lg-12">
                                                        <div class="form-group{{ $errors->has('rec_cat_name') ? ' has-error' : '' }}">
                                                           <label for="rec_cat_name" class="col-md-3 control-label" style="text-align: right;">Category<font color="red">*</font></label>
                                                            <div class="col-md-8">
                                                            <input id="rec_cat_name" type="text" class="form-control" name="rec_cat_name" value="{{ $data->rec_cat_name }}" placeholder="Reward Category" required maxlength="100">
                                                             @if($errors->has('rec_cat_name'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('rec_cat_name') }}</strong>
                                                            </span> 
                                                            @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row" style="margin-bottom: 5px;">
                                                    <div class="col-lg-12">
                                                        <div class="form-group{{ $errors->has('rec_icon') ? ' has-error' : '' }}">
                                                           <label for="rec_icon" class="col-md-3 control-label" style="text-align: right;">Upload Icon</label>
                                                            <div class="col-md-8">
                                                            <input id="rec_icon" type="file" class="form-control" name="rec_icon">
                                                             @if($errors->has('rec_icon'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('rec_icon') }}</strong>
                                                            </span> 
                                                            @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="row" style="margin-bottom: 15px;">
                                                        <div class="col-lg-12">
                                                            <div class="form-group{{ $errors->has('rec_status') ? ' has-error' : '' }}">
                                                               <label for="rec_status" class="col-md-3 control-label" style="text-align: right;">Status<font color="red">*</font></label>
                                                            <div class="col-md-8">
                                                            <select class="form-control" name="rec_status" required>
                                                                <option value="0" @if($data->rec_status == 0) selected="true" @endif>Active</option>
                                                                <option value="1" @if($data->rec_status == 1) selected="true" @endif>Inactive</option>
                                                            </select>
                                                            </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o" aria-hidden="true"></i> Cancle</button>
                                                        <button type="submit" class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Update</button>
                                                    </div>
                                                </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <!-- ------------------------------ END Reward Category MODEL ------------------------------- -->
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

<!-- ------------------------------ BEGIN Reward Category MODEL ------------------------------- -->

<div class="modal fade" id="createNewModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Create Reward Category</h4>
            </div>
            <div class="modal-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('create-reward-category') }}" style="margin-top: 10px;" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group{{ $errors->has('rec_cat_name') ? ' has-error' : '' }}">
                           <label for="rec_cat_name" class="col-md-3 control-label" style="text-align: right;">Category<font color="red">*</font></label>
                            <div class="col-md-8">
                            <input id="rec_cat_name" type="text" class="form-control" name="rec_cat_name" placeholder="Reward Category" required maxlength="100">
                             @if($errors->has('rec_cat_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('rec_cat_name') }}</strong>
                            </span> 
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="form-group{{ $errors->has('rec_icon') ? ' has-error' : '' }}">
                           <label for="rec_icon" class="col-md-3 control-label" style="text-align: right;">Upload Icon</label>
                            <div class="col-md-8">
                            <input id="rec_icon" type="file" class="form-control" name="rec_icon">
                             @if($errors->has('rec_icon'))
                            <span class="help-block">
                                <strong>{{ $errors->first('rec_icon') }}</strong>
                            </span> 
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-lg-12">
                        <div class="form-group{{ $errors->has('rec_status') ? ' has-error' : '' }}">
                           <label for="rec_status" class="col-md-3 control-label" style="text-align: right;">Status<font color="red">*</font></label>
                        <div class="col-md-8">
                        <select class="form-control" name="rec_status" required>
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
    <!-- ------------------------------ END Reward Category MODEL ------------------------------- -->
</body>

</html>@include('layouts.gridview-js')
<script type="text/javascript">
    $('input[name=toggle]').change(function(){
        var mode= $(this).prop('checked');
        var id= $(this).val();
        $.ajax({
          type:"POST",
          dataType:"JSON",
          url:"{{ url('/status-reward-category') }}",
          data : {_token: '{{ csrf_token() }}',mode : mode,rec_id:id},
          success:function(data)
          {
            if(data.status == "true")
            {
              alert("Reward Category active");
            }
            else if(data.status == "false")
            {
              alert("Reward Category inactive");
            }
          }
        });
      });
</script>