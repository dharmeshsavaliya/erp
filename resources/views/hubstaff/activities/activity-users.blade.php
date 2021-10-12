@extends('layouts.app')


@section('title', $title)

@section('content')
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>

<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb mb-3">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
            <a class="btn btn-secondary" data-toggle="modal" data-target="#fetch-activity-modal" style="color:white;">Fetch Activity</a>
            <a class="btn btn-secondary" data-toggle="modal" data-target="#open-timing-modal" style="color:white;">Add manual timings</a>
            <a class="btn btn-secondary" href="{{ route('hubstaff-acitivties.pending-payments') }}">Approved timings</a>
            <a class="btn btn-secondary hubstaff_activity_command" data-toggle="modal" data-target="#hubstaff_activity_modal">Hubstuff Activity Command</a>
    </div>
    <div class="col-lg-12 margin-tb">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <form class="form-check-inline" action="{{route('hubstaff-acitivties.activities')}}" method="get">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <?php echo Form::select("user_id",["" => "-- Select User --"]+$users,$user_id,["class" => "form-control select2"]); ?>
                        </div>
                        <div class="form-group col-md-2">
                            <?php echo Form::text("developer_task_id",request('developer_task_id'),["class" => "form-control","placeholder" => "Developer Task ID"]); ?>
                        </div>
                        <div class="form-group col-md-1">
                            
                            <?php echo Form::text("task_id",request('task_id'),["class" => "form-control","placeholder" => "Task ID"]); ?>
                        </div>
                        <div class="form-group col-md-1">
                            <select name="task_status" class="form-control">
                                <option value="" >Select Status</option>
                                <option value="Done" {{ request('task_status') ==  'Done' ? 'selected' : ''}}>Done</option>
                                <option value="Discussing" {{ request('task_status') == 'Discussing' ? 'selected' : ''}}>Discussing</option>
                                <option value="In Progress"  {{ request('task_status') ==  'In Progress' ? 'selected' : ''}}>In Progress</option>
                                <option value="Issue" {{ request('task_status') ==  'Issue' ? 'selected' : ''}}>Issue</option>
                                <option value="Planned" {{ request('task_status') ==  'Planned' ? 'selected' : ''}}>Planned</option>
                                <option value="Discuss with Lead" {{ request('task_status') ==  'Discuss with Lead' ? 'selected' : ''}}>Discuss with Lead</option>
                                <option value="Note" {{ request('task_status') ==  'Note' ? 'selected' : ''}}>Note</option>
                                <option value="Lead Response Needed" {{ request('task_status') == 'Lead Response Needed' ? 'selected' : ''}}>Lead Response Needed</option>
                                <option value="Errors in Task" {{ request('task_status') == 'Errors in Task' ? 'selected' : ''}}>Errors in Task</option>
                                <option value="In Review" {{ request('task_status') == 'In Review' ? 'selected' : ''}}>In Review</option>
                            </select>
                        </div>
                        <div class="form-group col-md-3">
                            <input type="text" value="{{$start_date}}" name="start_date" hidden/>
                            <input type="text" value="{{$end_date}}" name="end_date" hidden/>
                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                       
                        <div class="form-group col-md-1">
                            <select name="status" id="" class="form-control">
                            <option value="">Select</option>
                            <option value="new" {{$status == 'new' ? 'selected' : ''}}>New</option>
                            <option value="forwarded_to_admin" {{$status == 'forwarded_to_admin' ? 'selected' : ''}}>Forwarded to admin</option>
                            <option value="forwarded_to_lead" {{$status == 'forwarded_to_lead' ? 'selected' : ''}}>Forwarded to team lead</option>
                            <option value="approved" {{$status == 'approved' ? 'selected' : ''}}>Approved by admin</option>
                            <option value="pending" {{$status == 'pending' ? 'selected' : ''}}>Pending by admin</option>
                            </select>
                        </div>
                        <div class="form-group col-md-2">
                            <button type="submit" name="submit" class="btn btn-xs text-dark">
                                <i class="fa fa-search"></i>
                            </button>
                            <button type="submit" name="submit" value="report_download" title="Download report" class="btn btn-xs btn-secondary">
                                Download report
                            </button>
                        </div>  
                    </div>    
                </form> 
            </div>
            
            <div class="col-md-12 margin-tb">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                        <th width="1%">Date</th>
                            <th width="1%">User</th>
                            <th width="2%">Tm Trk</th>
                            <th width="7%">Tasks</th>
                            <th width="3%">Tm Trk</th>
                            <th width="3%">Tm Est</th>
                            <th width="3%" title="Time Diffrent">Tm Diff</th>
                            <th width="1%">Sts</th>
                            <th width="3%">Tm App</th>
                            <th width="3%">Tm App</th>
                            <th width="3%">Tm Pen</th>
                            <th width="4%">Usr Req</th>
                            <th width="5%">Pen Pmt Tm</th>
                            <th width="1%">Sts</th>
                            <th width="1%">Note</th>
                            <th width="6%">Frequency</th>
                            <th width="5%">Action</th>
                        </tr>
                        @php
                            $totalTracked = 0;
                            $totalApproved = 0;
                            $totalPending = 0;
                            $totalUserRequested = 0;
                            $totalPaymentPending = 0;
                        @endphp  
                        @foreach ($activityUsers as $index => $user)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($user['date'])->format('d-m') }} </td>
                            <td class="expand-row-msg" data-name="userName" data-id="{{$index}}">
                                <span class="show-short-userName-{{$index}}">{{ str_limit($user['userName'], 5, '..')}}</span>
                                <span style="word-break:break-all;" class="show-full-userName-{{$index}} hidden">{{$user['userName']}}</span>
                            </td>
                            @php
                                $totalTracked +=  $user['total_tracked'];
                                $totalApproved +=  $user['totalApproved'];
                                $totalPending +=  $user['totalPending'];
                                $totalUserRequested +=  $user['totalUserRequest'];
                                $totalPaymentPending +=  $user['totalNotPaid'];
                            @endphp
                            <td>{{number_format($user['total_tracked'] / 60,2,".",",")}}</td>
                            <td colspan="6">
                                <table class="w-100 table-hover">
                                <?php if(!empty($user['tasks'])) { ?>
                                    <?php foreach($user['tasks'] as $ut) { ?>
                                        <?php 
                                        @list($taskid,$devtask,$taskName,$estimation,$status,$devTaskId) = explode("||",$ut);
                                            $trackedTime = \App\Hubstaff\HubstaffActivity::where('task_id', $taskid)->sum('tracked');
                                            $time_history = \App\DeveloperTaskHistory::where('developer_task_id',$devTaskId)->where('attribute','estimation_minute')->where('is_approved',1)->first();
                                            if($time_history) {
                                                $est_time = $time_history->new_value;
                                            }
                                            else {
                                                $est_time = 0;
                                            }
                                        ?>
                                        @if ( $taskid )
                                        <tr>
                                            <td width="26%" class="expand-row-msg" data-name="devtask" data-id="{{$taskid}}">
                                                <?php if(Auth::user()->isAdmin()) { ?> 
                                                <a class="show-task-histories " style="color:#333333;" data-user-id="{{$user['user_id']}}" data-task-id="{{$taskid}}" href="javascript:;">
                                                    <span class="show-short-devtask-{{$taskid}}">{{ str_limit($devtask, 12, '..')}}</span>
                                                    <span style="word-break:break-all;" class="show-full-devtask-{{$taskid}} hidden">{{$devtask}}</span>
                                                </a>
                                                <?php }else{ ?>
                                                <a class="" data-user-id="{{$user['user_id']}}" style="color:#333333;" data-task-id="{{$taskid}}" href="javascript:;">
                                                    <span class="show-short-devtask-{{$taskid}}">{{ str_limit($devtask, 12, '..')}}</span>
                                                    <span style="word-break:break-all;" class="show-full-devtask-{{$taskid}} hidden">{{$devtask}}</span>
                                                </a>
                                                <?php } ?>
                                            </td>
                                            <td width="10%">
                                                @if ($taskName)
                                                    {{ (isset($trackedTime) && $devtask ) ? number_format($trackedTime / 60,2,".",",") : 'N/A' }}
                                                @endif
                                            </td>
                                            <td width="11%" class="p-0 pt-1 pl-2">
                                                @if ($taskName)
                                                    {{ $estimation }}
                                                @endif
                                                <button type="button" class="btn btn-xs show-time-history" title="Show History" data-id="{{$devTaskId}}"><i class="fa fa-info-circle"></i></button>
                                            </td>
                                            <td width="11%" class="p-0 pt-1 pl-2">
                                                @if ( $taskName )
                                                    @if (is_numeric($estimation) && $trackedTime && $taskName)
                                                        {{ $estimation . '-' . number_format($trackedTime / 60,2,".",",") }}
                                                    @else
                                                        N/A
                                                    @endif
                                                    <button type="button" class="btn btn-xs task-notes" title="Show notes" data-task="{{$devTaskId}}"><i class="fa fa-info-circle"></i></button>
                                                @endif
                                            </td>
                                            <td width="8%">
                                                @if ( $taskName )
                                                    {{ $status ? $status : 'N/A' }}
                                                @endif
                                            </td>
                                            <td width="11%">
                                                {{ $est_time }}
                                            </td>
                                        </tr>
                                        @endif
                                        <?php } ?>
                                    <?php } ?>
                                </table>
                            </td>
                            <td><span class="replaceme">{{number_format($user['totalApproved'] / 60,2,".",",")}}</span> </td>
                            <td>{{ number_format($user['totalPending'] / 60,2,".",",") }}</td>
                            <td><span>{{number_format($user['totalUserRequest'] / 60,2,".",",")}}</span> </td>
                            <td><span>{{number_format($user['totalNotPaid'] / 60,2,".",",")}}</td>
                            <td>{{$user['status']}}</td>
                            <td class="expand-row-msg" data-name="note" data-id="{{$index}}">
                                <span class="show-short-note-{{$index}}">{{ str_limit($user['note'], 12, '...')}}</span>
		                        <span style="word-break:break-all;" class="show-full-note-{{$index}} hidden">{{$user['note']}}</span>
                            </td>
                            @php
                            $fixed_price_user_or_job = '';    
                            if($user['fixed_price_user_or_job'] == 1)
                                $fixed_price_user_or_job = 'Fixed Price Job';
                            elseif($user['fixed_price_user_or_job'] == 2)
                                $fixed_price_user_or_job = 'Hourly Per Task';
                            elseif($user['fixed_price_user_or_job'] == 3)
                                $fixed_price_user_or_job = 'Salaried';                  
                            @endphp
                            <td>
                                <p>S/F PX : {{$fixed_price_user_or_job}}</p>
                                <p>Frequency : {{$user['payment_frequency']}} </p>
                                <!-- <p>Last Voucher Date : {{ \Carbon\Carbon::parse($user['last_mail_sent_payment'])->format('d-m-y') }}</p> -->
                            </td>
                            <td>
                                @if($user['forworded_to'] == Auth::user()->id && !$user['final_approval'])
                                <form action="">
                                    <input type="hidden" class="user_id" name="user_id" value="{{$user['user_id']}}">
                                    <input type="hidden" class="date" name="date" value="{{$user['date']}}">
                                    <a class="btn btn-xs text-dark show-activities">+</a>
                                </form>
                                @endif
                                @if(Auth::user()->isAdmin())
                                <form action="">
                                    <input type="hidden" class="user_id" name="user_id" value="{{$user['user_id']}}">
                                    <input type="hidden" class="date" name="date" value="{{$user['date']}}">
                                    <a class="btn btn-xs text-dark show-activities"><i class="fa fa-check" aria-hidden="true"></i></a>
                                    <a class="btn btn-xs text-dark approve-activities" title="Approve time"><i class="fa fa-check-circle" aria-hidden="true"></i></a>                                    
                                </form>
                                <button type="button" class="btn btn-xs text-dark hubstaff-activity-report-download" title="Activity Report" data-toggle="modal" data-system_user_id="{{ $user['system_user_id'] }}" data-target="#hubstaffActivityReportModel"><i class="fa fa-address-card" aria-hidden="true"></i></button>
                                @endif
                                <i class="fa fa-list list_history_payment_data"  data-user-id="{{$user['user_id_data']}}" aria-hidden="true"></i>
                            </td>
                        </tr>
                        @endforeach
                        <tr>
                            <th>Total</th>
                            <th></th>
                            <th>{{number_format($totalTracked / 60,2,".","")}}</th>
                            <th></th>
                            <th></th>
                            <th></th>   
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>{{number_format($totalApproved / 60,2,".","")}}</th>
                            <th>{{number_format($totalPending / 60,2,".","")}}</th>
                            <th>{{number_format($totalUserRequested / 60,2,".","")}}</th>
                            <th>{{number_format($totalPaymentPending / 60,2,".","")}}</th>
                            <th></th>
                            <th></th>
                            <th width="10%" colspan="2" class="text-center"></th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div id="records-modal" class="modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="max-width: 1200px !important; width: 100% !important;">
      <div class="modal-content" id="record-content">

      </div>
    </div>  
</div>


<!-- Modal -->
<div class="modal fade" id="hubstaff_activity_modal" tabindex="-1" role="dialog"  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="">Hubstuff Activity Command</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">

            <div class="col-md-12 mb-5 hubstaff_payment_user">
                <select class="form-control" id="select2insidemodal" name="user">
                    <option value="">Select</option>                           
                    @foreach(App\User::orderBy('name')->get() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-12">
                <input type="hidden" class="range_start_filter" value="<?php echo date("Y-m-d"); ?>" name="range_start" />
                <input type="hidden" class="range_end_filter" value="<?php echo date("Y-m-d"); ?>" name="range_end" />
                <div id="filter_date_range_" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ddd; width: 100%;border-radius:4px;">
                    <i class="fa fa-calendar"></i>&nbsp;
                    <span  id="date_current_show"></span><i class="fa fa-caret-down"></i>
                </div>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary execute_hubstaff_payment_command">Excecute</button>
      </div>
    </div>
  </div>
</div>

<div id="open-timing-modal" class="modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form>
            @csrf
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            <div class="form-group">
                <label for="">Date</label>
                <input type="text" name="starts_at" value="" class="form-control" id="custom_hour" required placeholder="Enter Date">
            </div>
            <div class="form-group">
                <label for="">Total time (In minutes)</label>
                <input type="number" name="total_time" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="">Task Id</label>
                <input type="number" name="task_id" class="form-control" placeholder="Enter task id, eg. 2997">
            </div>
            <div class="form-group">
                <label for=""> Notes </label>
                <textarea name="user_notes" class="form-control" style="resize:none" required placeholder="Add Your Comments Here"></textarea>
            </div>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-danger submit-manual-record">Submit</button> 
            </div>
        </form>
      </div>
    </div>  
</div>
<div id="fetch-activity-modal" class="modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form>
            @csrf
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="">Activity available up to</label>
                    <input id="activity-available" type="text"  value="" class="form-control" readonly>
                </div>
                <div class="form-group">
                    <input type="text" name="hub_staff_start_date" value="" hidden/>
                    <input type="text" name="hub_staff_end_date" value="" hidden/>
                    <div id="HubStaffDateRange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                    </div>
                </div>
                @if(auth()->user()->isAdmin())
                    <div class="form-group">
                        <label for="fetch_user_id">Fetch for</label>
                        {{ Form::select("fetch_user_id",\App\User::pluck('name','id')->toArray(),request('fetch_user_id'),["class" => "form-control select2","style" => "width:100%"]) }}
                    </div>
                @endif
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary submit-fetch-activity">Submit</button> 
            </div>
        </form>
      </div>
    </div>  
</div>

<div id="permission-request" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Notes list</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="col-md-12" id="permission-request">
                    <table class="table fixed_header">
                        <thead>
                            <tr>
                                <th>Notes</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                         <tbody class="show-list-records" >
                         </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal" id="hubstaffActivityReportModel">
    <div class="modal-dialog">
      <div class="modal-content">
  
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Hubstaff Activity Report</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
  
        <!-- Modal body -->
        <div class="modal-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Excel File</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody class=" hubstaff-activity-table"></tbody>
            </table>
        </div>
  
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
  
      </div>
    </div>
  </div>



  <div class="modal" id="hubstaffPaymentReportModel">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
  
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Hubstaff Payment Report</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
  
        <!-- Modal body -->
        <div class="modal-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Total Amount</th>
                        <th>Commane Execution</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody class="hubstaff-payment-table"></tbody>
            </table>
        </div>
  
        <!-- Modal footer -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
  
      </div>
    </div>
  </div>

@include("development.partials.time-history-modal")

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>

<script type="text/javascript">


$(document).on('click', '.approve-activities', function(e) {
    e.preventDefault();

    if(!confirm('Are you sure want to approve time?')){
        return false;
    }
    
    var form = $(this).closest("form");
    var thiss = $(this);
    var type = 'GET';
    $.ajax({
    url: '/hubstaff-activities/activities/approve-all-time?'+form.serialize(),
    type: type,
    beforeSend: function() {
        $("#loading-image").show();
    }
    }).done( function(response) {
        $("#loading-image").hide();
        
    }).fail(function(errObj) {
        $("#loading-image").hide();
        toastr['error'](errObj.responseJSON.message, 'error');
    });
});

$(document).on("click",".task-notes",function(e) {
    e.preventDefault();
    var id = $(this).data('task');
    $.ajax({
        url: '/hubstaff-activities/activities/task-notes',
        type: 'POST',
        data : { _token: "{{ csrf_token() }}", id:id},
        dataType: 'json',
        beforeSend: function () {
            $("#loading-image").show();
        },
        success: function(result){
            $("#loading-image").hide();
            if(result.code == 200) {
                var t = '';
                $.each(result.data,function(k,v) {
                    t += `<tr><td>`+v.notes+`</td>`;
                    t += `<td>`+v.date+`</td></tr>`;
                });
                if( t == '' ){
                    t = '<tr><td colspan="2" class="text-center">No data found</td></tr>';
                }
            }
            $("#permission-request").find(".show-list-records").html(t);
            $("#permission-request").modal("show");
        },
        error: function (){
            $("#loading-image").hide();
        }
    });
});

$(document).on('click', '.show-time-history', function() {
    var data = $(this).data('history');
    var issueId = $(this).data('id');
    $('#time_history_div table tbody').html('');
    $.ajax({
        url: "{{ route('development/time/history') }}",
        data: {id: issueId},
        success: function (data) {
            if(data != 'error') {
                $('input[name="developer_task_id"]').val(issueId);
                $.each(data, function(i, item) {
                    if(item['is_approved'] == 1) {
                        var checked = 'checked';
                    }
                    else {
                        var checked = ''; 
                    }
                    $('#time_history_div table tbody').append(
                        '<tr>\
                            <td>'+ moment(item['created_at']).format('DD/MM/YYYY') +'</td>\
                            <td>'+ ((item['old_value'] != null) ? item['old_value'] : '-') +'</td>\
                            <td>'+item['new_value']+'</td>\<td>'+item['name']+'</td><td><input type="radio" name="approve_time" value="'+item['id']+'" '+checked+' class="approve_time"/></td>\
                        </tr>'
                    );
                });
            }
        }
    });
    $('#time_history_modal').modal('show');
});

$(document).on('click', '.expand-row-msg', function () {
    var name = $(this).data('name');
    var id = $(this).data('id');
    var full = '.expand-row-msg .show-short-'+name+'-'+id;
    var mini ='.expand-row-msg .show-full-'+name+'-'+id;
    $(full).toggleClass('hidden');
    $(mini).toggleClass('hidden');
});

$(document).on('click', '.expand-row', function () {
    var selection = window.getSelection();
    if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
    }
});

$(document).on('change', '.task_efficiency', function(e) 
{
    var user_id = $(this).data('user_id');
    var efficiency = $(this).val();
    var type = $(this).data('type');
    var date = $(this).data('date');
    var hour = $(this).data('hour');

    var $action_url = '{{ route("hubstaff-acitivties.efficiency.save") }}';                 
        jQuery.ajax({
                
            type: "POST",
            url: $action_url,
            data: { user_id: user_id,efficiency: efficiency,type: type, date: date, hour: hour  },
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            //cache: false,
            //dataType: 'json',
            success: function(data)
            {
                toastr['success'](data.message);
                
            },
            error: function(error)
            {
                toastr['error'](data.message);
            },
                
        });
        return false;

});

$("#activity-available").val(new Date().toUTCString());
$(".select2").select2({tags:true});

$('#starts_at').datetimepicker({
    format: 'YYYY-MM-DD'
});
$('#custom_hour').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss'
});
$('#time_from').datetimepicker({
    format: 'YYYY-MM-DD HH:mm:ss'
});
let r_s = jQuery('input[name="start_date"]').val();
        let r_e = jQuery('input[name="end_date"]').val()

        if(r_s == "0000-00-00 00:00:00") {
           r_s = undefined; 
        }

        if(r_e == "0000-00-00 00:00:00") {
           r_e = undefined; 
        }

        let start = r_s ? moment(r_s, 'YYYY-MM-DD') : moment().subtract(6, 'days');
        let end = r_e ? moment(r_e, 'YYYY-MM-DD') : moment();

        let r_s_p = "";
        let r_e_p = "";

        let start_p = r_s_p ? moment(r_s_p,'YYYY-MM-DD') : moment().subtract(0, 'days');
        let end_p =   r_e_p ? moment(r_e_p,'YYYY-MM-DD') : moment();

        // jQuery('input[name="range_start"]').val(start.format('YYYY-MM-DD'));
        // jQuery('input[name="range_end"]').val(end.format('YYYY-MM-DD'));

        function cb(start, end, id) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#reportrange').daterangepicker({
            startDate: start,
            maxYear: 1,
            endDate: end,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);

        cb(start, end);

        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
            jQuery('input[name="start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
            jQuery('input[name="end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
        });


        function hubDate(start, end, id) {
            $('#HubStaffDateRange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            jQuery('input[name="hub_staff_start_date"]').val(start.format('YYYY-MM-DD'));
            jQuery('input[name="hub_staff_end_date"]').val(end.format('YYYY-MM-DD'));
        }
        
        $('#HubStaffDateRange').daterangepicker({
            maxYear: 1,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        },hubDate);


        $('#HubStaffDateRange').on('apply.daterangepicker', function (ev, picker) {
            jQuery('input[name="hub_staff_start_date"]').val(picker.startDate.format('YYYY-MM-DD'));
            jQuery('input[name="hub_staff_end_date"]').val(picker.endDate.format('YYYY-MM-DD'));
        });

        function hubpaymentdate(start_p, end_p, id){
            $('#filter_date_range_ span').html(start_p.format('MMMM D, YYYY') + ' - ' + end_p.format('MMMM D, YYYY'));
            jQuery('input[name="range_start"]').val(start_p.format('YYYY-MM-DD'));
            jQuery('input[name="range_end"]').val(end_p.format('YYYY-MM-DD'));
        }

        $('#filter_date_range_').daterangepicker({
            maxYear: 1,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, hubpaymentdate);

        $('#filter_date_range_').on('apply.daterangepicker', function (ev, picker) {
            jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
            jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));
        });

    // $(document).on('click', '.show-activitie1s', function(e) {
    //     e.preventDefault();
    //     var form = $(this).closest("form");
    //     console.log();
    //     var thiss = $(this);
    //     var type = 'GET';
    //         $.ajax({
    //         url: '/hubstaff-activities/activities/details',
    //         type: type,
    //         dataType: 'json',
    //         data: form.serialize(),
    //         beforeSend: function() {
    //             // $(thiss).text('Loading');
    //         }
    //         }).done( function(response) {
    //             console.log(response);
    //             $('#records-modal').modal('show');
    //             $('#record-content').html(response);
    //         // $(thiss).closest('tr').removeClass('row-highlight');
    //         // $(thiss).prev('span').text('Approved');
    //         // $(thiss).remove();
    //         }).fail(function(errObj) {
    //         alert("Could not change status");
    //         });
    //     });
    
        var thisRaw = null;
        $(document).on('click', '.show-activities', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        thisRaw = thiss;
        var type = 'GET';
            $.ajax({
            url: '/hubstaff-activities/activities/details?'+form.serialize(),
            type: type,
            beforeSend: function() {
                $("#loading-image").show();
            }
            }).done( function(response) {
            $("#loading-image").hide();
            $('#records-modal').modal('show');
            $('#record-content').html(response);
            }).fail(function(errObj) {
            $("#loading-image").hide();
            toastr['error'](errObj.responseJSON.message, 'error');
            });
        });


        $(document).on('click', '.submit-record', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
            $.ajax({
            url: '/hubstaff-activities/activities/details',
            type: type,
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            }
            }).done( function(response) {
            $("#loading-image").hide();
            thisRaw.closest("tr").find('.replaceme').html(response.totalApproved);
            $('#records-modal').modal('hide');
            thisRaw.closest("tr").find('.show-activities').css("display", "none");
            }).fail(function(errObj) {
                toastr['error'](errObj.responseJSON.message, 'error');
            $("#loading-image").hide();
            });
        });



        $(document).on('click', '.submit-manual-record', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
            $.ajax({
            url: '/hubstaff-activities/activities/manual-record',
            type: type,
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            }
            }).done( function(response) {
            $("#loading-image").hide();
            $('#open-timing-modal').modal('hide');
            toastr['success']('Successful');
            }).fail(function(errObj) {
                toastr['error'](errObj.responseJSON.message, 'error');
            $("#loading-image").hide();
            });
        });

        $(document).on('click', '.selectall', function(e) {
            var cls = '.'+$(this).data("id");
            if ($(this).is(':checked')) {
                $(cls).attr('checked', true);
            } else {
                $(cls).attr('checked', false);
            }
        });

        $(document).on('change', '.select-forword-to', function(e) {
           var person = $(this).data('person');
           $("#hidden-forword-to").val(person);
        });

        $(document).on('click', '.final-submit-record', function(e) {

        e.preventDefault();
        var vali = false;
        $('.notes-input').each(function() {
            if($(this).val() == ''){
                toastr['error']('invalid notes', 'error');
                vali = true; 
            }
        });

        if( vali == true ){
            return false;
        }
        var status = $(this).data('status');
        // return false;
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
        var data = form.serializeArray();
        data.push({name: 'status', value: status});

            $.ajax({
            url: '/hubstaff-activities/activities/final-submit',
            type: type,
            dataType: 'json',
            // data: form.serialize(),
            data: data,
            beforeSend: function() {
                $("#loading-image").show();
            }
            }).done( function(response) {
                $("#loading-image").hide();
                thisRaw.closest("tr").find('.replaceme').html(response.totalApproved);
                $('#records-modal').modal('hide');
                // $(".show-activities").css("display", "none");
                thisRaw.closest("tr").find('.show-activities').css("display", "none");
            }).fail(function(errObj) {
                toastr['error'](errObj.responseJSON.message, 'error');
                $("#loading-image").hide();
            });
        });


        $(document).on('click', '.submit-fetch-activity', function(e) {
        e.preventDefault();
        var form = $(this).closest("form");
        var thiss = $(this);
        var type = 'POST';
            $.ajax({
            url: '/hubstaff-activities/activities/fetch',
            type: type,
            dataType: 'json',
            data: form.serialize(),
            beforeSend: function() {
                $("#loading-image").show();
            }
            }).done( function(response) {
                $("#loading-image").hide();
                // window.location.reload();
                toastr['success'](response.message, 'success');
            }).fail(function(errObj) {
                $("#loading-image").hide();
                if(errObj.responseJSON) {
                    toastr['error'](errObj.responseJSON.message, 'error');
                }
                // window.location.reload();
            });
        });


        $(document).on('click', '.expand-row-btn', function () {
            $(this).closest("tr").find(".expand-col").toggleClass('dis-none');
        });

        $(document).on("click",".show-task-histories",function(e) {
            e.preventDefault();
            var $this = $(this);
            thisRaw = $this;
            $.ajax({
                url: '/hubstaff-activities/activities/task-activity',
                type: 'GET',
                data: {
                    "task_id":$this.data("task-id"),
                    "user_id":$this.data("user-id")
                },
                beforeSend: function() {
                    $("#loading-image").show();
                }
            }).done( function(response) {
                $("#loading-image").hide();
                $("#loading-image").hide();
                $('#records-modal').modal('show');
                $('#record-content').html(response);
            }).fail(function(errObj) {
                $("#loading-image").hide();
                if(errObj.responseJSON) {
                    toastr['error'](errObj.responseJSON.message, 'error');
                }
            });
        });

        $(document).on('click','.hubstaff-activity-report-download',function(){
            var user_id = $(this).data('system_user_id');
            $('#hubstaffActivityReportModel .hubstaff-activity-table').text('');
            $.ajax({
                url: "{{ route('hubstaff-acitivtity.report') }}",
                type: 'GET',
                data: {"user_id":user_id},
                success:function( response ){
                    if (response.status == true) {
                        array = response.data;
                        for (let i = 0; i < array.length; i++) {
                            j = i+1;
                            $html = '<tr><td>Excel File-'+j+'</td><td><button class="btn activity-report-download" data-file="'+array[i].activity_excel_file+'"><i class="fa fa-download" aria-hidden="true"></i></button></td></tr>';
                            $('#hubstaffActivityReportModel .hubstaff-activity-table').append($html);
                        }
                    }
                }
            });
        })

        $(document).on('click','.activity-report-download',function(){
            var file = $(this).data('file');
            var $this = $(this);

            window.location.replace("{{ route('hubstaff-acitivtity-report.download') }}?file=" +  file );

        })

        $(document).on('click','.hubstaff-payment-receipt',function(){
            var $this = $(this);
            var id = $this.data("id");

            $.ajax({
                url: "{{ url('hubstaff-acitivtity.addtocashflow') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    id : id
                },
                cors: true ,
                dataType: 'json',
                beforeSend: function() {
                    $("#loading-image").show();
                }
                }).done( function(response) {
                    $("#loading-image").hide();
                    if(response.code == 200)
                    {
                        toastr['success'](response.message);
                    }
                }).fail(function(errObj) {
                    $("#loading-image").hide();
                });
        })

        


        $(document).on('click','.list_history_payment_data',function(){
            var $this = $(this);
            var user_id = $this.data("user-id");

            $.ajax({
                url: "{{ route('hubstaff-acitivtity.payment_data') }}",
                type: 'GET',
                data: {"user_id":user_id},
                success:function( response ){
                    if (response.status == true) {

                        // console.log("------------>>>");
                        // console.log(response.data);
                        // array = response.data;
                        var html = '';
                        // for (let i = 0; i < array.length; i++) {
                        //     html = '<tr>';
                        //     html = '<td>'+array[i].start_date+'</td>';
                        //     html = '<td>'+array[i].end_date+'</td>';
                        //     html = '<td>'+array[i].total_amount+'</td>';
                        //     html = '<td>'+array[i].file_path+'</td>';
                        //     html = '</tr>';
                        // }

                        $.each( response.data, function( key, value ) {
                             rpost='';
                            if (value.command_execution && value.command_execution=="Manually")
                                 rpost='<a style="cursor:pointer;" data-id="'+value.id+'" class="hubstaff-payment-receipt" aria-hidden="true"> Add To Cashflow </a>';
                            html += '<tr>';
                            html += '<td>'+moment(value.start_date).format('DD-MM-YYYY')+'</td>';
                            html += '<td>'+moment(value.end_date).format('DD-MM-YYYY')+'</td>';
                            html += '<td>'+value.total_amount+'</td>';
                            html += '<td>'+(value.command_execution ?? '-')+rpost+'</td>';
                            html += '<td><a style="cursor:pointer;" data-file="'+value.file_path+'" class="fa fa-download hubstaff-payment-download" aria-hidden="true"></a></td>';
                            html += '</tr>';
                        });

                        // console.log("----html-------->>>");
                        // console.log(html);

                        $('#hubstaffPaymentReportModel .hubstaff-payment-table').html(html);
                        $('#hubstaffPaymentReportModel').modal('show');
                    }
                }
            });
        })

        $(document).on('click','.hubstaff-payment-download',function(){
            var file = $(this).data('file');
            var $this = $(this);

            window.location.replace("{{ route('hubstaff-payment-report.download') }}?file=" +  file );

        })

        $('#hubstaff_activity_modal').on('shown.bs.modal', function (e) {  
            $("#select2insidemodal").select2({
                dropdownParent: $("#hubstaff_activity_modal")
            });
        })

        $(document).on('click','.execute_hubstaff_payment_command',function(){
            var user_id = $("#select2insidemodal").val();
            var startDate=   jQuery('input[name="range_start"]').val();
            var endDate =    jQuery('input[name="range_end"]').val();

            if(user_id == ''){
                toastr['error']('Please Select User');
                return false;
            }

            if(startDate == ''){
                toastr['error']('Please Select Date Range');
                return false;
            }

            $.ajax({
                url: "{{ route('hubstaff-acitivtity.command_execution_manually') }}",
                type: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id : user_id,
                    startDate:startDate,
                    endDate:endDate,
                },
                cors: true ,
                dataType: 'json',
                beforeSend: function() {
                    $("#loading-image").show();
                }
                }).done( function(response) {
                    $("#loading-image").hide();
                    if(response.code == 200)
                    {
                        toastr['success'](response.message);
                    }
                }).fail(function(errObj) {
                    $("#loading-image").hide();
                });
        })

</script>
@endsection