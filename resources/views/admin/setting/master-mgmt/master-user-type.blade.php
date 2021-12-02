<div class="panel panel-default">
    <div class="panel-heading"><b>User Types List</b>
        <button class="btn btn-primary" style="float: right;margin-top: -7px;" id="addnewUsertype"><b>Add New </b><i class="fa fa-plus"></i></button>
    </div>

      <!-- ---------------------------------------------- DATABASE INSERT RECORD FORM START HERE -------------------------------------------- -->

     <div id='contentUsertypes' style="display: none;">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('save-user-types') }}" style="margin-top: 20px;">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group{{ $errors->has('Utp_Name') ? ' has-error' : '' }}">
                        <label for="Utp_Name" class="col-md-4 control-label">User Type<font color="red">*</font></label>
                        <div class="col-md-8">
                          <input type="text" class="form-control" id="Utp_Name" name="Utp_Name" placeholder="User Type" value="{{ old('Utp_Name') }}" maxlength="30" required>
                            @if ($errors->has('Utp_Name'))
                            <span class="help-block">
                                <strong>{{ $errors->first('Utp_Name') }}</strong>
                            </span> @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group{{ $errors->has('Utp_Details') ? ' has-error' : '' }}">
                        <label for="Utp_Details" class="col-md-4 control-label">Description<font color="red">*</font></label>
                        <div class="col-md-8">
                         <textarea type="text" class="form-control" id="Utp_Details" name="Utp_Details" placeholder="Description" maxlength="240" required>{{ old('Utp_Details') }}</textarea>
                            @if ($errors->has('Utp_Details'))
                            <span class="help-block">
                              <strong>{{ $errors->first('Utp_Details') }}</strong>
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
                        <th>User Type</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?> @foreach($usertypedata as $datarecord) <?php $i++; ?>
                    <tr class="odd gradeX">
                        <td width="10%" style="text-align: center;">{{ $i }}</td>
                        <td>{{ str_limit($datarecord->Utp_Name ? $datarecord->Utp_Name : '',40)  }}</td>
                        <td>{{ str_limit($datarecord->Utp_Details ? $datarecord->Utp_Details : '',40)  }}</td>
                        <td class="center" width="20%">
                            <button class="btn btn-primary record-btn" data-toggle="modal" data-target="#user-typesModel<?php echo $i; ?>"><i class="fa fa-edit"></i></button>
                            <a href="{{ url('permission-user-types',$datarecord->Utp_Id) }}" data-toggle="tooltip" title="Set User Type Permission !">
                                <button class="btn btn-warning record-btn"><i class="fa fa-cog" aria-hidden="true"></i></button>
                            </a>
                           <!--  <a href="{{ url('delete-user-types',$datarecord->Utp_Id) }}" onclick="return confirm('Are you sure for Delete ?')" data-toggle="tooltip" title="Delete Project !">
                                <button class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </a> -->
                        </td>

                       <!-- ------------------------------ DATABASE UPDATE RECORD FORM START HERE ------------------------------- -->

                            <div class="modal fade" id="user-typesModel<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" style="width: 550px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title" id="myModalLabel">Edit User Type</h4>
                                        </div>
                                        <div class="modal-body">
                                        <form class="form-horizontal" role="form" method="POST" action="{{ url('update-user-types') }}" style="margin-top: 20px;">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="Utp_Id"  value="{{ $datarecord->Utp_Id ? $datarecord->Utp_Id : '' }}">
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('Utp_Name') ? ' has-error' : '' }}">
                                                        <label for="Utp_Name" class="col-md-4 control-label" style="text-align: right;">User Type<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                             <input type="text" class="form-control" id="Utp_Name" name="Utp_Name" placeholder="User Type" value="{{ $datarecord->Utp_Name }}" maxlength="30" required>
                                                            @if ($errors->has('Utp_Name'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('Utp_Name') }}</strong>
                                                            </span> 
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('Utp_Details') ? ' has-error' : '' }}">
                                                        <label for="Utp_Details" class="col-md-4 control-label" style="text-align: right;">Description<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                          <textarea type="text" class="form-control" id="Utp_Details" name="Utp_Details" placeholder="Description" maxlength="240" required>{{ $datarecord->Utp_Details }}</textarea>
                                                            @if ($errors->has('Utp_Details'))
                                                            <span class="help-block">
                                                              <strong>{{ $errors->first('Utp_Details') }}</strong>
                                                          </span> @endif
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