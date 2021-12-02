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
                <div class="panel-heading"><b> <i class="fa fa-bars" aria-hidden="true"></i> Reward Name List</b></div>
                <!-- /.panel-heading -->
                <div class="panel-body" style="overflow:auto;"> <a class="btn btn-primary" style="float: right;" href="{{ url('export-reward-name',['type'=>'xls']) }}" onclick="return confirm('Are you sure? You want to download this record.')" data-toggle="tooltip" title="To download excel file!!">Export Excel</a>
                    <a class="btn btn-primary" style="float: right;margin-right: 5px;" href="{{ url('export-pdf-reward-name') }}" onclick="return confirm('Are you sure? You want to download this record.')" data-toggle="tooltip" title="To download pdf file!">Export Pdf</a>
                    <div class="dataTable_wrapper">
                        <form class="form-horizontal" role="form" method="get" action="{{ url('/reward-name') }}">
                            <input class="form-control" type="text" name="searchdata" value="{{ $searchdata ? $searchdata : '' }}" placeholder="Search Here.." id="searchdata-here">
                            @include('support.table-header-manu')
                            <select class="form-control" name="Asc_Desc_Select" required id="ascdesc-record">
                                <option value="per_id" @if($pageDescSelect=='per_id' ) selected="true" @endif>Sort By Sr.No</option>
                            </select>
                            <input class="form-control" type="text" name="page" value="{{ $pageGoto ? $pageGoto : '' }}" placeholder="Go To" id="go-to-page">
                        </form>
                        <table class="table table-striped table-bordered table-hover responsive">
                            <thead class="thead-table">
                                <tr>
                                    <th class="text-center">Sr No.</th>
                                    <th>Reward Name</th>
                                    <th class="text-center">Create Date</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($datarecords) > 0)
                                <?php $i = 0 ; ?> @foreach($datarecords as $data) <?php $i++; ?>
                                
                                <tr class="odd gradeX">@if($pageOrder == "ASC")
                                    <td class="text-center" width="5%">{{ ($datarecords->currentpage()-1) * $datarecords->perpage() + $i}}</td>@endif @if($pageOrder == "DESC")
                                    <td class="text-center" width="5%">{{ substr(($datarecords->currentpage()-1) * $datarecords->perpage() + $i - ($datarecords->total()+1),1)}}</td>@endif
                                    <td>{{ $data->per_name }}</td>
                                    <td width="10%" class="text-center">{{ date('M-d-Y',strtotime($data->per_createat)) }}</td>
                                    <td width="10%">
                                        <div class="material-switch">
                                            <input id="switch-primary-{{$data->per_id}}" value="{{$data->per_id}}" name="toggle" type="checkbox" {{ $data->per_status === 0 ? 'checked' : '' }}>
                                            <label for="switch-primary-{{$data->per_id}}" class="btn-success"></label>
                                        </div>
                                    </td>
                                    <td class="text-center" width="8%">
                                        <button class="btn btn-primary record-btn" data-toggle="modal" data-target="#updaterecordModel<?php echo $i; ?>" style="background-color: #2a343c;"><i class="fa fa-edit" title="Edit Reward Name!"></i></button>
                                        <a href="{{ url('delete-reward-name',$data->per_id) }}" onclick="return confirm('Are you sure you want to delete this record?')" data-toggle="tooltip" title="Delete Reward Name!">
                                            <button class="btn btn-danger record-btn"><i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </a></td>
                                </tr>
                                <!-- ------------------------------ BEGIN Reward Name MODEL ------------------------------- -->

                                    <div class="modal fade" id="updaterecordModel<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" style="width: 600px;">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title" id="myModalLabel">Edit Reward Name</h4>
                                                </div>
                                                <div class="modal-body">
                                                <form class="form-horizontal" role="form" method="POST" action="{{ url('update-reward-name') }}" style="margin-top: 20px;">
                                                    {{ csrf_field() }}
                                                    <input type="hidden" name="per_id"  value="{{ $data->per_id ? $data->per_id : '' }}">
                                                    <div class="row" style="margin-bottom: 15px;">
                                                        <div class="col-lg-12">
                                                            <div class="form-group{{ $errors->has('per_name') ? ' has-error' : '' }}">
                                                               <label for="per_name" class="col-md-3 control-label" style="text-align: right;">Reward Name<font color="red">*</font></label>
                                                                <div class="col-md-8">
                                                                <textarea id="per_name" type="text" class="form-control" name="per_name" placeholder="Reward Name" rows="4" required maxlength="100">{{ $data->per_name }}</textarea>
                                                                 @if ($errors->has('per_name'))
                                                                <span class="help-block">
                                                                    <strong>{{ $errors->first('per_name') }}</strong>
                                                                </span> @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row" style="margin-bottom: 15px;">
                                                        <div class="col-lg-12">
                                                            <div class="form-group{{ $errors->has('per_status') ? ' has-error' : '' }}">
                                                               <label for="per_status" class="col-md-3 control-label" style="text-align: right;">Status<font color="red">*</font></label>
                                                            <div class="col-md-8">
                                                            <select class="form-control" name="per_status" required>
                                                                <option value="0" @if($data->per_status == 0) selected="true" @endif>Active</option>
                                                                <option value="1" @if($data->per_status == 1) selected="true" @endif>Inactive</option>
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
                                        <!-- ------------------------------ END Reward Name MODEL ------------------------------- -->
                                @endforeach
                                @else
                                <tr class="odd gradeX">
                                    <td colspan="5" style="text-align: center;">Record not found</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>@include('support.pagination')</div>
                </div>
            </div>
        </div>
    </div>
    <!-- ---------------------------USER LIST TABLE RECORD CODE END HERE -------------------------->
</div>
</div>

<!-- ------------------------------ BEGIN Reward Name MODEL ------------------------------- -->

<div class="modal fade" id="createNewModel" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 600px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Create Reward Name</h4>
            </div>
            <div class="modal-body">
            <form class="form-horizontal" role="form" method="POST" action="{{ url('create-reward-name') }}" style="margin-top: 20px;">
                {{ csrf_field() }}
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-lg-12">
                        <div class="form-group{{ $errors->has('per_name') ? ' has-error' : '' }}">
                           <label for="per_name" class="col-md-3 control-label" style="text-align: right;">Reward Name<font color="red">*</font></label>
                            <div class="col-md-8">
                            <textarea id="per_name" type="text" class="form-control" name="per_name" placeholder="Reward Name" rows="4" required maxlength="100"></textarea>
                             @if ($errors->has('per_name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('per_name') }}</strong>
                            </span> @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-bottom: 15px;">
                    <div class="col-lg-12">
                        <div class="form-group{{ $errors->has('per_status') ? ' has-error' : '' }}">
                           <label for="per_status" class="col-md-3 control-label" style="text-align: right;">Status<font color="red">*</font></label>
                        <div class="col-md-8">
                        <select class="form-control" name="per_status" required>
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
    <!-- ------------------------------ END Reward Name MODEL ------------------------------- -->
</body>

</html>@include('layouts.gridview-js')
<script type="text/javascript">
    $('input[name=toggle]').change(function(){
        var mode= $(this).prop('checked');
        var id= $(this).val();
        $.ajax({
          type:"POST",
          dataType:"JSON",
          url:"{{ url('/status-reward-name') }}",
          data : {_token: '{{ csrf_token() }}',mode : mode,per_id:id},
          success:function(data)
          {
            if(data.status == "true")
            {
              alert("Reward Name active");
            }
            else if(data.status == "false")
            {
              alert("Reward Name inactive");
            }
          }
        });
      });
</script>