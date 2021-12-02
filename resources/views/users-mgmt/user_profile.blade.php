<!-- Main Header -->
@include('layouts.header')
<!-- Sidebar -->
@include('layouts.sidebar')
<link href="{{ asset('public/new-theam/jasny-bootstrap/css/jasny-bootstrap.css')}}" rel="stylesheet" />
<link href="{{ asset('public/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/bower_components/AdminLTE/plugins/datepicker/datepicker3.css')}}" rel="stylesheet" type="text/css" />
<style type="text/css">
    .col-md-8 {
        margin-bottom: -6px;
    }
    .panel-default {
        border-color: #c9b4b4;
    }
    .panel {
        margin-bottom: 20px;
        
    }
    .form-control {
        border: 1px solid #1d1111;
    }
    .control-label {
        font-size: 13px;
    }
</style>
<div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h3 class="page-header">Admin Profile Detail <a href="{{ url('/') }}" data-toggle="tooltip" title="Back To Home..!" style="float: right;margin-top:-8px; ">&nbsp&nbsp<button class="btn btn-primary"><b><i class="fa fa-chevron-circle-left" aria-hidden="true"></i> Dashboard</b></button></a>
                        <button class="btn btn-primary" data-toggle="tooltip" title="Edit User Profile !" id="editUserProfile" style="float: right;margin-top:-8px;"><b><i class="fa fa-user" aria-hidden="true"></i> Update Profile</b></button></h3>
                </div>
                <!-- /.col-lg-12 -->
            </div>
             <!-- /.row -->
            @if ($errors->has('password'))
            <span class="help-block" style="color: red;">
                <strong>{{ $errors->first('password') }}</strong>
            </span> 
            @endif

             <div class="row">
                <!----------- Success Message Display Start Here ----------->
                <div class="col-lg-6">
                    @if(session('success'))
                    <div class="flash-message" style="padding-top: 5px;">
                        <div class="alert alert-info" style="text-align: center;">
                            <span class="success-message"><big>{{ session('success') }}</big></span>
                        </div>
                    </div>
                    @endif @if (session('error'))
                    <div class="flash-message" style="padding-top: 5px;">
                        <div class="alert alert-danger" style="text-align: center;">
                            <span class="error-message"><big>{{ session('error') }}</big></span>
                        </div>
                    </div>
                    @endif
                </div>
                <!---------------- Success Message Display Start Here ------------------->
            </div>

            <div id='contentUserProfile' style="display: none;">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default" style="background-color: #f9f9f9;">
                            <div class="panel-heading">
                                <b><i class="fa fa-cubes" aria-hidden="true"></i> Edit Profile</b>
                            </div>
                            <div class="panel-body">
                                
                                <div class="col-lg-6">
                                    <form class="form-horizontal" role="form" method="POST" action="{{ url('update-users-profile') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                    <div class="form-group{{ $errors->has('use_full_name') ? ' has-error' : '' }}">
                                        <label for="use_full_name" class="col-md-4 control-label">Fullname<font color="red">*</font></label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="use_full_name" name="use_full_name" placeholder="User Name" value="{{ $user->use_full_name }}" maxlength="80" autofocus>
                                            @if ($errors->has('use_full_name'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('use_full_name') }}</strong>
                                            </span> 
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                        <label for="email" class="col-md-4 control-label">Email<font color="red">*</font></label>
                                        <div class="col-md-8">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Email Id" value="{{ $user->email }}" maxlength="90">
                                            @if ($errors->has('email'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span> 
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group{{ $errors->has('use_phone_no') ? ' has-error' : '' }}">
                                        <label for="use_phone_no" class="col-md-4 control-label">Contact Number<font color="red">*</font></label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control" id="use_phone_no" name="use_phone_no" placeholder="Mobile No" value="{{ $user->use_phone_no }}" maxlength="12" onkeyup="validatephone(this);">
                                            @if ($errors->has('use_phone_no'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('use_phone_no') }}</strong>
                                            </span> 
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>&nbsp&nbsp&nbsp
                                        <a class="btn btn-danger" href="{{ url('/') }}"><i class="fa fa-times-circle"></i> Cancel</a>
                                    </div>
                                </div>
                                    </form>
                                </div>
                                 
                               
                                <div class="col-lg-6">
                                    <form class="form-horizontal" role="form" method="POST" action="{{ url('change-admin-password') }}" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{ $user->id }}">
                                    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                        <label for="password" class="col-md-4 control-label">Password<font color="red">*</font></label>
                                        <div class="col-md-8">
                                            <input type="password" class="form-control" id="password" name="password" placeholder="************" value="{{ old('password') }}" maxlength="20" autocomplete="on" required>
                                            @if ($errors->has('password'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span> 
                                            @endif
                                        </div>
                                    </div>
                                     <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                        <label for="password_confirmation" class="col-md-4 control-label">Confirm Password<font color="red">*</font></label>
                                        <div class="col-md-8">
                                            <input type="password" class="form-control" id="password-confirm" name="password_confirmation" placeholder="************" value="{{ old('password_confirmation') }}" maxlength="20" required>
                                            @if ($errors->has('password_confirmation'))
                                            <span class="help-block">
                                                <strong>{{ $errors->first('password_confirmation') }}</strong>
                                            </span> 
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group">
                                    <div class="col-md-6 col-md-offset-4">
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Change Password</button>&nbsp&nbsp&nbsp
                                        <a class="btn btn-danger" href="{{ url('/') }}"><i class="fa fa-times-circle"></i> Cancel</a>
                                    </div>
                                </div>
                                     </form>
                                </div>
                               
                            </div>
                            <!-- /.col-lg-6 (nested) -->
                        </div>
                        <!-- /.row (nested) -->
                    </div>
                    <!-- /.panel-body -->
                </div>
            </div>
            <!-- /.panel -->
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h4><i class="fa fa-user" aria-hidden="true"></i> {{ $user->use_full_name ? $user->use_full_name : '' }}</h4>
                            <p>Below list to show <b>{{ $user->use_full_name ? $user->use_full_name : '' }}</b> full detail with description.</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <!-- Project Detail -->
                                        <tr>
                                            <th width="20%"><i class="fa fa-user" aria-hidden="true"></i> Name</th>
                                            <td>{{ $user->use_full_name ? $user->use_full_name : '' }}</td>
                                        </tr> 
                                        <tr>
                                            <th width="20%"><i class="fa fa-envelope" ></i> Email</th>
                                            <td>{{ $user->email ? $user->email : '' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="20%"><i class="fa fa-phone" aria-hidden="true"></i> Contact Number</th>
                                            <td>{{ $user->use_phone_no ? $user->use_phone_no : '' }}</td>
                                        </tr>
                                       
                                         <tr>
                                            <th width="20%"><i class="fa fa-check" aria-hidden="true"></i> Status</th>
                                            <td>
                                                @if($user->Use_Status == 0)
                                                <span style="color: green;"><b>Active</b></span>
                                                @elseif($user->Use_Status == 1)
                                                <span style="color: #a1a1a1;"><b>Inactive</b></span>
                                                @endif
                                            </td>
                                        </tr>  
                                        <tr>
                                            <th width="20%"><i class="fa fa-user" aria-hidden="true"></i> Role</th>
                                            <td>Admin</td>
                                        </tr>                                      
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
<script type="text/javascript">
    jQuery(document).ready(function(){
    jQuery('#editUserProfile').on('click', function(event) {        
        jQuery('#contentUserProfile').toggle('show');
    });
});
</script>
<script>
    $(document).ready(function(){
        $('[data-toggle="tooltip"]').tooltip();   
    });
</script>
<script type="text/javascript"> 
  $(document).ready( function() {
    $('.flash-message').delay(2000).fadeOut();
  });
</script>