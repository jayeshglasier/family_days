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
                <div class="panel-heading"><b> <i class="fa fa-user" aria-hidden="true"></i> User List</b></div>
                <!-- /.panel-heading -->
                <div class="panel-body" style="overflow:auto;">
                    <a class="btn btn-primary" style="float: right;" href="{{ url('user-details-export-excel',['type'=>'xls']) }}" onclick="return confirm('Are you sure? You want to download the user record.')" data-toggle="tooltip" title="To download excel file!!">Export Excel</a>
                    <a class="btn btn-primary" style="float: right;margin-right: 5px;" href="{{ url('user-details-export-pdf') }}" onclick="return confirm('Are you sure? You want to download the user record.')" data-toggle="tooltip" title="To download pdf file!">Export Pdf</a>
                    <div class="dataTable_wrapper">
                        <form class="form-horizontal" role="form" method="get" action="{{ url('/users') }}">
                            <input class="form-control" type="text" name="searchdata" value="{{ $searchdata ? $searchdata : '' }}" placeholder="Search Here..." id="searchdata-here">
                            @include('support.table-header-manu')
                            <select class="form-control" name="Asc_Desc_Select" required id="ascdesc-record">
                            <option value="id" @if($pageDescSelect == 'id') selected="true" @endif>Sort By Sr.No</option>
                            <option value="use_family_name" @if($pageDescSelect == 'use_family_name') selected="true" @endif>Sort By Family Name</option>
                            <option value="email" @if($pageDescSelect == 'email') selected="true" @endif>Sort By Email</option>
                        </select>
                            <input class="form-control" type="text" name="page" value="{{ $pageGoto ? $pageGoto : '' }}" placeholder="Go To" id="go-to-page">
                        </form>
                        <table class="table table-striped table-bordered table-hover responsive">
                            <thead class="thead-table">
                                <tr>
                                    <th class="text-center">Sr No.</th>
                                    <th>Family Name</th>
                                    <th>Username</th>
                                    <th>Fullname</th>
                                    <th>Email Id</th>
                                    <th class="text-center">Phone</th>
                                    <th class="text-center">User Type</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?> @foreach($datarecords as $data)
                                <?php $i++; ?>
                                <tr class="odd gradeX">
                                    @if($pageOrder == "ASC")
                                        <td class="text-center" width="5%">{{ ($datarecords->currentpage()-1) * $datarecords->perpage() + $i}}</td>
                                    @endif
                                    @if($pageOrder == "DESC")
                                        <td class="text-center" width="5%">{{ substr(($datarecords->currentpage()-1) * $datarecords->perpage() + $i - ($datarecords->total()+1),1)}}</td>
                                    @endif
                                    <td>{{ str_limit($data->use_family_name,30) }}</td>
                                    <td>{{ str_limit($data->use_username,30) }}</td>
                                    <td>{{ str_limit($data->use_full_name,30) }}</td>
                                    <td>{{ str_limit($data->email ? $data->email:'NA',30) }}</td>
                                    <td class="text-center">{{ $data->use_phone_no ? $data->use_phone_no: '---'}}</td>
                                    <td width="15%" class="text-center">{{ $data->rol_name ? $data->rol_name : 'NA' }} @if($data->use_is_admin == 1) {{ "- ( Admin )" }} @else {{ "" }} @endif</td>
                                    <td>
                                        <div class="material-switch" style="margin-left: -20px;">
                                            <input id="switch-primary-{{$data->id}}" value="{{$data->id}}" name="toggle" type="checkbox" {{ $data->use_status === 0 ? 'checked' : '' }}>
                                            <label for="switch-primary-{{$data->id}}" class="btn-success"></label>
                                        </div>
                                    </td>
                                    <td class="text-center"  width="8%">
                                        <a href="{{ url('update-users',$data->use_family_id) }}" data-toggle="tooltip" title="View / Edit User Detail !">
                                            <button class="btn btn-primary record-btn" style="background-color: #2a343c;"><i class="fa fa-pencil"></i></button>
                                        </a>
                                        @if($data->use_is_family_head == 1)
                                        @else
                                        <a href="{{ url('delete-users',$data->use_family_id) }}" onclick="return confirm('Are you sure you want to delete this record?')" data-toggle="tooltip" title="Delete User!">
                                            <button class="btn btn-danger record-btn"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
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
      url:"{{ url('/user-update-status') }}",
      data : {
            _token: '{{ csrf_token() }}',
            mode : mode,
            user_id:id
            },
      success:function(data)
      {
        if(data.status == "true")
        {
          alert("User active successfully.");
        }
        else if(data.status == "false")
        {
          alert("User Inactive successfully.");
        }
      }
    });
  }); 
</script>