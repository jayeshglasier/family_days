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
                <div class="panel-heading"><b> <i class="fa fa-envelope" aria-hidden="true"></i> Messages List</b></div>
                <!-- /.panel-heading -->
                <div class="panel-body" style="overflow:auto;">
                    <div class="dataTable_wrapper">
                        <form class="form-horizontal" role="form" method="get" action="{{ url('/users') }}">
                            
                            <input class="form-control" type="text" name="searchdata" value="{{ $searchdata ? $searchdata : '' }}" placeholder="Search Here..." id="searchdata-here">
                            @include('support.table-header-manu')
                            <select class="form-control" name="Asc_Desc_Select" required id="ascdesc-record">
                            <option value="Pnt_Id" @if($pageDescSelect == 'Pnt_Id') selected="true" @endif>Sort By Sr.No</option>
                        </select>
                            <input class="form-control" type="text" name="page" value="{{ $pageGoto ? $pageGoto : '' }}" placeholder="Go To" id="go-to-page">
                        </form>
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="thead-table">
                                <tr>
                                    <th class="text-center">Sr No.</th>
                                    <th>Date&Time</th>
                                    <th>Name</th>
                                    <th>Subject</th>
                                    <th>Message</th>
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
                                    <td>{{ str_limit($data->use_full_name,30) }}</td>
                                    <td>{{ str_limit($data->use_full_name,30) }}</td>
                                    <td>{{ str_limit($data->use_full_name,30) }}</td>
                                    <td>{{ str_limit($data->use_full_name,30) }}</td>
                                    <td class="text-center"  width="15%">
                                        <a href="{{ url('view-user-detail',$data->Use_User_Id) }}" data-toggle="tooltip" title="View User Detail !">
                                            <button class="btn btn-primary record-btn" style="background-color: #2a343c;"><i class="fa fa-users"></i></button>
                                        </a>
                                        <a href="{{ url('view-user-detail',$data->Use_User_Id) }}" data-toggle="tooltip" title="View User Detail !">
                                            <button class="btn btn-primary record-btn" style="background-color: #2a343c;"><i class="fa fa-pencil"></i></button>
                                        </a>
                                        <a href="{{ url('delete-user',$data->Use_Id) }}" onclick="return confirm('Are you sure for Delete ?')" data-toggle="tooltip" title="Delete User!">
                                            <button class="btn btn-danger record-btn"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        </a>
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