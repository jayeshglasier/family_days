<div class="panel panel-default">
    <div class="panel-heading"><b>Sidebar Manu List</b>
        <button class="btn btn-primary" style="float: right;margin-top: -7px;" id="addnewSidebarManu"><b>Add New </b><i class="fa fa-plus"></i></button>
    </div>
      <!-- ---------------------------------------------- DATABASE INSERT RECORD FORM START HERE -------------------------------------------- -->

     <div id='contentSidebarManu' style="display: none;">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('save-sidebar-manu') }}" style="margin-top: 20px;">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group{{ $errors->has('Sid_Manu_Name') ? ' has-error' : '' }}">
                        <label for="Sid_Manu_Name" class="col-md-4 control-label">Sidebar Manu<font color="red">*</font></label>
                        <div class="col-md-8">
                          <input type="text" class="form-control" id="Sid_Manu_Name" name="Sid_Manu_Name" placeholder="Sidebar Manu" value="{{ old('Sid_Manu_Name') }}" maxlength="30" required>
                            @if ($errors->has('Sid_Manu_Name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('Sid_Manu_Name') }}</strong>
                            </span> @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-6 col-md-offset-2">
                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Submit</button>&nbsp&nbsp&nbsp
                    <a class="btn btn-danger" href="{{ url('all-master') }}"><i class="fa fa-times-circle"></i> Cancel</a>
                </div>
            </div>
        </form>
    </div>

     <!-- ---------------------------------------------- DATABASE INSERT RECORD FORM END HERE -------------------------------------------- -->

    <div class="panel-body" style="overflow:auto;">
        <div class="dataTable_wrapper">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-table">
                    <tr>
                        <th style="text-align: center;">Sr.No</th>
                        <th>Sidebar Manu</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?> @foreach($datasidebarmanu as $datarecord) <?php $i++; ?>
                    <tr class="odd gradeX">
                        <td width="10%" style="text-align: center;">{{ $i }}</td>
                        <td>{{ $datarecord->Sid_Manu_Name ? $datarecord->Sid_Manu_Name : ''  }}</td>
                        <td class="center" width="20%">
                            <button class="btn btn-primary record-btn" data-toggle="modal" data-target="#SidebarManuModel<?php echo $i; ?>"><i class="fa fa-edit"></i></button>
                           <!--  <a href="{{ url('delete-SidebarManu',$datarecord->Utp_Id) }}" onclick="return confirm('Are you sure for Delete ?')" data-toggle="tooltip" title="Delete Project !">
                                <button class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </a> -->
                        </td>

                       <!-- ------------------------------ DATABASE UPDATE RECORD FORM START HERE ------------------------------- -->

                            <div class="modal fade" id="SidebarManuModel<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" style="width: 550px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title" id="myModalLabel">Edit Sidebar Manu</h4>
                                        </div>
                                        <div class="modal-body">
                                        <form class="form-horizontal" role="form" method="POST" action="{{ url('update-sidebar-manu') }}" style="margin-top: 20px;">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="Sid_Id"  value="{{ $datarecord->Sid_Id ? $datarecord->Sid_Id : '' }}">
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('Sid_Manu_Name') ? ' has-error' : '' }}">
                                                        <label for="Sid_Manu_Name" class="col-md-4 control-label" style="text-align: right;">Sidebar Manu<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                             <input type="text" class="form-control" id="Sid_Manu_Name" name="Sid_Manu_Name" placeholder="Sidebar Manu" value="{{ $datarecord->Sid_Manu_Name }}" maxlength="30" required>
                                                            @if ($errors->has('Sid_Manu_Name'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('Sid_Manu_Name') }}</strong>
                                                            </span> 
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal">Cancle</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                        </div>
                                    </div>
                                    <!-- /.modal-content -->
                                </div>
                                <!-- /.modal-dialog -->
                            </div>

                        <!-- ------------------------------ DATABASE UPDATE RECORD FORM END HERE ------------------------------- -->

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>