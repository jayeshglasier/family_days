@if(Auth::user()->use_role == 1)
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
<script type="text/javascript">
    $(window).load(function() {
    setTimeout(function(){ $('.loader').fadeOut('fast'); }, 50);
    })
</script>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header" style="margin: 40px 0 0px;">Dashboard</h3>
        </div>
    </div>
    <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                </div>
            </div>
        </div>
    </div>
</div>
    <div class="row">
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #ffa500;">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-user fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo (isset($totalUser) && $totalUser != '' ? $totalUser : '0') ?></div>
                            <div>Total User</div>
                        </div>
                    </div>
                </div>
                <a href="{{ url('users') }}">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading" style="background-color: #ffa500;">
                    <div class="row">
                        <div class="col-xs-3">
                            <i class="fa fa-users fa-5x"></i>
                        </div>
                        <div class="col-xs-9 text-right">
                            <div class="huge"><?php echo (isset($totalFamily) && $totalFamily != '' ? $totalFamily : '0') ?></div>
                            <div>Total Family</div>
                        </div>
                    </div>
                </div>
                <a href="{{ url('familys') }}">
                    <div class="panel-footer">
                        <span class="pull-left">View Details</span>
                        <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                        <div class="clearfix"></div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

</html>
@include('layouts.gridview-js')
</body>
</html>
@else
@if (session('error'))
<br>
<div class="flash-message" style="padding-top: 5px;">
    <div class="alert alert-danger" style="text-align: center;">
        <span class="error-message"><big>You have not right to login.</big></span>
    </div>
</div>
@endif
@include('auth.login');
@endif
