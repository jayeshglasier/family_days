@extends('layouts.app')
@section('css')
	<link rel="stylesheet" href="{{ asset('public/assets/bundles/summernote/summernote-bs4.css') }}">
    <style>
        .btn-width-100 {
            width:100px;
        }
        .table:not(.table-sm):not(.table-md):not(.dataTable) td, .table:not(.table-sm):not(.table-md):not(.dataTable) th {
            padding: 5px 5px 5px 5px !important;
            height: 9px !important;
            vertical-align: middle;
        }
    </style>
@endsection

@section('content')
<div class="section-body" style="margin-top: -20px;">
    <?php
        $hour = date('H');
        $dayTerm = ($hour >= 6 && $hour <= 11) ? "Good Morning" : (($hour > 11 && $hour <= 16) ? "Good Afternoon" : (($hour > 16 && $hour <= 23) ? "Good Evening" : "Hello" ) );
    ?>
    <div class="row mb-1">
        <div class="col-lg-6 col-md-6 d-none d-md-block">
            <h4>{{ $dayTerm }}, {{ Auth::user()->name }}</h4>
        </div>
        <div class="col-lg-6 col-md-6">
            <h3 class="page-header float-right" style="margin-bottom: 0px; margin-right: 30px;">
                @if($forgetTimerStop == 0)
                    @if(count($timesheets)>0 && $timesheets->last()->status == 1 )
                        <input type="button" value="Start" id="start" class="btn btn-success btn-width-100" />
                        <input type="button" value="Stop" id="stop" class="btn btn-danger btn-width-100" style="display:none;" />
                    @elseif(count($timesheets)==0)
                        <input type="button" value="Start" id="start" class="btn btn-success btn-width-100" />
                        <input type="button" value="Stop" id="stop" class="btn btn-danger btn-width-100" style="display:none;" />
                    @else
                        <input type="button" value="Start" id="start" class="btn btn-success btn-width-100" style="display:none;" />
                        <input type="button" value="Stop" id="stop" class="btn btn-danger btn-width-100" />
                    @endif
                @else
                    {{-- <input type="button" value="Start" id="start" class="btn btn-success" /> --}}
                    <input type="button" data-toggle="modal" data-href="#updateDailyTimer" href="#updateDailyTimer" value="Add Reason and End Time (Update Timer)" class="btn btn-danger" />
                @endif
            </h3>
            <h2 id="timer" class="page-header float-right mr-3">00:00:00</h2>
        </div>

    </div>
    <div class="row">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                <div class="card-content">
                                    <h5 class="font-15">Yesterday Total Time</h5>
                                    <h5 class="mb-3 mt-4 font-25">{{ $yesterdayTime->total_time ? $yesterdayTime->total_time : '00:00:00' }}</h5>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                <div class="banner-img">
                                    <img src="{{ asset('public/assets/img/banner/3.png') }}" alt="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                <div class="card-content">
                                    <h5 class="font-15">Comming Soon...</h5>
                                    <h2 class="mb-3 mt-4 font-25">0</h2>
                                    <a href="#">View Details</a>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                <div class="banner-img">
                                    <img src="{{ asset('public/assets/img/banner/3.png') }}" alt="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                <div class="card-content">
                                    <h5 class="font-15">Projects</h5>
                                    <h2 class="mb-3 mt-4 font-25">{{ $projectCount }}</h2>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                <div class="banner-img">
                                    <img src="{{ asset('public/assets/img/banner/3.png') }}" alt="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6 col-xs-12">
            <div class="card">
                <div class="card-statistic-4">
                    <div class="align-items-center justify-content-between">
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pr-0 pt-3">
                                <div class="card-content">
                                    <h5 class="font-15">Total Pending Task</h5>
                                    <h2 class="mb-3 mt-4 font-25">{{ $countPendingTasks }}</h2>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6 pl-0">
                                <div class="banner-img">
                                    <img src="{{ asset('public/assets/img/banner/4.png') }}" alt="" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

     @if(Auth::user()->type_id == 5 || Auth::user()->type_id == 3)
    {{-- start staff and manager show data --}}
        <div class="row">
            <div class="col-12 col-md-7 col-lg-7">
                <div class="card">
                    <div class="card-header">
                        <h4>Other Task : {{ date('d-M-Y') }}</h4>
                        <div class="card-header-action float-right">
                            <a data-toggle="modal" data-href="#addTaskModel" href="#addTaskModel" class="btn btn-primary float-right">Add Task <i class="fa fa-plus"></i></a>
                        </div>
                    </div>
                    <div class="card-body pl-4 pr-4">
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <tr>
                                    <th>Sr. No</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Difference</th>
                                    <th>Project</th>
                                    <th>Description</th>
                                    <th>Action</th>
                                </tr>
                                <?php $i = 0; ?> @forelse($datarecords as $datarecord)<?php $i++; ?>
                                    <tr>
                                        <td>{{ $i }}</td>
                                        <td>{{ date('h:i A',strtotime($datarecord->tsk_start_time)) }}</td>
                                        <td>{{ date('h:i A',strtotime($datarecord->tsk_end_time)) }}</td>
                                        <td><b>{{ $datarecord->tsk_time_difference }}</b></td>
                                        <td>{{ $datarecord->pro_name }}</td>
                                        <td>{!! nl2br(e($datarecord->tsk_description ? $datarecord->tsk_description : '')) !!}</td>
                                        <td><a class="btn btn-sm btn-primary" data-toggle="modal" data-href="#updateTaskModel" href="#updateTaskModel{{ $i }}"><i class="fa fa-edit"></i></a></td>
                                    </tr>
                                    {{-- start edit task model --}}
                                    <div class="modal fade" id="updateTaskModel{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true" data-backdrop="static">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="formModal">Edit Other Task</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="form-horizontal" role="form" method="POST" action="{{ url('update-tasks') }}">
                                                        @csrf
                                                        <input type="hidden" value="updateTaskModel{{ $i }}" name="model_name" />
                                                        <input type="hidden" value="{{ $datarecord->tsk_id }}" name="tsk_id">
                                                        <div class="form-group">
                                                            <label for="tsk_pro_unique_id">Select Project<span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <select class="form-control" name="tsk_pro_unique_id" required>
                                                                    <option value="">Select Project</option>
                                                                    @foreach($projects as $project)
                                                                        <option value="{{ $project->pro_unique_id }}" @if($project->pro_unique_id == $datarecord->tsk_pro_unique_id) selected @endif>{{ $project->pro_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('tsk_pro_unique_id')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="tsk_description">Description<span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <textarea id="tsk_description" type="text" class="form-control @error('tsk_description') is-invalid @enderror" name="tsk_description" style="height: 150px;">{{ $datarecord->tsk_description }}</textarea>
                                                                @error('tsk_description')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <input type="hidden" value="{{ date('d-m-Y') }}" name="tsk_date" class="form-control" required readonly />
                                                        </div>
                                                        <div class="form-group{{ $errors->has('tsk_start_time') ? ' has-error' : '' }}">
                                                            <label for="tsk_start_time">Start Time<span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-clock"></i>
                                                                    </div>
                                                                </div>
                                                                <input type="time" name="tsk_start_time" value="{{ old('tsk_start_time', date('H:i',strtotime($datarecord->tsk_start_time))) }}" class="form-control @error('tsk_start_time') is-invalid @enderror" required />
                                                                @error('tsk_start_time')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="tsk_end_time">End Time<span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-clock"></i>
                                                                    </div>
                                                                </div>
                                                                <input type="time" value="{{ old('tsk_end_time', date('H:i',strtotime($datarecord->tsk_end_time))) }}" name="tsk_end_time" class="form-control @error('tsk_end_time') is-invalid @enderror" required />
                                                                @error('tsk_end_time')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <button type="submit" class="btn btn-primary m-t-15 waves-effect">Update</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end edit task model --}}
                                @empty
                                <tr>
                                    <td colspan="8">
                                 <div class="card" style="margin-bottom: 2px;padding: 0px;box-shadow:none;">
                                    <div class="card-body">
                                        <div class="empty-state" data-height="200" style="height: 200px;">
                                            <div class="empty-state-icon">
                                                <i class="fas fa-file"></i>
                                            </div>
                                            <h2>Other Task</h2>
                                            <p class="lead">
                                                Note : 1. Every 1 or 2 hrs task is mandatory to be added in portal with full description. 2. Mention lunch break time. 3. It is mandatory to add time whenever you have meeting / call with client.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            </tr>
                                @endforelse
                                <tr><td colspan="8" class="text-center"><b>Total Hours : {{ $taskTotalHours->total_time }}</b> </td></tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-5 col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <h4>Today Time Activity</h4>
                    </div>
                    <div class="card-body pl-4 pr-4">
                        <div class="table-responsive">
                            <table class="table table-striped" id="showStaffTime">
                                <thead>
                                    <tr>
                                        <th class="text-center">Start Time</th>
                                        <th class="text-center">End Time</th>
                                        <th class="text-center">Duration</th>
                                    </tr>
                                </thead>
                                <tbody id="ajax-table"></tbody>
                            </table>
                            <table class="table table-striped" id="showStaffReport">
                                <thead>
                                    <tr>
                                        <th class="text-center">Start Time</th>
                                        <th class="text-center">End Time</th>
                                        <th class="text-center">Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($timesheets)>0)
                                        @foreach($timesheets as $timesheet)
                                        <tr>
                                            <td class="text-center">{{date('h:i:s A',strtotime($timesheet->created_at))}}</td>
                                            <td class="text-center">@if($timesheet->status == 1){{date('h:i:s A',strtotime($timesheet->updated_at))}}@else{{ '---'}} @endif</td>
                                            <td class="text-center"> <b>{{$timesheet->time_diffence }}</b></td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center">00:00:00</td>
                                            <td class="text-center">00:00:00</td>
                                            <td class="text-center"><b>00:00:00</b></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="col-12 text-center font-weight-bold text-dark">
                                Total Time: <span id="totaltime">00:00:00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {{-- end staff and manager show data --}}
    @endif

    @if(Auth::user()->type_id == 5 || Auth::user()->type_id == 3)
    {{-- start staff and manager show data --}}
        <div class="row">
            <div class="col-12 col-md-7 col-lg-7" data-height="200" style="height: 200px;margin-bottom: 150px;">
                <div class="card">
                    <div class="card-header">
                        {{-- for staff --}}
                        <h4>Sticky Notes <small>(Personal Notes)</small></h4>
                        <div class="card-header-action float-right">
                            <a data-toggle="modal" data-href="#addNotesModel" href="#addNotesModel" class="btn btn-primary float-right">
                                <i class="fa fa-plus"></i> Add Notes
                            </a>
                        </div>
                    </div>
                    <div class="card-body pl-4 pr-4" id="StickyNotesBody">
                        <div id="accordion">
                            <?php $i=1; ?>
                            @forelse ($stickyNotes as $stickyNote) <?php $i++; ?>
                                <div class="accordion">
                                    <div class="accordion-header" role="button" data-toggle="collapse" data-target="#panel-body-{{ $i }}" >
                                        <h4>{{ $stickyNote->stk_title }}</h4>
                                    </div>
                                    <div class="accordion-body collapse" id="panel-body-{{ $i }}" data-parent="#accordion">
                                        <p class="mb-0">
                                            {!! $stickyNote->stk_content !!}
                                            <a href="{{ url('delete-sticky-notes',$stickyNote->stk_unique_id) }}" class="btn btn-danger float-right" onclick="return confirm('Are you sure you want to delete this record?')" data-toggle="tooltip" title="Delete record!" style="line-height: 14px;padding: 0.3rem 0.4rem;"><i class="far fa-trash-alt font-13"></i></a>
                                            <a data-toggle="modal" data-href="#editNotesModel-{{ $i }}" href="#editNotesModel-{{ $i }}" class="btn btn-primary float-right ml-2 mr-1" style="line-height: 14px;padding: 0.3rem 0.4rem;"><i class="fas fa-edit font-13"></i></a>
                                        </p>
                                    </div>
                                </div>
                                {{-- start edit notes model --}}
                                <div class="modal fade" id="editNotesModel-{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg" style="width: 44%;" role="document">
                                         <div class="modal-content">
                                              <div class="modal-header">
                                                    <h5 class="modal-title" id="formModal">Edit Notes</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                         <span aria-hidden="true">&times;</span>
                                                    </button>
                                              </div>
                                              <div class="modal-body">
                                                    <form class="form-horizontal" role="form" method="POST" action="{{ url('edit-sticky-notes') }}">
                                                         @csrf
                                                         <input type="hidden" value="editNotesModel-{{ $i }}" name="model_name" />
                                                         <input type="hidden" value="{{ $stickyNote->stk_unique_id }}" name="stk_unique_id" />
                                                         <div class="form-group">
                                                              <label for="stk_title">Notes Title <span class="text-danger">*</span></label>
                                                              <div class="input-group">
                                                                    <input type="text" name="stk_title" id="stk_title" value="{{ $stickyNote->stk_title }}" class="form-control" required>
                                                                    @error('stk_title')
                                                                         <div class="invalid-feedback"> {{ $message }} </div>
                                                                    @enderror
                                                              </div>
                                                         </div>
                                                         <div class="form-group">
                                                              <label for="stk_content">Content <span class="text-danger">*</span></label>
                                                              <div class="input-group">
                                                                    <textarea class="form-control" name="stk_content" required rows="5" style="height: 100%;">{{ $stickyNote->stk_content }}</textarea>
                                                                    @error('stk_content')
                                                                         <div class="invalid-feedback"> {{ $message }} </div>
                                                                    @enderror
                                                              </div>
                                                         </div>
                                                         <p><span class="text-danger">*</span> Represent that it's compulsory fields</p>
                                                        <button type="submit" class="btn btn-primary waves-effect float-right" style="margin-top: -40px;">Update</button>
                                                    </form>
                                              </div>
                                         </div>
                                    </div>
                                </div>
                                {{-- end edit notes model --}}
                            @empty
                                <div class="card" style="margin-bottom: 2px;padding: 0px;box-shadow:none;">
                                    <div class="card-body">
                                        <div class="empty-state" data-height="200" style="height: 200px;">
                                            <div class="empty-state-icon">
                                                <i class="fas fa-file"></i>
                                            </div>
                                            <h2>We couldn't find any sticky notes</h2>
                                            <p class="lead">
                                                Sorry we can't find any sticky notes, to get rid of this message, Please to insert one sticky note.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            @endforelse
                        </div>
                        @if (count($stickyNotes) > 1)
                            <div class="col-12 text-center mb-2">
                                <button class="btn btn-primary btn-sm rounded-pill" onclick="seeMoreStickyNotes(this)">See More</button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-5 col-lg-5">
            </div>
        </div>
    {{-- end staff and manager show data --}}
    @endif

    {{-- for Hr and admin show data --}}
    @if(Auth::user()->type_id == 2 || Auth::user()->type_id == 4)
        <div class="row">
            {{-- start Today Staff Activity  --}}
            <div class="col-12 col-md-6 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Today Staff Office Timer </h4>
                        <div class="card-header-action">
                            <form class="form-horizontal" role="form" name="report-filter-page" method="get" action="{{ url('dashboard') }}">
                                <input type="text" name="tsk_date" id="tsk_date" value="{{ $dateFilter ? $dateFilter : date('d-m-Y') }}" class="form-control"  style="border-radius: 5px;"/>
                            </form>
                        </div>
                    </div>
                    <div class="card-body pl-2 pr-2">
                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <thead>
                                    <tr>
                                        <th class="text-center">Sr.No</th>
                                        <th>Staff Name</th>
                                        <th class="text-center">Start Time</th>
                                        <th class="text-center">End Time</th>
                                        <th class="text-center">Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($staffTimesheet)>0)
                                 <?php $i=0; ?> @foreach($staffTimesheet as $timesheet) <?php $i++; ?>
                                    <tr>
                                        <td class="text-center">{{$i}}</td>
                                        <td style="padding: 5px 15px;">{{$timesheet['name']}}</td>
                                        @if($timesheet['createdAt'])

                                        <?php $date = date('Y-m-d 10:30:00',strtotime($timesheet['createdAt'])); ?>
                                        @if(strtotime($date) > strtotime($timesheet['createdAt']))
                                        <td class="text-center" style="padding: 5px 15px;">{{date('h:i:s A',strtotime($timesheet['createdAt']))}}</td>
                                        @else
                                        <td class="text-center" style="padding: 5px 15px;color: red !important;">{{date('h:i:s A',strtotime($timesheet['createdAt']))}}</td>
                                        @endif
                                        @else
                                        <td class="text-center" style="padding: 5px 15px;">---</td>
                                        @endif
                                        @if($timesheet['updatedAt'])

                                        @if($timesheet['status'] == 1)
                                        <?php $enddate = date('Y-m-d 18:30:00',strtotime($timesheet['updatedAt'])); ?>

                                        @if(strtotime($enddate) > strtotime($timesheet['updatedAt']))
                                        <td class="text-center" style="padding: 5px 15px;color: red !important;">{{date('h:i:s A',strtotime($timesheet['updatedAt']))}}</td>
                                        @else
                                        <td class="text-center" style="padding: 5px 15px;">{{date('h:i:s A',strtotime($timesheet['updatedAt']))}}</td>
                                        @endif
                                        @else
                                        <td class="text-center" style="padding: 5px 15px;">---</td>
                                        @endif
                                        @else
                                        <td class="text-center" style="padding: 5px 15px;">---</td>
                                        @endif
                                        @if($timesheet['timeDiffence'])

                                        @if($timesheet['timeDiffence'] == "00:00:00")
                                        <td class="text-center" style="padding: 5px 15px;">---</td>
                                        @else
                                        <td class="text-center" style="padding: 5px 15px;">{{$timesheet['timeDiffence'] }}</td>
                                        @endif
                                        @else
                                        <td class="text-center" style="padding: 5px 15px;">---</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td class="text-center" style="padding: 5px 15px;"><b>---</b></td>
                                        <td class="text-center" style="padding: 5px 15px;">00:00:00</td>
                                        <td class="text-center" style="padding: 5px 15px;">00:00:00</td>
                                        <td class="text-center" style="padding: 5px 15px;"><b>00:00:00</b></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            {{-- start Today Staff Activity  --}}

            {{-- start Late Days Record --}}
            <div class="col-12 col-md-6 col-lg-6">
            	<div class="col-12 col-md-12 col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Today Time Activity</h4>
                    </div>
                    <div class="card-body pl-4 pr-4">
                        <div class="table-responsive">
                            <table class="table table-striped" id="showStaffTime">
                                <thead>
                                    <tr>
                                        <th class="text-center">Start Time</th>
                                        <th class="text-center">End Time</th>
                                        <th class="text-center">Duration</th>
                                    </tr>
                                </thead>
                                <tbody id="ajax-table"></tbody>
                            </table>
                            <table class="table table-striped" id="showStaffReport">
                                <thead>
                                    <tr>
                                        <th class="text-center">Start Time</th>
                                        <th class="text-center">End Time</th>
                                        <th class="text-center">Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($timesheets)>0)
                                        @foreach($timesheets as $timesheet)
                                        <tr>
                                            <td class="text-center">{{date('h:i:s A',strtotime($timesheet->created_at))}}</td>
                                            <td class="text-center">@if($timesheet->status == 1){{date('h:i:s A',strtotime($timesheet->updated_at))}}@else{{ '---'}} @endif</td>
                                            <td class="text-center"> <b>{{$timesheet->time_diffence }}</b></td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td class="text-center">00:00:00</td>
                                            <td class="text-center">00:00:00</td>
                                            <td class="text-center"><b>00:00:00</b></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="col-12 text-center font-weight-bold text-dark">
                                Total Time: <span id="totaltime">00:00:00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="card">
                    <div class="card-header">
                        {{-- for admin --}}
                        <h4>Sticky Notes <small>(Personal Notes)</small></h4>
                        <div class="card-header-action float-right">
                            <a data-toggle="modal" data-href="#addNotesModel" href="#addNotesModel" class="btn btn-primary float-right">
                                <i class="fa fa-plus"></i> Add Notes
                            </a>
                        </div>
                    </div>
                    <div class="card-body pl-4 pr-4" id="StickyNotesBody">
                        <div id="accordion">
                            <?php $i=1; ?>
                            @forelse ($stickyNotes as $stickyNote) <?php $i++; ?>
                                <div class="accordion">
                                    <div class="accordion-header" role="button" data-toggle="collapse" data-target="#panel-body-{{ $i }}" >
                                        <h4>{{ $stickyNote->stk_title }}</h4>
                                    </div>
                                    <div class="accordion-body collapse" id="panel-body-{{ $i }}" data-parent="#accordion">
                                        <p class="mb-0">
                                            {!! $stickyNote->stk_content !!}
                                            <a href="{{ url('delete-sticky-notes',$stickyNote->stk_unique_id) }}" class="text-danger float-right "><i class="far fa-trash-alt font-18"></i></a>
                                            <a data-toggle="modal" data-href="#editNotesModel-{{ $i }}" href="#editNotesModel-{{ $i }}" class="text-warning float-right ml-2 mr-1"><i class="fas fa-edit font-18"></i></a>
                                        </p>
                                    </div>
                                </div>
                                {{-- start edit notes model --}}
                                <div class="modal fade" id="editNotesModel-{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg" style="width: 44%;" role="document">
                                         <div class="modal-content">
                                              <div class="modal-header">
                                                    <h5 class="modal-title" id="formModal">Edit Notes</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                         <span aria-hidden="true">&times;</span>
                                                    </button>
                                              </div>
                                              <div class="modal-body">
                                                    <form class="form-horizontal" role="form" method="POST" action="{{ url('edit-sticky-notes') }}">
                                                         @csrf
                                                         <input type="hidden" value="editNotesModel-{{ $i }}" name="model_name" />
                                                         <input type="hidden" value="{{ $stickyNote->stk_unique_id }}" name="stk_unique_id" />
                                                         <div class="form-group">
                                                              <label for="stk_title">Notes Title <span class="text-danger">*</span></label>
                                                              <div class="input-group">
                                                                    <input type="text" name="stk_title" id="stk_title" value="{{ $stickyNote->stk_title }}" class="form-control" required>
                                                                    @error('stk_title')
                                                                         <div class="invalid-feedback"> {{ $message }} </div>
                                                                    @enderror
                                                              </div>
                                                         </div>
                                                         <div class="form-group">
                                                              <label for="stk_content">Content <span class="text-danger">*</span></label>
                                                              <div class="input-group">
                                                                    <textarea class="form-control" name="stk_content" required>
                                                                        {!! $stickyNote->stk_content !!}
                                                                    </textarea>
                                                                    @error('stk_content')
                                                                         <div class="invalid-feedback"> {{ $message }} </div>
                                                                    @enderror
                                                              </div>
                                                         </div>
                                                         <button type="submit" class="btn btn-primary m-t-15 waves-effect float-right">Submit</button>
                                                    </form>
                                              </div>
                                         </div>
                                    </div>
                                </div>
                                {{-- end edit notes model --}}
                            @empty
                            @endforelse
                        </div>
                        @if (count($stickyNotes) > 1)
                            <div class="col-12 text-center mb-2">
                                <button class="btn btn-primary btn-sm rounded-pill" onclick="seeMoreStickyNotes(this)">See More</button>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h4>Staff Late Days Record</h4>
                    </div>
                    <div class="card-body pl-4 pr-4">
                        <form class="form-horizontal" role="form" name="report-less-time" method="get" action="{{ url('dashboard') }}" style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-6" style="margin: 0px -15px 0px 10px;">
                                    <select class="form-control" name="user_id" id="userId" required style="">
                                        <option value="">Select Staff</option>
                                        @foreach($staffList as $staff)
                                            <option value="{{ $staff['id'] }}" @if($staff[ 'id']==$userId) selected="true" @endif>{{ $staff['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3" style="margin: 0px -15px 0px 0px;">
                                    <input type="text" value="{{ $startDate ? $startDate : date('d-m-Y') }}" name="start_date" class="form-control" required id="datepicker3">
                                </div>
                                <div class="col-md-3" style="margin: 0px -15px 0px 0px;">
                                    <input type="text" value="{{ $endDate ? $endDate : date('d-m-Y') }}" name="end_date" class="form-control" required id="datepicker2">
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <thead>
                                    <tr>
                                       <th>Date</th>
                                        <th colspan="2">Reason</th>
                                    </tr>
                                </thead>
                                <tbody>@if(count($lateTimeStaff)>0)
                                    <?php $i=0 ; ?>@foreach($lateTimeStaff as $latetime) <?php $i++; ?>
                                    <tr>
                                        <td width="50%">{{date('d-m-Y h:i:s A , l',strtotime($latetime['created_at']))}}</td>
                                        <td style="border-right-color:white !important;">{{ $latetime['reason'] ? $latetime['reason'] : '---' }}</td>
                                        <td class="cls-text-center" width="5%">
                                            <button class="btn btn-primary" data-toggle="modal" data-href="#addStaffReason" href="#addStaffReason<?php echo $i; ?>" style="padding: 2px 8px;"><i class="fa fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    {{-- start edit Staff Late Days Record model --}}
                                    <div class="modal fade" id="addStaffReason{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="formModal">Edit Reason</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="form-horizontal" role="form" method="POST" action="{{ url('add-reason') }}">
                                                        @csrf
                                                        <input type="hidden" value="addStaffReason{{ $i }}" name="model_name" />
                                                        <input type="hidden" value="{{ $latetime['task_id'] }}" name="task_id">
                                                        <div class="form-group">
                                                            <label for="reason">Description <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <textarea type="text" class="form-control @error('reason') is-invalid @enderror" name="reason" style="height: 150px;" required="">{{ $latetime['reason'] }}</textarea>
                                                                @error('reason')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary m-t-15 waves-effect">Update</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end Staff Late Days Record model --}}
                                    @endforeach
                                    @else
                                    <tr>
                                        <td width="50%"><b>---</b></td>
                                        <td colspan="2">---</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td colspan="3"><b>Total Late Days : {{count($lateTimeStaff)}}</b></td>
                                    <tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">
                        <h4>Update Staff Timer Record </h4>
                        <div class="card-header-action">
                            <button class="btn btn-primary" data-toggle="modal" data-href="#addStaffTimerModel" href="#addStaffTimerModel"><b>Add Staff Timer</b> <i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="card-body pl-4 pr-4">
                        <form class="form-horizontal" role="form" name="update-timer-report" method="get" action="{{ url('dashboard') }}" style="margin-bottom: 10px;">
                            <div class="row">
                                <div class="col-md-6" style="margin: 0px -15px 0px 10px;">
                                    <select class="form-control" name="timer_user_id" id="updateTimerId" required style="">
                                        <option value="">Select Staff {{ $userIdTimer }}</option>
                                        @foreach($staffList as $staff)
                                            <option value="{{ $staff->id }}" @if($staff->id == $userIdTimer) selected="true" @endif>{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3" style="margin: 0px -15px 0px 0px;">
                                    <input type="text" value="{{ $startDate ? $startDate : date('d-m-Y') }}" name="timer_date" class="form-control" required id="datepicker4">
                                </div>
                            </div>
                        </form>

                        <div class="table-responsive">
                            <table class="table table-striped table-md">
                                <thead>
                                    <tr>
                                        <th class="text-center">Start Time</th>
                                        <th class="text-center">End Time</th>
                                        <th class="text-center" colspan="2">Duration</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($staffTimerList)>0)
                                    <?php $i=0 ; ?>@foreach($staffTimerList as $staffTimer) <?php $i++; ?>
                                    <tr>
                                        <td class="text-center">{{ date('h:i:s A',strtotime($staffTimer->created_at )) }}</td>
                                        <td class="text-center">{{ date('h:i:s A',strtotime($staffTimer->updated_at )) }}</td>
                                        <td class="text-center" style="border-right-color: white;">{{ $staffTimer->time_diffence }}</td>
                                        <td class="cls-text-center" width="5%">
                                            <button class="btn btn-primary" data-toggle="modal" data-href="#updateTimerRecord" href="#updateTimerRecord<?php echo $i; ?>" style="padding: 2px 8px;"><i class="fa fa-edit"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    {{-- start edit Staff Late Days Record model --}}
                                    <div class="modal fade" id="updateTimerRecord{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="formModal">Update Staff Timer</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form class="form-horizontal" role="form" method="POST" action="{{ url('update-staff-daily-timer') }}">
                                                        @csrf
                                                        <input type="hidden" value="updateTimerRecord{{ $i }}" name="model_name" />
                                                        <input type="hidden" value="{{ $staffTimer->id }}" name="timer_id" />
                                                        <div class="form-group">
                                                            <label for="user_id">Select Staff <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <select class="form-control @error('user_id') is-invalid @enderror" name="user_id" required disabled>
                                                                    <option value="">Select Staff</option>
                                                                    @foreach($staffList as $staff)
                                                                        <option value="{{ $staff['id'] }}" @if($staffTimer->user_id == $staff['id']) selected @endif>{{ $staff['name'] }}</option>
                                                                    @endforeach
                                                                </select>
                                                                @error('user_id')
                                                                    <div class="invalid-feedback"> {{ $message }} </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="reason">Reason</label>
                                                            <div class="input-group">
                                                                <textarea id="reason" type="text" class="form-control @error('reason') is-invalid @enderror" name="reason" style="height: 100px;">{{ $staffTimer->reason }}</textarea>
                                                                @error('reason')
                                                                    <div class="invalid-feedback"> {{ $message }} </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="date">Date <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <input type="text" value="{{ $staffTimer->date ? date('d-m-Y',strtotime($staffTimer->date)) : date('d-m-Y') }}" name="date" class="form-control @error('reason') is-invalid @enderror datepicker-enddate" required/>
                                                                @error('date')
                                                                    <div class="invalid-feedback"> {{ $message }} </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="start_time">Start Time <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text">
                                                                        <i class="fas fa-clock"></i>
                                                                    </div>
                                                                </div>
                                                                <input type="time" name="start_time" value="{{ date('H:i',strtotime($staffTimer->created_at)) ?: date('H:i:s A') }}" class="form-control @error('start_time') is-invalid @enderror"  required />
                                                                @error('start_time')
                                                                    <div class="invalid-feedback"> {{ $message }} </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="end_time">End Time <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text">
                                                                    <i class="fas fa-clock"></i>
                                                                    </div>
                                                                </div>
                                                                <input type="time" value="{{ date('H:i',strtotime($staffTimer->updated_at)) ?: date('H:i:s A') }}" name="end_time" class="form-control @error('end_time') is-invalid @enderror" required />
                                                                @error('end_time')
                                                                    <div class="invalid-feedback"> {{ $message }} </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary m-t-15 float-right">Update</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end Staff Late Days Record model --}}
                                    @endforeach
                                    @else
                                    <tr>
                                        <td class="text-center"><b>---</b></td>
                                        <td class="text-center"><b>---</b></td>
                                        <td class="text-center"><b>---</b></td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            {{-- end Late Days Record --}}

            {{-- start Update Staff Timer Record --}}

            {{-- end Update Staff Timer Record --}}

        </div>
    {{-- end hr and admin show data --}}
    @endif
</div>
{{-- start add task model --}}
<div class="modal fade" id="addTaskModel" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModal">Add Daily Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('save-tasks') }}">
                    @csrf
                    <input type="hidden" value="addTaskModel" name="model_name" />
                    <div class="form-group">
                        <label for="tsk_pro_unique_id">Select Project <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select class="form-control" name="tsk_pro_unique_id" required>
                                <option value="">Select Project</option>
                                @foreach($projects as $project)
                                    @if(old('tsk_pro_unique_id') == $project->pro_unique_id)
                                        <option value="{{ $project->pro_unique_id }}" selected>{{ $project->pro_name }}</option>
                                    @else
                                        <option value="{{ $project->pro_unique_id }}">{{ $project->pro_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('tsk_pro_unique_id')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tsk_description">Description <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <textarea id="tsk_description" type="text" class="form-control @error('tsk_description') is-invalid @enderror" name="tsk_description" style="height: 150px;">{{ old('tsk_description') }}</textarea>
                            @error('tsk_description')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                     <input type="hidden" value="{{ date('d-m-Y') }}" name="tsk_date" class="form-control" required readonly />
                    </div>
                    <div class="form-group">
                        <label for="tsk_start_time">Start Time <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-clock"></i>
                              </div>
                            </div>
                            <input type="time" name="tsk_start_time" value="{{ old('tsk_start_time', date('H:i')) }}" class="form-control @error('tsk_start_time') is-invalid @enderror"  required />
                            @error('tsk_start_time')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tsk_end_time">End Time <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                  <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <input type="time" value="{{ old('tsk_end_time', date('H:i')) }}" name="tsk_end_time" class="form-control @error('tsk_end_time') is-invalid @enderror" required />
                            @error('tsk_end_time')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary m-t-15 waves-effect float-right">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- end add task model --}}

{{-- start create notes model --}}
<div class="modal fade" id="addNotesModel" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true" data-backdrop="static" >
	<div class="modal-dialog modal-lg" style="width: 44%;" role="document">
		 <div class="modal-content">
			  <div class="modal-header">
					<h5 class="modal-title" id="formModal">Add Notes</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						 <span aria-hidden="true">&times;</span>
					</button>
			  </div>
			  <div class="modal-body">
					<form class="form-horizontal" role="form" method="POST" action="{{ url('add-sticky-notes') }}">
						 @csrf
						 <input type="hidden" value="addNotesModel" name="model_name" />
						 <div class="form-group">
							  <label for="stk_title">Notes Title <span class="text-danger">*</span></label>
							  <div class="input-group">
								<input type="text" name="stk_title" id="stk_title" value="{{ old('stk_title') }}" class="form-control" required autofocus placeholder="Enter Notes">
								@error('stk_title')
									 <div class="invalid-feedback"> {{ $message }} </div>
								@enderror
							  </div>
						 </div>
						 <div class="form-group">
							  <label for="stk_content">Content <span class="text-danger">*</span></label>
							  <div class="input-group">
								<textarea class="form-control" name="stk_content" rows="5" style="height: 100px;" required placeholder="Write here content...">{{ old('stk_content') }}</textarea>
                                @error('stk_content')
									 <div class="invalid-feedback"> {{ $message }} </div>
								@enderror
							  </div>
						 </div>
                         <p><span class="text-danger">*</span> Represent that it's compulsory fields</p>
						 <button type="submit" class="btn btn-primary waves-effect float-right" style="margin-top: -40px;">Submit</button>
					</form>
			  </div>
		 </div>
	</div>
</div>
{{-- end create notes model --}}

{{-- start Add Staff Timer --}}
<div class="modal fade" id="addStaffTimerModel" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="formModal">Add Staff Timer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" role="form" method="POST" action="{{ url('save-staff-daily-timer') }}">
                    @csrf
                    <input type="hidden" value="addStaffTimerModel" name="model_name" />
                    <div class="form-group">
                        <label for="user_id">Select Staff <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select class="form-control @error('reason') is-invalid @enderror" name="user_id" required>
                                <option value="">Select Staff</option>
                                @foreach($staffList as $staff)
                                    @if(old('user_id') == $staff['id'])
                                        <option value="{{ $staff['id'] }}" selected>{{ $staff['name'] }}</option>
                                    @else
                                        <option value="{{ $staff['id'] }}">{{ $staff['name'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="reason">Reason</label>
                        <div class="input-group">
                            <textarea id="reason" type="text" class="form-control @error('reason') is-invalid @enderror" name="reason" style="height: 100px;">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="date">Date <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" value="{{ date('d-m-Y') }}" name="date" class="form-control @error('reason') is-invalid @enderror datepicker-enddate" required/>
                            @error('date')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="start_time">Start Time <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                              <div class="input-group-text">
                                <i class="fas fa-clock"></i>
                              </div>
                            </div>
                            <input type="time" name="start_time" value="{{ old('start_time', date('H:i')) }}" class="form-control @error('start_time') is-invalid @enderror"  required />
                            @error('start_time')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="end_time">End Time <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                  <i class="fas fa-clock"></i>
                                </div>
                            </div>
                            <input type="time" value="{{ old('end_time', date('H:i')) }}" name="end_time" class="form-control @error('end_time') is-invalid @enderror" required />
                            @error('end_time')
                                <div class="invalid-feedback"> {{ $message }} </div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary m-t-15 waves-effect float-right">Submit</button>
                </form>
            </div>
        </div>
    </div>
</div>
{{-- end Add Staff Timer --}}

{{-- start update Daily Timer model --}}
<div class="modal fade" id="updateDailyTimer" tabindex="-1" role="dialog" aria-labelledby="formModal" aria-hidden="true">
	<div class="modal-dialog" role="document">
		 <div class="modal-content">
			  <div class="modal-header">
					<h5 class="modal-title" id="formModal">Update Timer <small>(Yesterday you forget timer stop.)</small></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						 <span aria-hidden="true">&times;</span>
					</button>
			  </div>
			  <div class="modal-body">
					<form class="form-horizontal" role="form" method="POST" action="{{ url('update-staff-timer') }}">
						 @csrf
						 @if($forgetTimerRecord != null)
						 <input type="hidden" value="updateDailyTimer" name="model_name" />
						 <input type="hidden" value="{{ $forgetTimerRecord->id }}" name="timer_id" />
						 <div class="form-group">
							 <label for="reason">Reason <span class="text-danger">*</span></label>
							 <div class="input-group">
									 <textarea id="reason" type="text" class="form-control @error('reason') is-invalid @enderror" name="reason" style="height: 100px;" required>{{ old('reason') }}</textarea>
									 @error('reason')
										 <div class="invalid-feedback"> {{ $message }} </div>
									 @enderror
							 </div>
						 </div>
						 <div class="form-group">
							 <label for="timer_date">Date <span class="text-danger">*</span></label>
							 <div class="input-group">
									 <input type="text" value="{{ date('d-m-Y',strtotime($forgetTimerRecord->date)) }}" name="timer_date" class="form-control @error('reason') is-invalid @enderror" required readonly/>
									 @error('date')
										 <div class="invalid-feedback"> {{ $message }} </div>
									 @enderror
							 </div>
						 </div>
						 <div class="form-group">
							 <label for="start_time">Start Time <span class="text-danger">*</span></label>
							 <div class="input-group">
									 <div class="input-group-prepend">
									 <div class="input-group-text">
										 <i class="fas fa-clock"></i>
									 </div>
									 </div>
									 <input type="time" name="start_time" value="{{ old('start_time', date('H:i')) }}" class="form-control @error('start_time') is-invalid @enderror" required readonly/>
									 @error('start_time')
										 <div class="invalid-feedback"> {{ $message }} </div>
									 @enderror
							 </div>
						 </div>
						 <div class="form-group">
							 <label for="end_time">End Time <span class="text-danger">*</span></label>
							 <div class="input-group">
								 <div class="input-group-prepend">
									 <div class="input-group-text">
										 <i class="fas fa-clock"></i>
									 </div>
								 </div>
								 <input type="time" value="{{ old('end_time') }}" name="end_time" class="form-control @error('end_time') is-invalid @enderror" required />
								 @error('end_time')
									 <div class="invalid-feedback"> {{ $message }} </div>
								 @enderror
							 </div>
						 </div>
                         <p style="color: red;">Note: Add reason and also enter end time.</p>
						 <button type="submit" class="btn btn-primary m-t-15 waves-effect float-right">Submit</button>
						 @endif
					</form>
			  </div>
		 </div>
	</div>
</div>
{{-- start update Daily Timer model --}}

@endsection

@section('script')

<script>
    // firebase notification allow box
    function notifyMe() {
        if (!("Notification" in window)) {
            console.log("This browser does not support desktop notification");
        } else if (Notification.permission === "granted") {}
        else if (Notification.permission !== "denied") {
            $('#notificationPermissionModal').modal({show: true,backdrop: false});
        }
    }
    document.addEventListener("DOMContentLoaded", function(event) {
        setTimeout(function() {
            notifyMe();
        }, 5000); //wait 5 seconds
    });
</script>

<!-- JS Libraies -->
<script src="{{ asset('public/assets/bundles/summernote/summernote-bs4.js') }}"></script>
<script type="text/javascript">
    var today = new Date();
    $('#datepicker').datepicker({
        format: 'dd-mm-yyyy',
        endDate: '0d',
        autoclose: true,
        todayBtn: true,
    });
    $('#datepicker2').datepicker({
        format: 'dd-mm-yyyy',
        endDate: '0d',
        autoclose: true,
        todayBtn: true,
    });
    $('#datepicker3').datepicker({
        format: 'dd-mm-yyyy',
        endDate: '0d',
        autoclose: true,
        todayBtn: true,
    });

    $('#datepicker4').datepicker({
        format: 'dd-mm-yyyy',
        endDate: '0d',
        autoclose: true,
        todayBtn: true,
    });

     $('#datepicker5').datepicker({
        format: 'dd-mm-yyyy',
        endDate: '0d',
        autoclose: true,
        todayBtn: true,
    });
      $('#tsk_date').datepicker({
        format: 'dd-mm-yyyy',
        endDate: '0d',
        autoclose: true,
        todayBtn: true,
    });
</script>
    <script>
        function searchit() {
            $.post("{{url('/user/search')}}", {
                date: $("#search").val(),
                '_token': '{{csrf_token()}}'
            }, function (data, status) {
                $("#showStaffReport").hide();
                $("#showStaffTime").show();
                $("#total_time").html("Total Time:" + data.total_time[0].total_time);
                $("#ajax-table").html(data.timesheets);
            });
        }

        function updateTimeLog() {
            $.post("{{url('/user/update-status')}}", {
                id: '{{$user[0]->id}}',
                '_token': '{{csrf_token()}}'
            }, function (data, status) {
                $("#showStaffReport").hide();
                $("#showStaffTime").show();
                $("#ajax-table").html(data.timesheets);
                $("#totaltime").html(data.total_time[0].total_time);
            });
        }
        $(document).ready(function () {
            $("#showStaffTime").hide();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                }
            });
        });

        </script>
        <script>
            var h1 = document.getElementById('timer'),
                start = document.getElementById('start'),
                stop = document.getElementById('stop'),
                t;
                var today_total_time = '{{$today_total_time}}';
                totaltime.textContent = today_total_time;

                @if(isset($time_diff) && $time_diff!=null)
                    <?php $time = explode(':', $time_diff) ?>
                    var seconds = {{$time[2]}}, minutes = {{$time[1]}}, hours = {{$time[0]}};
                    // h1.textContent = hours + ':' + minutes + ':' + seconds;
                    h1.textContent = (hours ? (hours > 9 ? hours : "0" + hours) : "00") + ":" + (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);

                @elseif(isset($total_time) && $total_time!=null )
                    <?php $time = explode(':', $total_time) ?>
                    var seconds = {{$time[2]}}, minutes = {{$time[1]}}, hours = {{$time[0]}};
                    // h1.textContent = hours + ':' + minutes + ':' + seconds;
                    h1.textContent = (hours ? (hours > 9 ? hours : "0" + hours) : "00") + ":" + (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);
                @else
                    var seconds = 0, minutes = 0, hours = 0;
                @endif

            function add() {
                seconds++;
                if (seconds >= 60) {
                    seconds = 0;
                    minutes++;
                    if (minutes >= 60) {
                        minutes = 0;
                        hours++;
                    }
                }
                h1.textContent = (hours ? (hours > 9 ? hours : "0" + hours) : "00") + ":" + (minutes ? (minutes > 9 ? minutes : "0" + minutes) : "00") + ":" + (seconds > 9 ? seconds : "0" + seconds);
                timer();
            }
            function timer() {
                t = setTimeout(add, 1000);
            }

            @if(isset($time_diff))
                timer();
                start.style = 'display:none';
                stop.style = 'display:inline;';
            @endif

            @if(isset($timer))
                start.style = 'display:none';
                stop.style = 'display:inline;';
            @endif

            /* Start button */
            start.onclick = function () {
                stop.classList.add('btn-progress');
                setTimeout(function(){
                    stop.classList.remove('btn-progress');
                }, 1*1000);

                timer();
                start.style = 'display:none';
                stop.style = 'display:inline;';
                updateTimeLog();
            }

            /* Stop button */
            stop.onclick = function () {
                start.classList.add('btn-progress');
                setTimeout(function(){
                    start.classList.remove('btn-progress');
                }, 1*1000);

                updateTimeLog();
                start.style = 'display:inline;';
                stop.style = 'display:none';
                clearTimeout(t);
            }

        </script>

        <script type="text/javascript">
          $(document).ready( function() {
            $('.flash-message').delay(4000).fadeOut();
          });
        </script>
        <script type="text/javascript">
            $(document).ready(function() {
                $('#datepicker').on('change', function() {
                    document.forms['report-filter-page'].submit();
                });
                $('#datepicker2').on('change', function() {
                    document.forms['report-less-time'].submit();
                });
                $('#datepicker3').on('change', function() {
                    document.forms['report-less-time'].submit();
                });
                    $('#userId').on('change', function() {
                    document.forms['report-less-time'].submit();
                });

                $('#updateTimerId').on('change', function() {
                    document.forms['update-timer-report'].submit();
                });

                $('#datepicker4').on('change', function() {
                    document.forms['update-timer-report'].submit();
                });
                // Today Staff Activity
                $('#tsk_date').on('change', function() {
                    document.forms['report-filter-page'].submit();
                });
            });
        </script>

        <script>
           setTimeout(function(){
               location.reload();
           },3600000);
            // 18000000 milliseconds means 5 hours. 3600000 milliseconds means 1 hours.
        </script>

        <script type="text/javascript">
            $(document).ready(function() {
                var formname = '#{{ old('model_name') }}';
                if(formname)
                {
                    $(formname).modal('show');
                }
            });
        </script>

        <script>
            var pageNumber = 2;
            var i = 6;
            function seeMoreStickyNotes(selectObj) {
                $(selectObj).addClass('btn-progress');
                $.ajax({
                    type : 'GET',
                    url: "{{ url('see-more-sticky-notes') }}?page=" +pageNumber,
                    success : function(data){
                        pageNumber++;
                        $(selectObj).removeClass('btn-progress');

                        if(data.length == 0){
                            // :( no more articles
                        }else{
                            $(data.stickyNotes.data).each(function(key,value){
                                i++;
                                $('#StickyNotesBody').css({'height':'300px', 'overflow':'auto'});
                                $('#accordion').append('<div class="accordion"><div class="accordion-header" role="button" data-toggle="collapse" data-target="#panel-body-'+i+'" ><h4>'+value.stk_title+'</h4></div><div class="accordion-body collapse" id="panel-body-'+i+'" data-parent="#accordion"><p class="mb-0"> '+value.stk_content+' <a href="{{ url("delete-sticky-notes",'+value.stk_unique_id+') }}" class="text-danger float-right "><i class="far fa-trash-alt font-18"></i></a> <a data-toggle="modal" data-href="#editNotesModel-'+i+'" href="#editNotesModel-'+i+'" class="text-warning float-right ml-2 mr-1"><i class="fas fa-edit font-18"></i></a></p></div></div>');
                            });
                        }

                    },error: function(data){
                        console.error(data);
                    },
                })
            }
        </script>

@endsection
