@include('layouts.header')
@include('layouts.sidebar')
<link href="{{ asset('public/new-theam/jasny-bootstrap/css/jasny-bootstrap.css')}}" rel="stylesheet" />
<link href="{{ asset('public/css/form-style.css') }}" rel="stylesheet">
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header">User Change Password</h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b><i class="fa fa-cubes" aria-hidden="true"></i> User Change Password</b>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('update-password') }}">
                        {{ csrf_field() }}
                    <input id="Use_Id" type="hidden" class="form-control" name="Use_Id" value="{{ $user->Use_Id }}">
                    <div class="col-lg-12">
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Email ID<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control"  value="{{ $user->Use_Email_Id }}" readonly>
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">New Password<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="password" class="form-control" id="password" name="password" placeholder="********" value="{{ old('password') }}" maxlength="20" autofocus>
                                @if ($errors->has('password'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password_confirmation" class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-8">
                                <input type="password" class="form-control" id="password-confirm" name="password_confirmation" placeholder="********" value="{{ old('password_confirmation') }}" maxlength="20">
                                @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Change Password</button>&nbsp&nbsp&nbsp
                            <a class="btn btn-danger" href="{{ url('user-management') }}"><i class="fa fa-times-circle"></i> Cancel</a>
                        </div>
                    </div>
                    </form>
                </div>
                <!-- /.col-lg-6 (nested) -->
            </div>
            <!-- /.row (nested) -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
</div>
<!-- /.row -->
</div>
<!-- /#page-wrapper -->

</div>
<!-- /#wrapper -->
<!---------------------- New Theam Javascript---------------->
</body>

</html>

<script src="{{ asset('public/new-theam/js/app.js')}}" type="text/javascript"></script>
<script src="{{ asset('public/new-theam/jasny-bootstrap/js/jasny-bootstrap.js')}}"></script>
<script src="{{ asset('public/new-theam/js/pages/form_examples.js')}}"></script>
<!---------------------- New Theam Javascript---------------->

<script src="{{ asset('public/js/jquery.min.js') }}"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{ asset('public/js/bootstrap.min.js') }}"></script>
<!-- Metis Menu Plugin JavaScript -->
<script src="{{ asset('public/js/metisMenu.min.js') }}"></script>
<!-- Custom Theme JavaScript -->
<script src="{{ asset('public/js/startmin.js') }}"></script>