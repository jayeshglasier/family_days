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
    <body>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6">
                    <h3 class="page-header">Set Sidebar Manu Permission </h3>
                </div>
                <!------------------------------------------------ Success Message Display Start Here ------------------------>
                   @include('layouts.success-error-message')
                <!---------------------------------------------- Success Message Display Start Here ------------------------------->
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-body">

                            <h4><i class="fa fa-cubes" aria-hidden="true"></i> Set/Update User Type Permission</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                    <form class="form-horizontal" role="form" method="POST" action="{{ url('permission-update-user-types') }}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="Usr_Type_Id" value="{{ $usertypeid->Utp_Id }}">
                                    @foreach($modulelist as $module)
                                    <tr>
                                        <th width="5%"><input type="checkbox" name="Usr_Mod_Id[]" style="width: 98px;" value="{{ $module->Mod_Id }}"
                                            @foreach($permissionassign as $modeileId)
                                                @if($modeileId->Usr_Mod_Id == $module->Mod_Id)
                                                {{ "checked" }}
                                                @endif
                                            @endforeach>
                                        </th>
                                        <td>{{ $module->Mod_Name ? $module->Mod_Name : '' }}</td>
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <th width="5%"></th>
                                        <td>
                                            <div class="form-group">
                                                <div class="">
                                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>&nbsp&nbsp&nbsp
                                                    <a class="btn btn-danger" href="{{ url('all-master') }}"><i class="fa fa-times-circle"></i> Cancel</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </form>
                                    </tbody>
                                </table>
                            </div>
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

