@include('layouts.header')
@include('layouts.sidebar')
<link href="{{ asset('public/new-theam/jasny-bootstrap/css/jasny-bootstrap.css')}}" rel="stylesheet" />
<link href="{{ asset('public/css/form-style.css') }}" rel="stylesheet">
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-6" style="margin-top: -30px;">
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
   
    <!-- /.row -->
    <div class="row" style="margin-left: -30px;">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <b><i class="fa fa-cubes" aria-hidden="true"></i>Setting</b>
                </div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('store-system-setting') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}
                    <div class="col-lg-8">
                        <div class="form-group{{ $errors->has('sys_name') ? ' has-error' : '' }}">
                            <label for="sys_name" class="col-md-4 control-label">System Name<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="sys_name" name="sys_name" placeholder="System Name" value="{{ isset($editdata->sys_name) ? $editdata->sys_name : '' }}" maxlength="100">
                                @if ($errors->has('sys_name'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sys_name') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('sys_min_chores') ? ' has-error' : '' }}">
                            <label for="sys_min_chores" class="col-md-4 control-label">Minimum Chores Point<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="sys_min_chores" name="sys_min_chores" placeholder="00" value="{{ isset($editdata->sys_min_chores) ? $editdata->sys_min_chores : '' }}" maxlength="4" onkeyup="validatephone(this);">
                                @if ($errors->has('sys_min_chores'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sys_min_chores') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('sys_max_chores') ? ' has-error' : '' }}">
                            <label for="sys_max_chores" class="col-md-4 control-label">Maximum Chores Point<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="sys_max_chores" name="sys_max_chores" placeholder="00" value="{{ isset($editdata->sys_max_chores) ? $editdata->sys_max_chores : '' }}" maxlength="4" onkeyup="validatephone(this);">
                                @if ($errors->has('sys_max_chores'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sys_max_chores') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('sys_min_reward') ? ' has-error' : '' }}">
                            <label for="sys_min_reward" class="col-md-4 control-label">Minimum Reward Point<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="sys_min_reward" name="sys_min_reward" placeholder="00" value="{{ isset($editdata->sys_min_reward) ? $editdata->sys_min_reward : '' }}" maxlength="4" onkeyup="validatephone(this);">
                                @if ($errors->has('sys_min_reward'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sys_min_reward') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('sys_max_reward') ? ' has-error' : '' }}">
                            <label for="sys_max_reward" class="col-md-4 control-label">Maximum Reward Point<font color="red">*</font></label>
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="sys_max_reward" name="sys_max_reward" placeholder="00" value="{{ isset($editdata->sys_max_reward) ? $editdata->sys_max_reward : '' }}" maxlength="4" onkeyup="validatephone(this);">
                                @if ($errors->has('sys_max_reward'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('sys_max_reward') }}</strong>
                                </span> 
                                @endif
                            </div>
                        </div>
                         <div class="form-group" style="margin-left: -46px;">
                            <label for="sys_logo" class="col-md-4 control-label">Upload Logo</label>
                            <div class="col-md-8">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 200px; height: 150px;"></div>
                                    <div>
                                        <span class="btn btn-default btn-file">
                                        <span class="fileinput-new">Select file</span>
                                        <span class="fileinput-exists">Change</span>
                                        <input type="file" name="sys_logo">
                                        </span>
                                        <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>     
                    </div>

                    <div class="col-lg-4">  
                        <div class="form-group">
                            <label for="sys_logo" class="col-md-4 control-label">Old Logo</label>
                            <div class="col-md-8">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                     <img src="{{ asset('public/images/logo').'/'.$editdata->sys_logo }}" style="height: 150px;width:150px;" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
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
<script type="text/javascript"> 
  $(document).ready( function() {
    $('.flash-message').delay(3000).fadeOut();
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