<div class="panel panel-default">
    <div class="panel-heading"><b>Database List</b>
        <button class="btn btn-primary" style="float: right;margin-top: -7px;" id="addnewDatabase"><b>Add New </b><i class="fa fa-plus"></i></button>
    </div>

      <!-- ---------------------------------------------- DATABASE INSERT RECORD FORM START HERE -------------------------------------------- -->

     <div id='contentDatabase' style="display: none;">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('save-database') }}" style="margin-top: 20px;">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group{{ $errors->has('Dab_Pro_Unique_Id') ? ' has-error' : '' }}">
                        <label for="Dab_Pro_Unique_Id" class="col-md-4 control-label">Project<font color="red">*</font></label>
                        <div class="col-md-8">
                            <select class="form-control" name="Dab_Pro_Unique_Id" required>
                                <option value="">Select Project</option>
                                @foreach($dataprojects as $project)
                                 <option value="{{ $project->Pro_Unique_Id }}">{{ $project->Pro_Name }}</option>
                                 @endforeach
                            </select>
                            @if ($errors->has('Dab_Pro_Unique_Id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('Dab_Pro_Unique_Id') }}</strong>
                            </span> @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group{{ $errors->has('Dab_Database_Name') ? ' has-error' : '' }}">
                        <label for="Dab_Database_Name" class="col-md-4 control-label">Database Name<font color="red">*</font></label>
                        <div class="col-md-8">
                         <input type="text" class="form-control" id="Dab_Database_Name" name="Dab_Database_Name" placeholder="Database Name" value="{{ old('Dab_Database_Name') }}" maxlength="100" required>
                            @if ($errors->has('Dab_Database_Name'))
                            <span class="help-block">
                              <strong>{{ $errors->first('Dab_Database_Name') }}</strong>
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

    <div class="panel-body">
        <div class="dataTable_wrapper">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-table">
                    <tr>
                        <th>Project</th>
                        <th>Database Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i; ?> @foreach($maindatabase as $datarecord) <?php $i++; ?>
                    <tr class="odd gradeX">
                        <td>{{ str_limit($datarecord->Pro_Name ? $datarecord->Pro_Name : '',40)  }}</td>
                        <td>{{ str_limit($datarecord->Dab_Database_Name ? $datarecord->Dab_Database_Name : '',40)  }}</td>
                        <td class="center" width="20%">
                            <button class="btn btn-primary record-btn" data-toggle="modal" data-target="#databaseModel<?php echo $i; ?>"><i class="fa fa-edit"></i></button>
                            <a href="{{ url('delete-projects',$datarecord->Pro_Unique_Id) }}" onclick="return confirm('Are you sure for Delete ?')" data-toggle="tooltip" title="Delete Project !">
                                <button class="btn btn-danger record-btn"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </a>
                        </td>

                       <!-- ------------------------------ DATABASE UPDATE RECORD FORM START HERE ------------------------------- -->

                            <div class="modal fade" id="databaseModel<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" style="width: 487px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title" id="myModalLabel">Edit Database</h4>
                                        </div>
                                        <div class="modal-body">
                                        <form class="form-horizontal" role="form" method="POST" action="{{ url('update-database') }}" style="margin-top: 20px;">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="Dab_Unique_Id"  value="{{ $datarecord->Dab_Unique_Id ? $datarecord->Dab_Unique_Id : '' }}">
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('Dab_Pro_Unique_Id') ? ' has-error' : '' }}">
                                                        <label for="Dab_Pro_Unique_Id" class="col-md-4 control-label" style="text-align: right;">Project<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                            <select class="form-control" name="Dab_Pro_Unique_Id" required>
                                                                @foreach($dataprojects as $project)
                                                                 <option value="{{ $project->Pro_Unique_Id }}" @if($project->Pro_Unique_Id == $datarecord->Dab_Pro_Unique_Id) selected @endif>{{ $project->Pro_Name }}</option>
                                                                 @endforeach
                                                            </select>
                                                            @if ($errors->has('Dab_Pro_Unique_Id'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('Dab_Pro_Unique_Id') }}</strong>
                                                            </span> 
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('Dab_Database_Name') ? ' has-error' : '' }}">
                                                        <label for="Dab_Database_Name" class="col-md-4 control-label" style="text-align: right;">Database Name<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                         <input type="text" class="form-control" id="Dab_Database_Name" name="Dab_Database_Name" placeholder="Database Name" value="{{ $datarecord->Dab_Database_Name ? $datarecord->Dab_Database_Name : '' }}" maxlength="100" required>
                                                            @if ($errors->has('Dab_Database_Name'))
                                                            <span class="help-block">
                                                              <strong>{{ $errors->first('Dab_Database_Name') }}</strong>
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