@include('layouts.header')
@include('layouts.sidebar')
<link href="{{ asset('public/new-theam/jasny-bootstrap/css/jasny-bootstrap.css')}}" rel="stylesheet" />
<link href="{{ asset('public/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/bower_components/AdminLTE/plugins/datepicker/datepicker3.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/css/form-style.css') }}" rel="stylesheet">
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header">Add New User</h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b><i class="fa fa-cubes" aria-hidden="true"></i> Add New User</b>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('save-users') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                    <div class="col-lg-6">
                        <div class="form-group{{ $errors->has('Use_User_Id') ? ' has-error' : '' }}">
                            <label for="Use_User_Id" class="col-md-4 control-label">User Id<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="Use_User_Id" name="Use_User_Id" placeholder="User Id" value="{{ $userId ? $userId : 'USR0001'}}" maxlength="10"  readonly>
                                @if ($errors->has('Use_User_Id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_User_Id') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Use_Full_Name') ? ' has-error' : '' }}">
                            <label for="Use_Full_Name" class="col-md-4 control-label">Username<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="Use_Full_Name" name="Use_Full_Name" placeholder="Username" value="{{ old('Use_Full_Name') }}" maxlength="80" autofocus>
                                @if ($errors->has('Use_Full_Name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_Full_Name') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Use_Email_Id') ? ' has-error' : '' }}">
                            <label for="Use_Email_Id" class="col-md-4 control-label">Email Id<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="email" class="form-control" id="Use_Email_Id" name="Use_Email_Id" placeholder="Email Id" value="{{ old('Use_Email_Id') }}" maxlength="90">
                                @if ($errors->has('Use_Email_Id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_Email_Id') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Use_Mobile_Phone') ? ' has-error' : '' }}">
                            <label for="Use_Mobile_Phone" class="col-md-4 control-label">Mobile No<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="Use_Mobile_Phone" name="Use_Mobile_Phone" placeholder="Mobile No" value="{{ old('Use_Mobile_Phone') }}" maxlength="10" onkeyup="validatephone(this);">
                                @if ($errors->has('Use_Mobile_Phone'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_Mobile_Phone') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                         <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="password" class="form-control" id="password" name="password" placeholder="************" value="{{ old('password') }}" maxlength="20">
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
                                <input type="password" class="form-control" id="password-confirm" name="password_confirmation" placeholder="************" value="{{ old('password_confirmation') }}" maxlength="20">
                                @if ($errors->has('password_confirmation'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Use_Gender_Id') ? ' has-error' : '' }}">
                            <label for="Use_Gender_Id" class="col-md-4 control-label">Gender<font color="red">*</font></label>
                            <div class="col-md-8">
                                <select class="form-control" name="Use_Gender_Id">
                                    <option value="1">Male</option>
                                    <option value="2">Female</option>
                                    <option value="3">Other</option>
                                </select>
                                @if ($errors->has('Use_Gender_Id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_Gender_Id') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Use_Birth_Date') ? ' has-error' : '' }}">
                            <label for="Use_Birth_Date" class="col-md-4 control-label">Birth Date<font color="red">*</font></label>
                            <div class="col-md-8">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ date('d/m/Y') }}" name="Use_Birth_Date" class="form-control pull-right" id="datepicker">
                                </div>
                                @if ($errors->has('Use_Birth_Date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_Birth_Date') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Use_Join_Date') ? ' has-error' : '' }}">
                            <label for="Use_Join_Date" class="col-md-4 control-label">Join Date<font color="red">*</font></label>
                            <div class="col-md-8">
                                 <div class="input-group date">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" value="{{ date('d/m/Y') }}" name="Use_Join_Date" class="form-control pull-right" id="datepickerone">
                                </div>
                                @if ($errors->has('Use_Join_Date'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_Join_Date') }}</strong>
                                </span> @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Use_User_Type') ? ' has-error' : '' }}">
                            <label for="Use_User_Type" class="col-md-4 control-label">User Type<font color="red">*</font></label>
                            <div class="col-md-8">
                                 <select class="form-control" name="Use_User_Type">
                                    <option value="">Select User Type</option>
                                    @foreach($usertypedata as $usertype)
                                    <option value="{{ $usertype->Utp_Id }}">{{ $usertype->Utp_Name }}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('Use_User_Type'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_User_Type') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                         <div class="form-group{{ $errors->has('Use_Address') ? ' has-error' : '' }}">
                            <label for="Use_Address" class="col-md-4 control-label">Address<font color="red">*</font></label>
                            <div class="col-md-8">
                                <textarea id="Use_Address" type="text" class="form-control" name="Use_Address" placeholder="Address">{{ old('Use_Address') }}</textarea>
                                 @if($errors->has('Use_Address'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_Address') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Use_Country') ? ' has-error' : '' }}">
                            <label for="Use_Country" class="col-md-4 control-label">Country<font color="red">*</font></label>
                            <div class="col-md-8">
                                <select class="form-control" name="Use_Country" id="Use_Country">
                                    <option value="">Select Country</option>
                                    @foreach($countrys as $country)
                                    <option value="{{ $country->Cnt_Id }}">{{ $country->Cnt_Name }}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('Use_Country'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_Country') }}</strong>
                                </span> @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Use_State') ? ' has-error' : '' }}">
                            <label for="Use_State" class="col-md-4 control-label">State<font color="red">*</font></label>
                            <div class="col-md-8">
                                 <select class="form-control state-cls" name="Use_State" id="Use_State">
                                    <option value="">Select State</option>
                                </select>
                                @if ($errors->has('Use_State'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_State') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Use_City') ? ' has-error' : '' }}">
                            <label for="Use_City" class="col-md-4 control-label">City<font color="red">*</font></label>
                            <div class="col-md-8">
                                  <input type="text" class="form-control" id="Use_City" name="Use_City" placeholder="City" value="{{ old('Use_City') }}" maxlength="50">
                                @if ($errors->has('Use_City'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_City') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('Use_About') ? ' has-error' : '' }}">
                            <label for="Use_About" class="col-md-4 control-label">About User</label>
                            <div class="col-md-8">
                                <textarea id="Use_About" type="text" class="form-control" name="Use_About" placeholder="About User">{{ old('Use_About') }}</textarea>
                                @if ($errors->has('Use_About'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('Use_About') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                         <div class="form-group{{ $errors->has('Use_Profile_Img') ? ' has-error' : '' }}">
                            <label for="Use_Profile_Img" class="col-md-4 control-label">Profile Image</label>
                            <div class="col-md-8">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
                                    <div>
                                        <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">Select image</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" name="Use_Profile_Img">
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
<script>
    $("#Use_Country").on('change',function(){
        var cntId = $(this).val();
        $.ajax({
            type : "POST",
            url : "{{ url('user/getcountry') }}",
            data : {
                _token: '{{ csrf_token() }}',
                Use_Country : cntId
            },
            dataType : "JSON",
            success : function(data){
                    $(".state-cls option").each(function() {
                        $(this).remove();
                    });
                    $(".city-cls option").each(function() {
                        $(this).remove();
                    });
                    var items = [];
                     $.each( data, function( key, val ) {
                        items.push( "<option value='" +this['Sts_Name']+"'>" + this['Sts_Name'] + "</option>" );
                     });
                     $("#Use_State").append('<option value="">-- Select State--</option>');

                    $("#Use_State").append(items);
                },
            error : function(error){
                console.log(error);
            }
        });
    });
</script>