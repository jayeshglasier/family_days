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
</style>
    <body>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-6">
                    <h3 class="page-header">Below list to show Project full details with description.</h3>
                </div>
                <!------------------------------------------------ Success Message Display Start Here ------------------------>
                   @include('layouts.success-error-message')
                <!---------------------------------------------- Success Message Display Start Here ------------------------------->
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                 <div class="col-lg-12" style="margin-bottom: 5px;margin-top: -13px;">
                    <button class="btn btn-primary" data-toggle="tooltip" title="Add New Note !" id="addnewNote"><b>Add Note</b>&nbsp&nbsp<i class="fa fa-plus"></i></button>
                </div>
                <div id='content' style="display: none;">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('save-note-projects') }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="Pnt_Pro_Unique_Id" value="{{ $viewdata->Pro_Unique_Id }}">
                        <div class="col-lg-6">
                            <div class="form-group{{ $errors->has('Pnt_Notes') ? ' has-error' : '' }}">
                                <label for="Pnt_Notes" class="col-md-4 control-label">Project Note<font color="red">*</font></label>
                                <div class="col-md-8">
                                    <textarea id="Pnt_Notes" type="text" class="form-control" name="Pnt_Notes" placeholder="Project Note" value="{{ old('Pnt_Notes') }}" required></textarea>
                                    @if ($errors->has('Pnt_Notes'))
                                    <span class="help-block">
                                      <strong>{{ $errors->first('Pnt_Notes') }}</strong>
                                  </span> @endif
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-2">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>&nbsp&nbsp&nbsp
                                <a class="btn btn-danger" href="{{ url('view-projects/'.$viewdata->Pro_Unique_Id) }}"><i class="fa fa-times-circle"></i> Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <h4><i class="fa fa-cubes" aria-hidden="true"></i> Project Detail</h4>
                            <p>Below list to show Project full detail with description.</p>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <!-- Project Details -->
                                        <tr>
                                            <th width="20%"><i class="fa fa-folder-open-o" aria-hidden="true"></i> Name</th>
                                            <td>{{ $viewdata->Pro_Name ? $viewdata->Pro_Name : '' }}</td>
                                        </tr>
                                        <tr>
                                            <th width="20%"><i class="fa fa-bars" aria-hidden="true"></i> Description</th>
                                            <td>{!! nl2br(e($viewdata->Pro_Description ? $viewdata->Pro_Description : '')) !!}</td>
                                        </tr>
                                        <tr>
                                            <th width="20%"><i class="fa fa-calendar-o" aria-hidden="true"></i> Start Date</th>
                                            <td>{{ date('d-M-Y',strtotime($viewdata->Pro_Start_Date ? $viewdata->Pro_Start_Date : '')) }}</td>
                                        </tr>
                                        <tr>
                                            <th width="20%"><i class="fa fa-calendar-o" aria-hidden="true"></i> End Date</th>
                                            <td>{{ date('d-M-Y',strtotime($viewdata->Pro_End_Date ? $viewdata->Pro_End_Date : '')) }}</td>
                                        </tr>
                                         <tr>
                                            <th width="20%"><i class="fa fa-power-off" aria-hidden="true"></i> Status</th>
                                            <td>
                                                @if($viewdata->Pro_Status == 0)
                                                <span style="color: green;"><b>Active</b></span> - <span>Project is active and ongoing.</span>
                                                @elseif($viewdata->Pro_Status == 1)
                                                <span style="color: #a1a1a1;"><b>Inactive</b></span> - <span>Work on a project has been temporarily stopped due to scheduling or other issues within the company.</span>
                                                @elseif($viewdata->Pro_Status == 2)
                                                <span style="color: #448fea;"><b>Completed</b></span> - <span>Project have been completed.</span>
                                                @elseif($viewdata->Pro_Status == 3)
                                                <span style="color: #a1a1a1;"><b>Hold</b></span> - <span>Project are kept on hold because of non-payment by the client or any other reason.</span>
                                                @elseif($viewdata->Pro_Status == 4)
                                                <span style="color: red;"><b>Cancelled</b></span> - <span>Project work has been stopped indefinitely.</span>
                                                @endif
                                            </td>
                                        </tr>
                                        </tbody>
                                </table>
                                <h4><i class="fa fa-cubes" aria-hidden="true"></i> Company Detail</h4>
                                <p>Below list to show Company full detail with description.</p>
                                    <table class="table table-bordered table-striped">
                                        <tbody>
                                            <!-- Company Details -->
                                            <tr>
                                                <th width="20%"><i class="fa fa-university" aria-hidden="true"></i> Company Name</th>
                                                <td>{{ $viewdata->Pro_Com_Name ? $viewdata->Pro_Com_Name : '' }}</td>
                                            </tr>
                                            <tr>
                                                <th width="20%"><i class="fa fa-map-marker" aria-hidden="true"></i> Company Address</th>
                                                <td>{!! nl2br(e($viewdata->Pro_Com_Address ? $viewdata->Pro_Com_Address : '')) !!}</td>
                                            </tr>
                                            <tr>
                                                <th width="20%"><i class="fa fa-envelope-o" aria-hidden="true"></i> E-Mail</th>
                                                <td>{{ $viewdata->Pro_Com_Email_Id ? $viewdata->Pro_Com_Email_Id : '' }}</td>
                                            </tr>
                                             <tr>
                                                <th width="20%"><i class="fa fa-phone" aria-hidden="true"></i> Contact Number</th>
                                                <td>{{ $viewdata->Pro_Com_Mobile_No ? $viewdata->Pro_Com_Mobile_No : '' }}</td>
                                            </tr>
                                            <tr>
                                                <th width="20%"><i class="fa fa-globe" aria-hidden="true"></i> Website</th>
                                                <td>{{ $viewdata->Pro_Com_Website ? $viewdata->Pro_Com_Website : '' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <h4><i class="fa fa-cubes" aria-hidden="true"></i> Project Notes</h4>
                                <p>Below list to show project all notes.</p>
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <!-- Company Details -->
                                        @if(!$viewnotes->isEmpty())
                                        <?php $i=0; ?> @foreach($viewnotes as $note) <?php $i++; ?>
                                        <tr>
                                            <th width="2%">{{ $i }}.</th>
                                            <td>{!! nl2br(e($note->Pnt_Notes ? $note->Pnt_Notes : '')) !!}
                                            <a href="{{ url('delete-note-projects',$note->Pnt_Unique_Id) }}" onclick="return confirm('Are you sure for Delete ?')" data-toggle="tooltip" title="Delete Project Note !" style="float: right;">
                                                <button class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                            </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                          <tr>
                                            <th width="2%">#</th>
                                            <td>No any notes found</td>
                                        </tr>
                                        @endif
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
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->
   @include('layouts.gridview-js')
</body>

</html>
<script type="text/javascript">
    jQuery(document).ready(function(){
    jQuery('#addnewNote').on('click', function(event) {        
        jQuery('#content').toggle('show');
    });
});
</script>

