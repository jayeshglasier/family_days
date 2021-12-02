@include('layouts.header')
@include('layouts.sidebar')
<link href="{{ asset('public/new-theam/jasny-bootstrap/css/jasny-bootstrap.css')}}" rel="stylesheet" />
<link href="{{ asset('public/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/bower_components/AdminLTE/plugins/datepicker/datepicker3.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/css/form-style.css') }}" rel="stylesheet">
<style type="text/css">
    .user-information{
        text-align: right;
        font-weight: 600;
    }
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"></h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-6" style="padding-left: 1px !important;">
            <div class="panel panel-default" style="margin-top: -25px !important;background-color: white !important;">
                <div class="panel-heading">
                    <b><i class="fa fa-cubes" aria-hidden="true"></i> Update User Profile</b><a href="{{ url('/users') }}" style="color:#fff;"><i style="float: right;font-size: 25px" class="fa fa-chevron-circle-left" aria-hidden="true"></i></a>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('update-users') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden"  class="form-control" id="use_id" name="use_id" value="{{ $updatedata->id }}">
                        <input type="hidden"  class="form-control" id="use_user" name="use_user" value="user_profile">
                    <div class="col-lg-12">
                        <h4> Account Information</h4>
                        <div class="form-group{{ $errors->has('use_family_id') ? ' has-error' : '' }}">
                            <label for="use_family_id" class="col-md-3 control-label">Family Id</label>
                            <div class="col-md-8">
                                <input type="text"  class="form-control" id="use_family_id" name="use_family_id" placeholder="User Id" value="{{ $updatedata->use_family_id }}" maxlength="25"  readonly style="margin-left: -20px !important;">
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('use_full_name') ? ' has-error' : '' }}">
                            <label for="use_full_name" class="col-md-3 control-label">Fullname<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="use_full_name" name="use_full_name" placeholder="FullnameName" value="{{ $updatedata->use_full_name }}" maxlength="80" style="margin-left: -20px !important;" required>
                                @if ($errors->has('use_full_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('use_full_name') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-3 control-label">Email </label>
                            <div class="col-md-8">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email Id" value="{{ $updatedata->email }}" maxlength="90" style="margin-left: -20px !important;">
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('use_phone_no') ? ' has-error' : '' }}">
                            <label for="use_phone_no" class="col-md-3 control-label">Phone No</label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="use_phone_no" name="use_phone_no" placeholder="Mobile No" value="{{ $updatedata->use_phone_no }}" maxlength="12" onkeyup="validatephone(this);" style="margin-left: -20px !important;">
                            </div>
                        </div>
                        
                        <div class="form-group{{ $errors->has('use_dob') ? ' has-error' : '' }}">
                            <label for="use_dob" class="col-md-3 control-label">Birth Date</label>
                            <div class="col-md-8" style="margin-left: -20px !important;">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    @if($updatedata->use_dob)
                                    <input type="text" value="{{ date('M-d-Y', strtotime(str_replace('/', '-', $updatedata->use_dob))) }}" name="use_dob" class="form-control pull-right" id="datepicker">
                                    @else
                                    <input type="text" value="" name="use_dob" class="form-control pull-right" id="datepicker">
                                    @endif
                                </div>
                            </div>
                        </div>
                        <h4> User Picture</h4>
                        <div class="form-group{{ $errors->has('use_image') ? ' has-error' : '' }}">
                            <label for="use_image" class="col-md-3 control-label">User Picture</label>
                            <div class="col-md-8">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"><img src="{{ asset('public/images/user-images').'/'.$updatedata->use_image }}" style="height: 149px;" /></div>
                                    <div>
                                        <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">Choose a photo</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" name="use_image">
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-3">
                            <button type="submit" class="btn btn-primary" style="background-color: orange !important;"><i class="fa fa-save"></i> Update</button>&nbsp&nbsp&nbsp
                            <a class="btn btn-danger" href="{{ url('users') }}" style="background-color: #868585 !important;"><i class="fa fa-times-circle"></i> Cancel</a>
                        </div>
                    </div>
                    </form>
                </div>
                <!-- /.col-lg-6 (nested) -->
            </div>
            <!-- /.row (nested) -->
        </div>
        <!-- Begin User Change Password -->
        <div class="col-lg-6" style="padding-left: 1px !important;">
            <div class="panel panel-default" style="margin-top: -25px !important;background-color: white !important;">
                <div class="panel-heading"> <b><i class="fa fa-cubes" aria-hidden="true"></i> Change Password</b><a href="{{ url('/users') }}" style="color:#fff;"><i style="float: right;font-size: 25px" class="fa fa-chevron-circle-left" aria-hidden="true"></i></a>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('change-user-password') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input type="hidden" class="form-control" id="use_id" name="use_id" value="{{ $updatedata->id }}">
                        <div class="col-lg-12">
                            <h4> Change Password</h4>
                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-5 control-label">Password<font color="red">*</font></label>
                                <div class="col-md-7">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="**********" value="{{ old('password') }}" maxlength="25" style="margin-left: -20px !important;" required>
                                </div>
                            </div>
                            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                <label for="password_confirmation" class="col-md-5 control-label">Confirm Password<font color="red">*</font></label>
                                <div class="col-md-7">
                                    <input type="password" class="form-control" id="password-confirm" name="password_confirmation" placeholder="Confirm Password" maxlength="25" style="margin-left: -20px !important;" required>
                                    @if ($errors->has('password')) 
                                    <span class="help-block" style="margin-left: -20px !important;color: red;"><strong>{{ $errors->first('password') }}</strong></span> 
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-12 col-md-offset-4">
                                <button type="submit" class="btn btn-primary" style="background-color: orange !important;"><i class="fa fa-check"></i> Change Password</button>&nbsp&nbsp&nbsp <a class="btn btn-danger" href="{{ url('users') }}" style="background-color: #868585 !important;"><i class="fa fa-times-circle"></i> Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Begin User Information -->
             <div class="panel panel-default" style="margin-top: 10px !important;background-color: white !important;">
                <div class="panel-heading"> <b><i class="fa fa-cubes" aria-hidden="true"></i> User Information</b><a href="{{ url('/users') }}" style="color:#fff;"><i style="float: right;font-size: 25px" class="fa fa-chevron-circle-left" aria-hidden="true"></i></a>
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-bordered table-hover responsive">
                        <tbody>
                            <tr>
                                <td width="25%" class="user-information">Family Name</td>
                                <td>{{ $updatedata->use_family_name }}</td>
                            </tr>
                            <tr>
                                <td width="25%" class="user-information">Family Id</td>
                                <td>{{ $updatedata->use_family_id }}</td>
                            </tr>
                            <tr>
                                <td width="25%" class="user-information">Fullname</td>
                                <td>{{ $updatedata->use_full_name }}</td>
                            </tr>
                            <tr>
                                <td width="25%" class="user-information">Email</td>
                                <td>{{ $updatedata->email ? $updatedata->email:'NA' }}</td>
                            </tr>
                            <tr>
                                <td width="25%" class="user-information">Phone</td>
                                <td>{{ $updatedata->use_phone_no ? $updatedata->use_phone_no:'NA' }}</td>
                            </tr>
                            <tr>
                                <td width="25%" class="user-information">Birth Date</td>
                                <td>@if($updatedata->use_dob) {{ date('M-d-Y', strtotime(str_replace('/', '-', $updatedata->use_dob))) }} @else {{ 'NA' }} @endif</td>
                            </tr>
                            <tr>
                                <td width="25%" class="user-information">User Type</td>
                                <td>{{ $updatedata->rol_name }}</td>
                            </tr>
                            <tr>
                                <td width="25%" class="user-information">Status</td>
                                <td>@if($updatedata->use_status == 0) {{ "Active" }} @elseif($updatedata->use_status == 1) {{ "Inactive" }} @endif</td>
                            </tr>
                            <tr>
                                <td width="25%" class="user-information">Join Date</td>
                                <td>{{ date('M-d-Y', strtotime(str_replace('/', '-', $updatedata->created_at))) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
             <!-- End User Information -->
        </div>
    <!-- End User Change Password -->
    </div>
</div>
</div>
</div>
</div>
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

<script src="{{ asset ('public/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.js') }}" type="text/javascript" ></script>
<script src="{{ asset ('public/bower_components/AdminLTE/plugins/datepicker/bootstrap-datepicker.js') }}" type="text/javascript" ></script>

<script type="text/javascript">

    $('#datepicker').datepicker({  
       format: 'dd/mm/yyyy'
     }); 

    $('#datepickerone').datepicker({  
       format: 'dd/mm/yyyy'
     }); 

</script>
<script type="text/javascript">            
    function validatephone(phone)           
    {           
        var maintainplus = '';          
        var numval = phone.value            
        if ( numval.charAt(0)=='+' )            
        {           
            var maintainplus = '';          
        }           
        curphonevar = numval.replace(/[\\A-Za-z!"?$%^&\?\/<>?|`?\]\[]/g,'');      
        phone.value = maintainplus + curphonevar;           
        var maintainplus = '';          
        phone.focus;            
    }           
</script>