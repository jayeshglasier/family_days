@include('layouts.header') 
@include('layouts.gridview-css')
@include('layouts.sidebar')
<style type="text/css">
    .btn-primary {
    color: #fff;
    background-color: #291e1e;
    border-color: #848e96;
    font-weight: 500;
}
</style>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">Below list to show User full detail. <a href="{{ url('user-management') }}" data-toggle="tooltip" title="Back to User List!" style="float: right;margin-top:-8px; "><button class="btn btn-primary"><b>Back</b></button></a></h3>

                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h4><i class="fa fa-user" aria-hidden="true"></i> User Detail</h4>
                            <p>Below list to show User full detail with description.</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <!-- Project Detail -->
                                        <tr>
                                            <th width="20%"><i class="fa fa-user" aria-hidden="true"></i> Name</th>
                                            <td>{{ $viewdata->Use_Full_Name ? $viewdata->Use_Full_Name : '' }}</td>
                                        </tr> 
                                        <tr>
                                            <th width="20%"><i class="fa fa-envelope" ></i> Email</th>
                                            <td>{{ $viewdata->Use_Email_Id ? $viewdata->Use_Email_Id : '' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="20%"><i class="fa fa-phone" aria-hidden="true"></i> Contact Number</th>
                                            <td>{{ $viewdata->Use_Mobile_Phone ? $viewdata->Use_Mobile_Phone : '' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="20%"><i class="fa fa-home" aria-hidden="true"></i> Address</th>
                                            <td>{!! nl2br(e($viewdata->Use_Address ? $viewdata->Use_Address : '')) !!}</td>
                                        </tr>
                                         <tr>
                                            <th width="20%"><i class="fa fa-map-marker" aria-hidden="true"></i> City</th>
                                            <td>{{ $viewdata->Use_City ? $viewdata->Use_City : '' }}</td>
                                        </tr>
                                         <tr>
                                            <th width="20%"><i class="fa fa-map-marker" aria-hidden="true"></i> State</th>
                                            <td>{{ $viewdata->Use_State ? $viewdata->Use_State : '' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="20%"><i class="fa fa-map-marker" aria-hidden="true"></i> Country</th>
                                            <td>{{ $viewdata->Use_Country ? $viewdata->Use_Country : '' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="20%"><i class="fa fa-user" aria-hidden="true" ></i> Gender</th>
                                            <td>@if($viewdata->Use_Gender_Id == 1) {{ "Male" }} 
                                                @elseif($viewdata->Use_Gender_Id == 2) {{ "Female" }}
                                                @else {{ "Other" }}
                                                @endif
                                            </td>
                                        </tr>
                                         <tr>
                                            <th width="20%"><i class="fa fa-check" aria-hidden="true"></i> Status</th>
                                            <td>
                                                @if($viewdata->Use_Status == 0)
                                                <span style="color: green;"><b>Active</b></span> - <span>User is active.</span>
                                                @elseif($viewdata->Use_Status == 1)
                                                <span style="color: #a1a1a1;"><b>Inactive</b></span> - <span>User is Inactive..</span>
                                                @endif
                                            </td>
                                        </tr>  
                                        <tr>
                                            <th width="20%"><i class="fa fa-user" aria-hidden="true"></i> User Type</th>
                                            <td>{{ $viewdata->Utp_Name ? $viewdata->Utp_Name : '' }}</td>
                                        </tr>                                      
                                    </tbody>
                                </table>                                
                            </div>
                            <p>About User : {!! nl2br(e($viewdata->Use_About ? $viewdata->Use_About : '')) !!}</p>
                        </div>
                    </div>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
   @include('layouts.gridview-js')
</body>
</html>