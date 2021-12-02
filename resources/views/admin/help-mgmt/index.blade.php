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
hr {
    margin-top: 9px !important;
    margin-bottom: 10px !important;
}
</style>
<div id="page-wrapper">
    <!-- ---------------------------USER LIST TITLE BAR CODE START HERE -------------------------->
    <div class="row">
        <div class="col-lg-6">
            <h4 class="page-header"></h4>
        </div>
        <!------------------------------------------------ Success Message Display Start Here ------------------------>
        <div class="col-lg-6">
            @if(session('success'))
            <br>
            <div class="flash-message" style="padding-top: 5px;">
                <div class="alert alert-info" style="text-align: center;">
                    <span class="success-message"><big>{{ session('success') }}</big></span>
                </div>
            </div>
            @endif @if (session('error'))
            <br>
            <div class="flash-message" style="padding-top: 5px;">
                <div class="alert alert-danger" style="text-align: center;">
                    <span class="error-message"><big>{{ session('error') }}</big></span>
                </div>
            </div>
            @endif
        </div>
        <!---------------------------------------------- Success Message Display Start Here ------------------------------->
    </div>
     <!-- ---------------------------USER LIST TITLE BAR CODE End HERE -------------------------->

     <!-- ---------------------------USER LIST TABLE RECORD CODE START HERE -------------------------->
    <div class="row">
        <div class="col-lg-12" style="padding-left: 1px !important;">
            <div class="panel panel-default" style="margin-top: -25px !important;">
                <div class="panel-heading"><b> <i class="fa fa-user" aria-hidden="true"></i> Help List</b></div>
                <!-- /.panel-heading -->
                <div class="panel-body" style="overflow:auto;">
                    <div class="dataTable_wrapper">
                        <form class="form-horizontal" role="form" method="get" action="{{ url('/help') }}">
                            <input class="form-control" type="text" name="searchdata" value="{{ $searchdata ? $searchdata : '' }}" placeholder="Search Here..." id="searchdata-here">
                            @include('support.table-header-manu')
                            <select class="form-control" name="Asc_Desc_Select" required id="ascdesc-record">
                            <option value="amc_id" @if($pageDescSelect == 'amc_id') selected="true" @endif>Sort By Sr.No</option>
                        </select>
                            <input class="form-control" type="text" name="page" value="{{ $pageGoto ? $pageGoto : '' }}" placeholder="Go To" id="go-to-page">
                        </form>
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="thead-table">
                                <tr>
                                    <th class="text-center">Sr No.</th>
                                    <th>Fullname</th>
                                    <th>Email</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">Document</th>
                                    <th class="text-center">Read / Unread</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?> @foreach($datarecords as $data)
                                <?php $i++; ?>
                                <tr class="odd gradeX">
                                    @if($pageOrder == "ASC")
                                        <td class="text-center" width="10%">{{ ($datarecords->currentpage()-1) * $datarecords->perpage() + $i}}</td>
                                    @endif
                                    @if($pageOrder == "DESC")
                                        <td class="text-center" width="10%">{{ substr(($datarecords->currentpage()-1) * $datarecords->perpage() + $i - ($datarecords->total()+1),1)}}</td>
                                    @endif
                                    <td>{{ $data->amc_full_name }}</td>
                                    <td>{{ str_limit($data->amc_email,30) }}</td>
                                    <td width="15%" class="text-center">{{ str_limit($data->amc_phone,30) }}</td>
                                    <td width="15%" class="text-center">
                                        @if($data->amc_media_file)<a href="{{ asset('public/images/contract-admin').'/'.$data->amc_media_file }}" target="_blank"><i class="fa fa-download"></i> Download</a>@else {{ '---' }}@endif</td>
                                    <td width="10%">
                                          <div class="material-switch" style="margin-left: 20px;">
                                            <input id="switch-primary-{{$data->amc_id}}" value="{{$data->amc_id}}" name="toggle" type="checkbox" {{ $data->amc_status === 0 ? 'checked' : '' }}>
                                            <label for="switch-primary-{{$data->amc_id}}" class="btn-success"></label>
                                        </div>
                                    </td>
                                    <td class="text-center"  width="10%">
                                         <button class="btn btn-primary record-btn" data-toggle="modal" data-target="#updaterecordModel<?php echo $i; ?>" style="background-color: #2a343c;"><i class="fa fa-users"></i></button>
                                         <a href="{{ url('delete-help',$data->amc_id) }}" onclick="return confirm('Are you sure you want to delete this record?')" data-toggle="tooltip" title="Delete Reward Name!">
                                            <button class="btn btn-danger record-btn"><i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </a>
                                    </td>
                                </tr>
                                <!-- ------------------------------ BEGIN MODEL ------------------------------- -->

                                    <div class="modal fade" id="updaterecordModel<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                        <div class="modal-dialog" style="width: 600px;">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                    <h4 class="modal-title" id="myModalLabel">Help Details</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-3" style="text-align: right;"><b>Fullname :</b> </div>
                                                        <div class="col-md-9" style="margin-left: -20px;">{{ $data->amc_full_name }}</div>
                                                    </div>
                                                    <hr>
                                                     <div class="row">
                                                        <div class="col-md-3" style="text-align: right;"><b>Email : </b></div>
                                                        <div class="col-md-9" style="margin-left: -20px;">{{ $data->amc_email }}</div>
                                                    </div>
                                                    <hr>
                                                     <div class="row">
                                                        <div class="col-md-3" style="text-align: right;"><b>Phone : </b></div>
                                                        <div class="col-md-9" style="margin-left: -20px;">{{ $data->amc_phone }}</div>
                                                    </div>
                                                    <hr>
                                                     <div class="row">
                                                        <div class="col-md-3" style="text-align: right;"><b>Subject : </b></div>
                                                        <div class="col-md-9" style="margin-left: -20px;">{{ $data->amc_subject }}</div>
                                                    </div>
                                                    <hr>
                                                     <div class="row">
                                                        <div class="col-md-3" style="text-align: right;"><b>Message : </b></div>
                                                        <div class="col-md-9" style="margin-left: -20px;">{{ $data->amc_message }}</div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-3" style="text-align: right;"><b>Create Date : </b></div>
                                                        <div class="col-md-9" style="margin-left: -20px;">{{ date('M-d-Y',strtotime($data->amc_createat)) }}</div>
                                                    </div>
                                                    <hr>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <!-- ------------------------------ END MODEL ------------------------------- -->
                                @endforeach
                            </tbody>
                        </table>
                        @include('support.pagination')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ---------------------------USER LIST TABLE RECORD CODE END HERE -------------------------->
</div>
</div>
</body>
</html>
@include('layouts.gridview-js')
<script type="text/javascript">
    $('input[name=toggle]').change(function(){
    var mode= $(this).prop('checked');
    var id= $(this).val();
    $.ajax({
      type:"POST",
      dataType:"JSON",
      url:"{{ url('/help-update-status') }}",
      data : {
            _token: '{{ csrf_token() }}',
            mode : mode,
            amc_id:id
            },
      success:function(data)
      {
        if(data.status == "true")
        {
          alert("Read successfully.");
        }
        else if(data.status == "false")
        {
          alert("Unread successfully.");
        }
      }
    });
  }); 
</script>