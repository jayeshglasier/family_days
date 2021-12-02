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
        background-color: #d8d8d8;
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
            <h3 class="page-header">Add Project Detail</h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b><i class="fa fa-cubes" aria-hidden="true"></i> Add Project Detail</b>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('save-projects') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                    <div class="col-lg-6">
                        <div class="form-group{{ $errors->has('Pro_Name') ? ' has-error' : '' }}">
                            <label for="Pro_Name" class="col-md-4 control-label">Name<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="Pro_Name" name="Pro_Name" placeholder="Project Name" value="{{ old('Pro_Name') }}" maxlength="100" autofocus>
                                @if ($errors->has('Pro_Name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Pro_Name') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Pro_Description') ? ' has-error' : '' }}">
                            <label for="Pro_Description" class="col-md-4 control-label">Description<font color="red">*</font></label>
                            <div class="col-md-8">
                                <textarea id="Pro_Description" type="text" class="form-control" name="Pro_Description" placeholder="Project Description" value="{{ old('Pro_Description') }}" >{{ old('Pro_Description') }}</textarea>
                                 @if ($errors->has('Pro_Description'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Pro_Description') }}</strong>
                                </span> @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Pro_Start_Date') ? ' has-error' : '' }}">
                            <label for="Pro_Start_Date" class="col-md-4 control-label">Start Date<font color="red">*</font></label>
                            <div class="col-md-8">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ date('d/m/Y') }}" name="Pro_Start_Date" class="form-control pull-right" id="datepicker">
                                </div>
                                @if ($errors->has('Pro_Start_Date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Pro_Start_Date') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Pro_End_Date') ? ' has-error' : '' }}">
                            <label for="Pro_End_Date" class="col-md-4 control-label">End Date<font color="red">*</font></label>
                            <div class="col-md-8">
                                 <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ date('d/m/Y') }}" name="Pro_End_Date" class="form-control pull-right" id="datepickerone">
                                </div>
                                @if ($errors->has('Pro_End_Date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Pro_End_Date') }}</strong>
                                </span> @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Pro_Status') ? ' has-error' : '' }}">
                            <label for="Pro_Status" class="col-md-4 control-label">Status<font color="red">*</font></label>
                            <div class="col-md-8">
                                <select class="form-control" name="Pro_Status">
                                    <option value="0">Active</option>
                                    <option value="1">Inactive</option>
                                    <option value="2">Completed</option>
                                    <option value="3">Hold</option>
                                    <option value="4">Cancelled</option>
                                </select>
                                @if ($errors->has('Pro_Status'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Pro_Status') }}</strong>
                                </span> @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-group{{ $errors->has('Pro_Com_Name') ? ' has-error' : '' }}">
                            <label for="Pro_Com_Name" class="col-md-4 control-label">Company Name<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input id="Pro_Com_Name" type="text" class="form-control" name="Pro_Com_Name" placeholder="Company Name" value="{{ old('Pro_Com_Name') }}" maxlength="140"> @if ($errors->has('Pro_Com_Name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Pro_Com_Name') }}</strong>
                                </span> @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Pro_Com_Address') ? ' has-error' : '' }}">
                            <label for="Pro_Com_Address" class="col-md-4 control-label">Company Address</label>
                            <div class="col-md-8">
                                <textarea id="Pro_Com_Address" type="text" class="form-control" name="Pro_Com_Address" placeholder="Company Address" value="{{ old('Pro_Com_Address') }}">{{ old('Pro_Com_Address') }}</textarea>
                                 @if ($errors->has('Pro_Com_Address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Pro_Com_Address') }}</strong>
                                </span> @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Pro_Com_Email_Id') ? ' has-error' : '' }}">
                            <label for="Pro_Com_Email_Id" class="col-md-4 control-label">E-Mail ID<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input id="Pro_Com_Email_Id" type="text" class="form-control phone" name="Pro_Com_Email_Id" placeholder="E-Mail ID" value="{{ old('Pro_Com_Email_Id') }}" onblur="validateEmail(this);" maxlength="90">
                                 @if ($errors->has('Pro_Com_Email_Id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Pro_Com_Email_Id') }}</strong>
                                </span> @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Pro_Com_Website') ? ' has-error' : '' }}">
                            <label for="Pro_Com_Website" class="col-md-4 control-label">Website</label>
                            <div class="col-md-8">
                                <input id="Pro_Com_Website" type="text" class="form-control" name="Pro_Com_Website" placeholder="Website" value="{{ old('Pro_Com_Website') }}" maxlength="240"> 
                                @if ($errors->has('Pro_Com_Website'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Pro_Com_Website') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Pro_Com_Mobile_No') ? ' has-error' : '' }}">
                            <label for="Pro_Com_Mobile_No" class="col-md-4 control-label">Contact Number<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input id="Pro_Com_Mobile_No" type="text" class="form-control" name="Pro_Com_Mobile_No" placeholder="Contact Number" value="{{ old('Pro_Com_Mobile_No') }}" onkeyup="validatephone(this);" maxlength="10">
                                @if ($errors->has('Pro_Com_Mobile_No'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Pro_Com_Mobile_No') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                         <div class="form-group{{ $errors->has('Pro_Com_Logo') ? ' has-error' : '' }}">
                            <label for="Pro_Com_Logo" class="col-md-4 control-label">Company Logo</label>
                            <div class="col-md-8">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
                                    <div>
                                        <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">Select image</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" name="Pro_Com_Logo">
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>&nbsp&nbsp&nbsp
                            <a class="btn btn-danger" href="{{ url('projects') }}"><i class="fa fa-times-circle"></i> Cancel</a>
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
<script language=Javascript>
      <!--
   $(document).ready(function () {
    //called when key is pressed in textbox
      $("#Pro_Com_Mobile_No").keypress(function (e) {
         //if the letter is not digit then display error and don't type anything
         if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
            //display error message
            $("#errmsg").html("Digits Only").show().fadeOut("slow");
                return false;
        }
       });
    });

    function validateEmail(emailField){
            var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;

            if (reg.test(emailField.value) == false) 
            {
                alert('Invalid Email Address');
                return false;
            }
            return true;
    }
      //-->
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