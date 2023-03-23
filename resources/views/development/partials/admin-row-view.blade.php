<tr style="color:grey;">
    <td>
            {{ $issue->id }}
            @if($issue->is_resolved==0)
            <input type="checkbox" name="selected_issue[]" value="{{$issue->id}}" {{in_array($issue->id, $priority) ? 'checked' : ''}}>
            @endif
        <input type="checkbox" title="Select task" class="select_task_checkbox" name="task" data-id="{{ $issue->id }}" value="">
        <!-- <a href="{{ url("development/task-detail/{$issue->id}") }}">{{ $issue->id }}
        </a> -->
        <a href="javascript:;" data-id="{{ $issue->id }}" class="upload-document-btn"><img width="15px" src="/images/attach.png" alt="" style="cursor: default;"><a>
                <a href="javascript:;" data-id="{{ $issue->id }}" class="list-document-btn"><img width="15px" src="/images/archive.png" alt="" style="cursor: default;"><a>

                        <a href="javascript:;" data-toggle="modal" data-target="#developmentReminderModal" class='pd-5 development-set-reminder' data-id="{{ $issue->id }}" data-frequency="{{ !empty($issue->reminder_message) ? $issue->frequency : '60' }}" data-reminder_message="{{ !empty($issue->reminder_message) ? $issue->reminder_message : 'Plz update' }}" data-reminder_from="{{ $issue->reminder_from }}" data-reminder_last_reply="{{ $issue->reminder_last_reply }}">
                            <i class="fa fa-bell @if(!empty($issue->reminder_message) && $issue->frequency > 0) {{ 'green-notification'  }} @else {{ 'red-notification' }} @endif" aria-hidden="true"></i>
                        </a>

                        <br>
                        {{ \Carbon\Carbon::parse($issue->created_at)->format('H:i d-m') }}
                        @if($issue->task_type_id == 1) Devtask @elseif($issue->task_type_id == 3) Issue @endif
    </td>
    <td style="vertical-align: middle;">
        <select name="module" class="form-control task-module" data-id="{{$issue->id}}">
            <option value=''>Select Module..</option>
            @foreach($modules as $module)

            @if( isset($issue->module_id) && (int) $issue->module_id == $module->id )
            <option value="{{$module->id}}" selected>{{$module->name}}</option>
            @else
            <option value="{{$module->id}}">{{$module->name}}</option>
            @endif
            @endforeach
        </select>
    </td>
    <td class="p-2">{{ Carbon\Carbon::parse($module->created_at)->format('d-m H:i') }}</td>
    <td style="vertical-align: middle;word-break: break-all;">
        <p>{{ $issue->subject ?? 'N/A' }}</p>
    </td>
    <td class="expand-row">
        <!-- class="expand-row" -->
        <span class="{{ ($issue->message && $issue->message_status == 0) || $issue->message_is_reminder == 1 || ($issue->sent_to_user_id == Auth::id() && $issue->message_status == 0) ? 'text-danger' : '' }}" style="word-break: break-all;">{{ \Illuminate\Support\Str::limit($issue->message, 150, $end='...') }}</span>
        <textarea class="form-control send-message-textbox addToAutoComplete" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" style="margin-bottom:5px" rows="3" cols="20"></textarea>
        <input class="" name="add_to_autocomplete" class="add_to_autocomplete" type="checkbox" value="true">
        <?php echo Form::select("send_message_" . $issue->id, [
            "to_developer" => "Send To Developer",
            "to_master" => "Send To Master Developer",
            "to_team_lead" => "Send To Team Lead",
            "to_tester" => "Send To Tester"
        ], null, ["class" => "form-control send-message-number", "style" => "width:30% !important;display: inline;"]);
        ?>

        <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message" data-id="{{$issue->id}}"><img src="/images/filled-sent.png" /></button>

        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" style="margin-top:-0%;margin-left: -3%;" title="Load messages"><img src="/images/chat.png" alt=""></button>
        <br>
        <div class="td-full-container hidden">
            <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send Attachment</button>
            <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send Images</button>
            <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple />
        </div>
    </td>
    <td data-id="{{ $issue->id }}">
        <div class="form-group">		
			@if ($issue->status == 'Approved')
				<span>{{ $issue->status }}</span>: {{ $issue->developerTaskHistory ? $issue->developerTaskHistory->new_value : 0 }}
            @elseif ($issue->developerTaskHistory)
                <span style="color:#337ab7"><strong>Unapproved</strong></span>: {{ $issue->developerTaskHistory ? $issue->developerTaskHistory->new_value : 0 }}
            @else
				<span style="color:#337ab7"><strong>Unapproved</strong> </span>
            @endif
        </div>

        @if(auth()->user()->id == $issue->assigned_to)
        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings" data-id="{{$issue->id}}" data-type="developer">Meeting time</button>
        @elseif(auth()->user()->id == $issue->master_user_id)
        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings" data-id="{{$issue->id}}" data-type="lead">Meeting time</button>
        @elseif(auth()->user()->id == $issue->tester_id)
        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings" data-id="{{$issue->id}}" data-type="tester">Meeting time</button>
        @elseif(auth()->user()->isAdmin())
        <button type="button" class="btn btn-xs meeting-timing-popup" title="Add Meeting timings" data-id="{{$issue->id}}" data-type="admin">Meeting time</button>
        @endif

    </td>
    <td data-id="{{ $issue->id }}">
        <div class="form-group">
            <div class='input-group'>
                <span>{{ optional($issue->developerTaskHistory)->new_value ?: "--" }}</span>
            </div>
        </div>
    </td>
    <td>
        @if (isset($issue->timeSpent) && $issue->timeSpent->task_id > 0)
        Developer : {{ formatDuration($issue->timeSpent->tracked) }}

        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history" title="Show tracked time History" data-id="{{$issue->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button>
        @endif

        @if (isset($issue->leadtimeSpent) && $issue->leadtimeSpent->task_id > 0)
        Lead : {{ formatDuration($issue->leadtimeSpent->tracked) }}

        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history" title="Show tracked time History" data-id="{{$issue->id}}" data-type="lead"><i class="fa fa-info-circle"></i></button>
        @endif

        @if (isset($issue->testertimeSpent) && $issue->testertimeSpent->task_id > 0)
        Tester : {{ formatDuration($issue->testertimeSpent->tracked) }}

        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-tracked-history" title="Show tracked time History" data-id="{{$issue->id}}" data-type="tester"><i class="fa fa-info-circle"></i></button>
        @endif


        @if(!$issue->hubstaff_task_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->assigned_to))
        <button type="button" class="btn btn-xs create-hubstaff-task" title="Create Hubstaff task for User" data-id="{{$issue->id}}" data-type="developer">Create D Task</button>
        @endif
        @if(!$issue->lead_hubstaff_task_id && $issue->master_user_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->master_user_id))
        <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task" title="Create Hubstaff task for Master user" data-id="{{$issue->id}}" data-type="lead">Create L Task</button>
        @endif

        @if(!$issue->tester_hubstaff_task_id && $issue->tester_id && (auth()->user()->isAdmin() || auth()->user()->id == $issue->tester_id))
        <button style="margin-top:10px;" type="button" class="btn btn-xs create-hubstaff-task" title="Create Hubstaff task for Tester" data-id="{{$issue->id}}" data-type="tester">Create T Task</button>
        @endif
    </td>
    {{--<td>{{ $issue->submitter ? $issue->submitter->name : 'N/A' }} </td>--}}
    <td>
        <div>
            <select class="form-control assign-user select2" data-id="{{$issue->id}}" name="assigned_to" id="user_{{$issue->id}}">
                <option value="">Select...</option>
                <?php $assignedId = isset($issue->assignedUser->id) ? $issue->assignedUser->id : 0; ?>
                @foreach($users as $id => $name)
                @if( $assignedId == $id )
                <option value="{{$id}}" selected>{{ $name }}</option>
                @else
                <option value="{{$id}}">{{ $name }}</option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="mr-t-5">
            <select class="form-control assign-master-user select2" data-id="{{$issue->id}}" name="master_user_id" id="user_{{$issue->id}}">
                <option value="">Select...</option>
                <?php $masterUser = isset($issue->masterUser->id) ? $issue->masterUser->id : 0; ?>
                @foreach($users as $id=>$name)
                @if( $masterUser == $id )
                <option value="{{$id}}" selected>{{ $name }}</option>
                @else
                <option value="{{$id}}">{{ $name }}</option>
                @endif
                @endforeach
            </select>
        </div>
        <div class="mr-t-5">
            <select class="form-control assign-team-lead select2" data-id="{{$issue->id}}" name="team_lead_id" id="user_{{$issue->id}}">
                <option value="">Select...</option>
                @foreach($users as $id=>$name)
                <option value="{{$id}}" {{$issue->team_lead_id == $id ? 'selected' : ''}}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mr-t-5">
            <select class="form-control assign-tester select2" data-id="{{$issue->id}}" name="tester_id" id="user_{{$issue->id}}">
                <option value="">Select...</option>
                @foreach($users as $id=>$name)
                <option value="{{$id}}" {{$issue->tester_id == $id ? 'selected' : ''}}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history" title="Show History" data-id="{{$issue->id}}"><i class="fa fa-info-circle"></i></button>
        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs pull-request-history" title="Pull Request History" data-id="{{$issue->id}}"><i class="fa fa-history"></i></button>
    </td>
    <td>
        <div>
            @if($issue->is_resolved)
            <strong>Done</strong>
            @else
            <?php echo Form::select("task_status", $statusList, $issue->status, ["class" => "form-control resolve-issue", "onchange" => "resolveIssue(this," . $issue->id . ")"]); ?>
            @endif
            <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-status-history" title="Show Status History" data-id="{{$issue->id}}">
                <i class="fa fa-info-circle"></i>
            </button>
        </div>
    </td>
    <td>
    {{ $issue->cost ?: 0 }}
    </td>
    <td>
        @if($issue->is_milestone)
        <p style="margin-bottom:0px;">Milestone : @if($issue->is_milestone) Yes @else No @endif</p>
        <p style="margin-bottom:0px;">Total : {{$issue->no_of_milestone}}</p>
        @if($issue->no_of_milestone == $issue->milestone_completed)
        <p style="margin-bottom:0px;">Done : {{$issue->milestone_completed}}</p>
        @else
        <input type="number" name="milestone_completed" id="milestone_completed_{{$issue->id}}" placeholder="Completed..." class="form-control save-milestone" value="{{$issue->milestone_completed}}" data-id="{{$issue->id}}">
        @endif
        @else
        No
        @endif
    </td>
    <td class="p-2">
        <div style="margin-bottom:10px;width: 100%;">
            <div class="form-group">
                <input type="number" class="form-control" name="estimate_minutes{{$issue->id}}" value="{{$issue->estimate_minutes}}" min="1" autocomplete="off">
                <div style="max-width: 30px;"><button class="btn btn-sm btn-image send-approximate-lead" title="Send approximate" onclick="funDevTaskInformationUpdatesTime('estimate_minutes',{{$issue->id}})" data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button></div>
            </div>
        </div>
    </td>
    <td class="p-2">
        <div class="form-group">
            <div class='input-group date cls-start-due-date'>
                <input type="text" class="form-control" name="start_dates{{$issue->id}}" value="{{$issue->start_date}}" autocomplete="off" />
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
            <div style="max-width: 30px;"><button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate" onclick="funDevTaskInformationUpdatesTime('start_date',{{$issue->id}})" data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button></div>
        </div>
    </td>
    <td class="p-2">
        <div class="form-group">
            <div class='input-group date cls-start-due-date'>
                <input type="text" class="form-control" name="estimate_date{{$issue->id}}" value="{{$issue->estimate_date}}" autocomplete="off" />
                <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
            <div style="max-width: 30px;"><button class="btn btn-sm btn-image send-start_date-lead" title="Send approximate" onclick="funDevTaskInformationUpdatesTime('estimate_date',{{$issue->id}})" data-taskid="{{ $issue->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button></div>
        </div>
    </td>
    <td>
        <button class="btn btn-image set-remark" data-task_id="{{ $issue->id }}" data-task_type="Dev-task"><i class="fa fa-comment" aria-hidden="true"></i></button>
        <div class="dropdown dropleft">
            <a class="btn btn-secondary btn-sm dropdown-toggle" href="javascript:void(0);" role="button" id="dropdownMenuLink{{$issue->id}}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Actions
            </a>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink{{$issue->id}}">
                <a class="dropdown-item" href="javascript:void(0);" onclick="funTaskInformationModal(this, '{{ $issue->id }}')">Task Information: Update</a>
            </div>
        </div>
    </td>
</tr>


