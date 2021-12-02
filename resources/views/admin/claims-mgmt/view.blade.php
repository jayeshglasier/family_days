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
.hero-widget {
    text-align: left;
    padding-top: 10px;
    padding-bottom: 28px;
}
.hero-widget label {
    font-size: 17px;
    margin-left: 10px;
}
.nav-pills > li.active > a, .nav-pills > li.active > a:focus, .nav-pills > li.active > a:hover {
    color: #fff;
    background-color: orange;
    margin-bottom: 10px;
    margin-left: 15px;
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
        <!---------------------------- Success Message Display Start Here ------------------------------->
    </div>
     <!-- ---------------------------USER LIST TITLE BAR CODE End HERE -------------------------->

     <!-- ---------------------------USER LIST TABLE RECORD CODE START HERE -------------------------->

    <div class="row">
        <div class="col-lg-12" style="margin-top: -25px !important;padding-left: 1px !important;">
            <div class="panel panel-default" style="">
                <div class="panel-heading"><i class="fa fa-dashboard fa-fw"></i> Claims Details <a href="{{ url('/claims') }}" style="color:#fff;"><i style="float: right;font-size: 25px" class="fa fa-chevron-circle-left" aria-hidden="true"></i></a></div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-pills">
                        @if(count($datarecords) > 0)
                        <li class="" style="float: right;"><a class="btn btn-primary" style="float: right;padding: 8px 8px;" href="{{ url('claims-list-export-excel',['id'=>$familyId,'type'=>'xls']) }}" onclick="return confirm('Are you sure? You want to download the claims record.')" data-toggle="tooltip" title="To download excel file!!"><i class="fa fa-file-excel-o" aria-hidden="true"></i> Export Excel</a> </li>
                        @endif
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="home-pills">
                          @foreach($datarecords as $data)               
                           <div class="col-sm-6">
                                <div class="hero-widget well well-sm">
                                    <div class="icon">
                                        @if($data->red_icon)
                                            @if($data->bds_link)
                                            <a href="{{ $data->bds_link }}" target="_blank">
                                            <img src="{{ asset('public/images/reward-icon/').'/'.$data->red_icon }}" style="height: 80px;width: 80px;float: left" />
                                            @else
                                               <img src="{{ asset('public/images/reward-icon/').'/'.$data->red_icon }}" style="height: 70px;width: 70px;float: left" />
                                            @endif
                                        @else
                                        <img src="{{ asset('public/images/reward-icon/reward-default-icon.jpg') }}" style="height: 80px;width: 80px;float: left" />
                                        @endif
                                    </div>
                                    <div class="text">
                                        <label class="text-muted"><b>@if($data->red_cat_name == "Other") {{ $data->red_rewards_name }} @else {{ $data->red_cat_name }} @endif {{ '('.$data->red_brand_name.')' }} <small style="font-size: 12px;color: orange;"></small></b></label> <label style="float: right;color: orange">{{ $data->red_point }}<br>  
                                        </label></br>
                                        <label class="text-muted" style="font-size: 13px;"></label> <p style="float: right;">- {{ $data->use_full_name }}</p>
                                        <label class="text-muted" style="font-size: 13px;margin-left: 1px;">Date: {{ date('M-d-Y',strtotime($data->red_is_claim_date)) }}</label>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @include('support.pagination')
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
    </div>
    <!-- ---------------------------USER LIST TABLE RECORD CODE END HERE -------------------------->
</div>
</div>
</body>
</html>
@include('layouts.gridview-js')