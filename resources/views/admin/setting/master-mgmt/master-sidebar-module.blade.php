<div class="panel panel-default">
    <div class="panel-heading"><b>Sidebar Module List</b>
        <button class="btn btn-primary" style="float: right;margin-top: -7px;" id="addnewSidebarModule"><b>Add New </b><i class="fa fa-plus"></i></button>
    </div>
      <!-- ---------------------------------------------- SIDEBAR MODULE INSERT RECORD FORM START HERE -------------------------------------------- -->

     <div id='contentSidebarModule' style="display: none;">
        <form class="form-horizontal" role="form" method="POST" action="{{ url('save-sidebar-module') }}" style="margin-top: 20px;">
            {{ csrf_field() }}
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group{{ $errors->has('Mod_Parent_Id') ? ' has-error' : '' }}">
                        <label for="Mod_Parent_Id" class="col-md-4 control-label">Sidebar Manu</label>
                        <div class="col-md-8">
                            <select class="form-control" name="Mod_Parent_Id" required>
                                <option value="0">Select Sidebar Manu</option>
                                @foreach($datasidebarmanu as $sidebarmanu)
                                 <option value="{{ $sidebarmanu->Sid_Id }}">{{ $sidebarmanu->Sid_Manu_Name }}</option>
                                 @endforeach
                            </select>
                            @if ($errors->has('Mod_Parent_Id'))
                            <span class="help-block">
                                <strong>{{ $errors->first('Mod_Parent_Id') }}</strong>
                            </span> @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group{{ $errors->has('Mod_Name') ? ' has-error' : '' }}">
                        <label for="Mod_Name" class="col-md-4 control-label">Module Name<font color="red">*</font></label>
                        <div class="col-md-8">
                         <input type="text" class="form-control" id="Mod_Name" name="Mod_Name" placeholder="Module Name" value="{{ old('Mod_Name') }}" maxlength="60" required>
                            @if ($errors->has('Mod_Name'))
                            <span class="help-block">
                              <strong>{{ $errors->first('Mod_Name') }}</strong>
                          </span> @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group{{ $errors->has('Mod_Url_Name') ? ' has-error' : '' }}">
                        <label for="Mod_Url_Name" class="col-md-4 control-label">Url Parameter<font color="red">*</font></label>
                        <div class="col-md-8">
                         <input type="text" class="form-control" id="Mod_Url_Name" name="Mod_Url_Name" placeholder="Url Parameter" value="{{ old('Mod_Url_Name') }}" maxlength="60" required>
                            @if ($errors->has('Mod_Url_Name'))
                            <span class="help-block">
                              <strong>{{ $errors->first('Mod_Url_Name') }}</strong>
                          </span> @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group{{ $errors->has('Mod_Fa_Class_Name') ? ' has-error' : '' }}">
                        <label for="Mod_Fa_Class_Name" class="col-md-4 control-label">Icon <font color="red">*</font></label>
                        <div class="col-md-8">
                         <input type="text" class="form-control" id="Mod_Fa_Class_Name" name="Mod_Fa_Class_Name" placeholder="eg. Fa Fa-File" value="{{ old('Mod_Fa_Class_Name') }}" maxlength="60" required>
                            @if ($errors->has('Mod_Fa_Class_Name'))
                            <span class="help-block">
                              <strong>{{ $errors->first('Mod_Fa_Class_Name') }}</strong>
                          </span> @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                    <div class="form-group{{ $errors->has('Mod_Status') ? ' has-error' : '' }}">
                        <label for="Mod_Status" class="col-md-4 control-label">Status</label>
                        <div class="col-md-8">
                            <select class="form-control" name="Mod_Status" required>
                                <option value="0">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @if ($errors->has('Mod_Status'))
                            <span class="help-block">
                                <strong>{{ $errors->first('Mod_Status') }}</strong>
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

     <!-- ---------------------------------------------- sidebar-module INSERT RECORD FORM END HERE -------------------------------------------- -->

    <div class="panel-body" style="overflow:auto;">
        <div class="dataTable_wrapper">
            <table class="table table-striped table-bordered table-hover">
                <thead class="thead-table">
                    <tr>
                        <th style="text-align: center;">Sr.No</th>
                        <th>Module Name</th>
                        <th>Url</th>
                        <th>Icon</th>
                        <th style="text-align: center;">Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?> @foreach($datasidebarmodule as $datarecord) <?php $i++; ?>
                    <tr class="odd gradeX">
                        <td style="text-align: center;">{{ $i }}</td>
                        <td>{{ str_limit($datarecord->Mod_Name ? $datarecord->Mod_Name : '',40)  }}</td>
                        <td>{{ str_limit($datarecord->Mod_Url_Name ? $datarecord->Mod_Url_Name : '',40)  }}</td>
                        <td>{{ str_limit($datarecord->Mod_Fa_Class_Name ? $datarecord->Mod_Fa_Class_Name : '',40)  }}</td>
                        <td style="text-align: center;">@if($datarecord->Mod_Status == 0) <span class="activestatus"></span> @elseif($datarecord->Mod_Status == 1) <span class="inactivestatus"></span> @else {{ "none" }} @endif</td>
                        <td class="center" width="20%">
                            <button class="btn btn-primary record-btn" data-toggle="modal" data-target="#sidebarModule<?php echo $i; ?>"><i class="fa fa-edit"></i></button>
                           <!--  <a href="{{ url('delete-user-types',$datarecord->Utp_Id) }}" onclick="return confirm('Are you sure for Delete ?')" data-toggle="tooltip" title="Delete Project !">
                                <button class="btn btn-danger"><i class="fa fa-trash" aria-hidden="true"></i></button>
                            </a> -->
                        </td>

                       <!-- ------------------------------ sidebar-module UPDATE RECORD FORM START HERE ------------------------------- -->

                            <div class="modal fade" id="sidebarModule<?php echo $i; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog" style="width: 550px;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                            <h4 class="modal-title" id="myModalLabel">Edit Sidebar Module</h4>
                                        </div>
                                        <div class="modal-body">
                                        <form class="form-horizontal" role="form" method="POST" action="{{ url('update-sidebar-module') }}" style="margin-top: 20px;">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="Mod_Id"  value="{{ $datarecord->Mod_Id ? $datarecord->Mod_Id : '' }}">
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('Mod_Parent_Id') ? ' has-error' : '' }}">
                                                        <label for="Mod_Parent_Id" class="col-md-4 control-label" style="text-align: right;">Sidebar Manu<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                             <select class="form-control" name="Mod_Parent_Id" required>
                                                                <option value="0">Select Sidebar Manu</option>
                                                                @foreach($datasidebarmanu as $sidebarmanu)
                                                                 <option value="{{ $sidebarmanu->Sid_Id }}" @if($sidebarmanu->Sid_Id == $datarecord->Mod_Parent_Id) selected @endif>{{ $sidebarmanu->Sid_Manu_Name }}</option>
                                                                 @endforeach
                                                            </select>
                                                            @if ($errors->has('Mod_Parent_Id'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('Mod_Parent_Id') }}</strong>
                                                            </span> @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('Mod_Name') ? ' has-error' : '' }}">
                                                        <label for="Mod_Name" class="col-md-4 control-label" style="text-align: right;">Module Name<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                          <input type="text" class="form-control" id="Mod_Name" name="Mod_Name" placeholder="Module Name" value="{{ $datarecord->Mod_Name }}" maxlength="60" required>
                                                            @if($errors->has('Mod_Name'))
                                                            <span class="help-block">
                                                              <strong>{{ $errors->first('Mod_Name') }}</strong>
                                                            </span> 
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('Mod_Url_Name') ? ' has-error' : '' }}">
                                                        <label for="Mod_Url_Name" class="col-md-4 control-label" style="text-align: right;">Url Parameter<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                          <input type="text" class="form-control" id="Mod_Url_Name" name="Mod_Url_Name" placeholder="Url Parameter" value="{{ $datarecord->Mod_Url_Name }}" maxlength="60" required>
                                                            @if($errors->has('Mod_Url_Name'))
                                                            <span class="help-block">
                                                              <strong>{{ $errors->first('Mod_Url_Name') }}</strong>
                                                            </span> 
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('Mod_Fa_Class_Name') ? ' has-error' : '' }}">
                                                        <label for="Mod_Fa_Class_Name" class="col-md-4 control-label" style="text-align: right;">Icon<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                          <input type="text" class="form-control" id="Mod_Fa_Class_Name" name="Mod_Fa_Class_Name" placeholder="Icon" value="{{ $datarecord->Mod_Fa_Class_Name }}" maxlength="60" required>
                                                            @if($errors->has('Mod_Fa_Class_Name'))
                                                            <span class="help-block">
                                                              <strong>{{ $errors->first('Mod_Fa_Class_Name') }}</strong>
                                                            </span> 
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row" style="margin-bottom: 15px;">
                                                <div class="col-lg-12">
                                                    <div class="form-group{{ $errors->has('Mod_Status') ? ' has-error' : '' }}">
                                                        <label for="Mod_Status" class="col-md-4 control-label" style="text-align: right;">Sidebar Manu<font color="red">*</font></label>
                                                        <div class="col-md-8">
                                                             <select class="form-control" name="Mod_Status" required>
                                                                <option value="0" @if($datarecord->Mod_Status == 0) selected @endif>Active</option>
                                                                <option value="1" @if($datarecord->Mod_Status == 1) selected @endif>Inactive</option>
                                                            </select>
                                                            @if ($errors->has('Mod_Status'))
                                                            <span class="help-block">
                                                                <strong>{{ $errors->first('Mod_Status') }}</strong>
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

                        <!-- ------------------------------ sidebar-module UPDATE RECORD FORM END HERE ------------------------------- -->

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>