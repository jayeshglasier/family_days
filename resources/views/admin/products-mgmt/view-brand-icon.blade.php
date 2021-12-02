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
                    <h4 class="page-header">Brand Picture List:</h4>
                </div>
                <div class="col-lg-8" style="background-color: #fff;margin-left: 15px;">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('create-reward-sub-brand') }}" style="margin-top: 10px;" enctype="multipart/form-data">
                {{ csrf_field() }}
                <input id="bds_brand_id" type="hidden" class="form-control" name="bds_brand_id"  value="{{ $brands->brd_id }}">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="form-group{{ $errors->has('bds_brand_icon') ? ' has-error' : '' }}">
                           <label for="bds_brand_icon" class="col-md-5 control-label" style="text-align: right;">Upload Brand Picture<font color="red">*</font></label>
                            <div class="col-md-7">
                            <input id="bds_brand_icon" type="file" class="form-control" name="bds_brand_icon" required>
                             @if($errors->has('bds_brand_icon'))
                            <span class="help-block">
                                <strong>{{ $errors->first('bds_brand_icon') }}</strong>
                            </span> 
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-lg-6">
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times-circle-o" aria-hidden="true"></i> Reset</button>
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
                        <div class="panel-heading"><b> <i class="fa fa-bars" aria-hidden="true"></i> Brand Pictures</b></div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <tbody>
                                        <!-- Project Details -->
                                         <tr >
                                            <th width="10%">Brand Name</th>
                                            <td style="padding-top: 10px;">{{ $brands->brd_brand_name }}</td>
                                        </tr> 
                                    </tbody>
                                </table>
                                
                                @foreach($datarecords as $data)
                                <div class="col-lg-2" style="text-align: center;">                                        
                                    <div><img src="{{ asset('public/images/brand-icon').'/'.$data->bds_brand_icon }}" style="height: 150px;width: 100%;margin-bottom: 10px;margin-top: 10px;" />
                                    </div>
                                    <a href="{{ url('delete-reward-sub-brand',$data->bds_id) }}" onclick="return confirm('Are you sure you want to delete this picture?')" data-toggle="tooltip" title="Delete Brand Picture!"><button class="btn btn-danger record-btn" style="background-color:red !important;"><i class="fa fa-trash" aria-hidden="true"></i></button></a>
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