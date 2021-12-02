@include('layouts.header')
@include('layouts.sidebar')
<link href="{{ asset('public/new-theam/jasny-bootstrap/css/jasny-bootstrap.css')}}" rel="stylesheet" />
<link href="{{ asset('public/bower_components/AdminLTE/plugins/daterangepicker/daterangepicker.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/bower_components/AdminLTE/plugins/datepicker/datepicker3.css')}}" rel="stylesheet" type="text/css" />
<link href="{{ asset('public/css/form-style.css') }}" rel="stylesheet">
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h3 class="page-header"></h3>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        
        <div class="col-lg-12" style="padding-left: 1px !important;">
            <div class="panel panel-default" style="margin-top: -25px !important;background-color: white !important;">
                <div class="panel-heading">
                    <b><i class="fa fa-cubes" aria-hidden="true"></i> Family Member List</b> <a href="{{ url('/familys') }}" style="color:#fff;"><i style="float: right;font-size: 25px" class="fa fa-chevron-circle-left" aria-hidden="true"></i></a>

                </div>
                <div class="panel-body">
                    @foreach($datarecords as $data)                    
                    <div class="col-sm-4">
                        <div class="hero-widget well well-sm" style="background-color: #fff;">
                            <label>@if($data->use_is_admin == 1) {{ "Admin" }}  @else {{ "-----" }} @endif</label>
                            <div class="icon">
                                  @if($data->use_image)
                                <img src="{{ asset('public/images/user-images').'/'.$data->use_image }}" style="height: 100px;border-radius: 15px;" />
                                @else
                                <img src="{{ asset('public/images/user-images/user-profile.png') }}" style="height: 100px;border-radius: 15px;" />
                                @endif
                            </div>
                            <div class="text">
                                <label class="text-muted" style="color: #12122d;">{{ str_limit($data->use_username,30) }}</label><br>
                                <label class="text-muted" style="color: #12122d;">{{ str_limit($data->full_name,30) }}</label><br>
                                <label class="text-muted" style="color: #12122d;">{{ str_limit($data->email ? $data->email:'---',30) }}</label><br>
                                <label class="text-muted" style="color: #515157;font-size: 15px;">Ph: {{ $data->use_phone_no ? $data->use_phone_no:'N/A' }}</label><br>
                                <label class="text-muted" style="color: #515157;font-size: 15px">Birthdate: {{ $data->use_dob ? $data->use_dob:'N/A' }}</label><br>
                            </div>
                            <div class="options">
                              <a href="{{ url('edit-family-member-detail',$data->use_family_id) }}" data-toggle="tooltip" title="Edit User Detail !">
                                    <button class="btn btn-primary record-btn" style="background-color: #2a343c;"><i class="fa fa-pencil"></i></button>
                                </a>  
                                <a href="javascript:;" class="btn btn-primary btn-lg">{{ $data->rol_name }}</a>
                                @if($data->use_is_family_head == 1)
                                @else
                                <a href="{{ url('family-member-delete',$data->use_family_id) }}" onclick="return confirm('Are you sure you want to delete this record?')" data-toggle="tooltip" title="Delete User!">
                                    <button class="btn btn-danger record-btn"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                </a>
                                @endif
                                <br>
                            </div>
                            <div>                                        
                            </div>
                        </div>
                    </div>
                    @endforeach
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