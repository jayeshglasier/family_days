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
                <div class="panel-heading"><b> <i class="fa fa-user" aria-hidden="true"></i> Claim List</b></div>
                <!-- /.panel-heading -->
                <div class="panel-body" style="overflow:auto;">
                    <a class="btn btn-primary" style="float: right;" href="{{ url('claim-details-export-excel',['type'=>'xls']) }}" onclick="return confirm('Are you sure? You want to download the claim record.')" data-toggle="tooltip" title="To download excel file!!">Export Excel</a>
                    <div class="dataTable_wrapper">
                        <form class="form-horizontal" role="form" method="get" action="{{ url('/claims') }}">
                            <input class="form-control" type="text" name="searchdata" value="{{ $searchdata ? $searchdata : '' }}" placeholder="Search Here..." id="searchdata-here">
                            @include('support.table-header-manu')
                            <select class="form-control" name="Asc_Desc_Select" required id="ascdesc-record">
                            <option value="id" @if($pageDescSelect == 'id') selected="true" @endif>Sort By Sr.No</option>
                        </select>
                            <input class="form-control" type="text" name="page" value="{{ $pageGoto ? $pageGoto : '' }}" placeholder="Go To" id="go-to-page">
                        </form>
                        <table class="table table-striped table-bordered table-hover">
                            <thead class="thead-table">
                                <tr>
                                    <th class="text-center">Sr No.</th>
                                    <th>Family Id</th>
                                    <th>Family Name</th>
                                    <th class="text-center">No of Claim</th>
                                    <th class="text-center">Export Excel</th>
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
                                    <td width="15%">{{ $data->use_family_id }}</td>
                                    <td>{{ str_limit($data->use_family_name,30) }}</td>
                                    <td style="text-align: center;" width="15%">{{ $data->no_of_chore ? $data->no_of_chore:'0' }}</td>
                                    <td width="10%" class="text-center">
                                        @if($data->no_of_chore == 0) {{ '---'}} @else<a class="btn btn-primary" style="padding: 1px 6px;" href="{{ url('claims-list-export-excel',['id'=>$data->use_family_id,'type'=>'xls']) }}" onclick="return confirm('Are you sure? You want to download the claim record.')" data-toggle="tooltip" title="To download excel file!!"><i class="fa fa-file-excel-o" aria-hidden="true"></i> </a>@endif</td>
                                    <td class="text-center"  width="10%">
                                        <a href="{{ url('view-claims',$data->use_family_id) }}" data-toggle="tooltip" title="View Claim Details!">
                                            <button class="btn btn-primary record-btn" style="background-color: #2a343c;"><i class="fa fa-users"></i></button>
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