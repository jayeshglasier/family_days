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
    .nav-pills > li.active > a, .nav-pills > li.active > a:focus, .nav-pills > li.active > a:hover {
    color: #fff;
    background-color: black;
    }
    .activestatus {
        height: 25px;
        width: 25px;
        background-color: #09a849;
        border-radius: 50%;
        display: inline-block;
    }
    .inactivestatus {
        height: 25px;
        width: 25px;
        background-color: #da0e39;
        border-radius: 50%;
        display: inline-block;
    }
    a:focus, a:hover {
    color: #fff;
    text-decoration: underline;
    }
</style>
<div id="page-wrapper">
<div class="row">
    <div class="col-lg-6">
        <h4 class="page-header"></h4>
    </div>
    <!------------------------------------------------ Success Message Display Start Here ------------------------>
   @include('layouts.success-error-message')
    <!---------------------------------------------- Success Message Display Start Here ------------------------------->
</div>
<!---------------------------------------------- Datatable Code Start Here ------------------------------->
<div class="row">
         <!-- /.col-lg-6 -->
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">All Master</div>
        <!-- /.panel-heading -->
        <div class="panel-body" style="overflow:auto;">
            <!-- Nav tabs -->
            <ul class="nav nav-pills" style="margin-bottom: 5px; ">
                <li class="active"><a href="#user-type-pills" data-toggle="tab">User Type</a>
                </li>
                <li><a href="#database-pills" data-toggle="tab">Database</a>
                </li>
                <li><a href="#sidebar-manu-pills" data-toggle="tab">Sidebar Manu</a>
                </li>
                <li><a href="#sidebar-module-pills" data-toggle="tab">Sidebar Module</a>
                </li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane fade in active" id="user-type-pills">
                   @include('admin.setting.master-mgmt.master-user-type')
                </div>
                <div class="tab-pane fade" id="database-pills">
                    @include('admin.setting.master-mgmt.master-database')
                </div>
                <div class="tab-pane fade" id="sidebar-manu-pills">
                   @include('admin.setting.master-mgmt.master-sidebar-manu')
                </div>
                <div class="tab-pane fade" id="sidebar-module-pills">
                 @include('admin.setting.master-mgmt.master-sidebar-module')
                </div>
            </div>
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-6 -->
        
    </div>
</div>
<!---------------------------------------------- Datatable Code End Here ------------------------------->
</div>
<!-- /#wrapper -->
@include('layouts.gridview-js')
</body>

</html>
<script type="text/javascript">
    jQuery(document).ready(function(){
    jQuery('#addnewDatabase').on('click', function(event) {        
        jQuery('#contentDatabase').toggle('show');
    });
});
</script>
<script type="text/javascript">
    jQuery(document).ready(function(){
    jQuery('#addnewUsertype').on('click', function(event) {        
        jQuery('#contentUsertypes').toggle('show');
    });
});
</script>
<script type="text/javascript">
    jQuery(document).ready(function(){
    jQuery('#addnewSidebarManu').on('click', function(event) {        
        jQuery('#contentSidebarManu').toggle('show');
    });
});
</script>
<script type="text/javascript">
    jQuery(document).ready(function(){
    jQuery('#addnewSidebarModule').on('click', function(event) {        
        jQuery('#contentSidebarModule').toggle('show');
    });
});
</script>