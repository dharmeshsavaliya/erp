

<tr style="color:grey;">
    <td  >
        <a href="{{ url("development/task-detail/{$issue->id}") }}">{{ $issue->id }}
        </a>
        <a href="javascript:;" data-id="{{ $issue->id }}" class="upload-document-btn"><img width="15px" src="/images/attach.png" alt="" style="cursor: default;"><a>
        <a href="javascript:;" data-id="{{ $issue->id }}" class="list-document-btn"><img width="15px" src="/images/archive.png" alt="" style="cursor: default;"><a>
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
    <td style="vertical-align: middle;word-break: break-all;"><p>{{ $issue->subject ?? 'N/A' }}</p> </td>
    <td class="expand-row">
    <!-- class="expand-row" -->
    {{ \Illuminate\Support\Str::limit($issue->message, 150, $end='...') }}
    <input type="text" class="form-control send-message-textbox" data-id="{{$issue->id}}" id="send_message_{{$issue->id}}" name="send_message_{{$issue->id}}" style="margin-bottom:5px"/>
    <?php echo Form::select("send_message_".$issue->id,[
                        "to_developer" => "Send To Developer",
                        "to_master" => "Send To Master Developer"
                    ],null,["class" => "form-control send-message-number", "style" => "width:85% !important;display: inline;"]); ?>
    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image send-message-open" type="submit" id="submit_message"  data-id="{{$issue->id}}" ><img src="/images/filled-sent.png"/></button>

  
        <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='developer_task' data-id="{{ $issue->id }}" style="margin-top: 2%;" title="Load messages"><img src="/images/chat.png" alt=""></button>
    <br>
        <div class="td-full-container hidden">
            <button class="btn btn-secondary btn-xs" onclick="sendImage({{ $issue->id }} )">Send Attachment</button>
            <button class="btn btn-secondary btn-xs" onclick="sendUploadImage({{$issue->id}} )">Send Images</button>
            <input id="file-input{{ $issue->id }}" type="file" name="files" style="display: none;" multiple/>
         </div>
    </td>
    <td data-id="{{ $issue->id }}">
        <div class="form-group">
            <div class='input-group estimate_minutes'>
                <input style="min-width: 30px;" placeholder="E.minutes" value="{{ $issue->estimate_minutes }}" type="text" class="form-control estimate-time-change" name="estimate_minutes_{{$issue->id}}" data-id="{{$issue->id}}" id="estimate_minutes_{{$issue->id}}">
                <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-time-history" title="Show History" data-id="{{$issue->id}}"><i class="fa fa-info-circle"></i></button>
            </div>
            <!-- <button class="btn btn-secondary btn-xs estimate-time-change" data-id="{{$issue->id}}">Save</button> -->
            
        </div>
    </td>
    <td>{{ (isset($issue->timeSpent) && $issue->timeSpent->task_id > 0) ? formatDuration($issue->timeSpent->tracked) : '' }}</td>
    {{--<td>{{ $issue->submitter ? $issue->submitter->name : 'N/A' }} </td>--}}
    <td>
        <label for="" style="font-size: 12px;">Assigned To :</label>
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
        <label for="" style="font-size: 12px;margin-top:10px;">Lead :</label>
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
    </td>
    <td>
        @if($issue->is_resolved)
            <strong>Done</strong>
        @else
            <?php echo Form::select("task_status",$statusList,$issue->status,["class" => "form-control resolve-issue","onchange" => "resolveIssue(this,".$issue->id.")"]); ?>
        @endif
    </td>
    <td>
        @if($issue->cost > 0)
            {{ $issue->cost }}
        @else
            <input type="text" name="cost" id="cost_{{$issue->id}}" placeholder="Amount..." class="form-control save-cost" data-id="{{$issue->id}}">
        @endif
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
</tr>