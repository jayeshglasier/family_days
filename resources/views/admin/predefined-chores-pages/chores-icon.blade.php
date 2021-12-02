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
.btn-success {
    color: #fff;
    background-color: #141814;
    border-color: #121712;
}
</style>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h4 class="page-header">Chores Icon List :</h4>
                </div>
                <div class="col-lg-8" style="background-color: #fff;margin-left: 15px;">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('create-chores-icons') }}" style="margin-top: 10px;" enctype="multipart/form-data">
                {{ csrf_field() }}
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group{{ $errors->has('chi_image') ? ' has-error' : '' }}">
                           <label for="chi_image" class="col-md-5 control-label" style="text-align: right;">Upload Chores Icon<font color="red">*</font></label>
                            <div class="col-md-7">
                            <input id="chi_image" type="file" class="form-control" name="chi_image" required>
                             @if($errors->has('chi_image'))
                            <span class="help-block">
                                <strong>{{ $errors->first('chi_image') }}</strong>
                            </span> 
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6">
                <div class="modal-footer">
                    <a href="{{ url('chores-icons') }}"><button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o" aria-hidden="true"></i> Reset</button></a>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> Save</button>
                </div>
            </div></div>
            </form>
        </div>
                <!-- /.col-lg-12 -->
            </div>
            <br>
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><b> <i class="fa fa-bars" aria-hidden="true"></i> Chores Icon</b></div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                @foreach($datarecords as $data)
                                <div class="col-lg-1" style="text-align: center;">                                        
                                    <div><img src="{{ asset('public/images/chore-icon').'/'.$data->chi_image }}" style="height: 80px;width: 100%;margin-bottom: 10px;margin-top: 10px;" />
                                    </div>
                                    <a href="{{ url('delete-chores-icon',$data->chi_id) }}" onclick="return confirm('Are you sure you want to delete this chores icon?')" data-toggle="tooltip" title="Delete Chores Icon!"><button class="btn btn-danger record-btn" style="background-color:red !important;"><i class="fa fa-trash" aria-hidden="true"></i></button></a>
                                </div>
                                 @endforeach                            
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