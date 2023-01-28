@php
    $status_color = \App\TaskStatus::where('id',$task->status)->first();
@endphp
<tr style="background-color: {{$status_color->task_color}}!important;" class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} {{ !$task->due_date ? 'no-due-date' : '' }} {{ $task->is_statutory == 3 ? 'row-highlight' : '' }}" id="task_{{ $task->id }}">
    <td>
{{--        @if(auth()->user()->isAdmin())--}}
{{--        <input type="checkbox" name="selected_issue[]" value="{{$task->id}}" title="Task is in priority" {{in_array($task->id, $priority) ? 'checked' : ''}}>--}}
{{--        @endif--}}
{{--        <input type="checkbox" title="Select task" class="select_task_checkbox" name="task" data-id="{{ $task->id }}" value="">--}}
        {{ $task->id }}
    </td>
    <td class="table-hover-cell p-2">
        @php
        $special_task = \App\Task::find($task->id);
        $users_list = '';
        foreach ($special_task->users as $key => $user) {
        if ($key != 0) {
        $users_list .= ', ';
        }
        if (array_key_exists($user->id, $users)) {
        $users_list .= $users[$user->id];
        } else {
        $users_list = 'User Does Not Exist';
        }
        }

        $users_list .= ' ';

        foreach ($special_task->contacts as $key => $contact) {
        if ($key != 0) {
        $users_list .= ', ';
        }

        $users_list .= "$contact->name - $contact->phone" . ucwords($contact->category);
        }
        @endphp

        <!--<span class="td-mini-container">
          {{ strlen($users_list) > 15 ? substr($users_list, 0, 15) : $users_list }}
        </span>-->

        @if(auth()->user()->isAdmin())
        <select id="assign_to" class="form-control menu-task-assign-user select2" data-id="{{$task->id}}" data-lead="1" name="master_user_id" id="user_{{$task->id}}">
            <option value="">Select...</option>
            <?php $masterUser = isset($task->assign_to) ? $task->assign_to : 0; ?>
            @foreach($users as $id=>$name)
            @if( $masterUser == $id )
            <option value="{{$id}}" selected>{{ $name }}</option>
            @else
            <option value="{{$id}}">{{ $name }}</option>
            @endif
            @endforeach
        </select>
        @else
        @if($task->assign_to)
        @if(isset($users[$task->assign_to]))
        <p>{{$users[$task->assign_to]}}</p>
        @else
        <p>-</p>
        @endif
        @endif
        @endif

        <span class="td-full-container hidden">
            {{ $users_list }}
        </span>
        <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs menu-show-user-history" title="Show History" data-id="{{$task->id}}"><i class="fa fa-info-circle"></i></button>
        <div class="col-md-12 expand-col dis-none" style="padding:0px;">
            <br>
            <label for="" style="font-size: 12px;margin-top:10px;">Lead :</label>
            <select id="master_user_id" class="form-control assign-master-user select2" data-id="{{$task->id}}" data-lead="1" name="master_user_id" id="user_{{$task->id}}">
                <option value="">Select...</option>
                <?php $masterUser = isset($task->master_user_id) ? $task->master_user_id : 0; ?>
                @foreach($users as $id=>$name)
                @if( $masterUser == $id )
                <option value="{{$id}}" selected>{{ $name }}</option>
                @else
                <option value="{{$id}}">{{ $name }}</option>
                @endif
                @endforeach
            </select>
            <br />
            @if(auth()->user()->isAdmin())
            <label for="" style="font-size: 12px;margin-top:10px;">Lead 2 :</label>
            <select id="master_user_id" class="form-control assign-master-user select2" data-id="{{$task->id}}" data-lead="2" name="master_user_id" id="user_{{$task->id}}">
                <option value="">Select...</option>
                <?php $masterUser = isset($task->second_master_user_id) ? $task->second_master_user_id : 0; ?>
                @foreach($users as $id=>$name)
                @if( $masterUser == $id )
                <option value="{{$id}}" selected>{{ $name }}</option>
                @else
                <option value="{{$id}}">{{ $name }}</option>
                @endif
                @endforeach
            </select>
            @else
            @if($task->second_master_user_id)
            @if(isset($users[$task->second_master_user_id]))
            <p>{{$users[$task->second_master_user_id]}}</p>
            @else
            <p>-</p>
            @endif
            @endif
            @endif


            <label for="" style="font-size: 12px;margin-top:10px;">Due date :</label>
            <div class="d-flex">
                <div class="form-group" style="padding-top:5px;">
                    <div class='input-group date due-datetime'>

                        <input type="text" class="form-control input-sm due_date_cls" name="due_date" value="{{$task->due_date}}" />

                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>

                    </div>
                </div>
                <button class="btn btn-sm btn-image set-due-date" title="Set due date" data-taskid="{{ $task->id }}"><img style="padding: 0;margin-top: -14px;" src="{{asset('images/filled-sent.png')}}" /></button>
            </div>

            @if($task->is_milestone)
            <p style="margin-bottom:0px;">Total : {{$task->no_of_milestone}}</p>
            @if($task->no_of_milestone == $task->milestone_completed)
            <p style="margin-bottom:0px;">Done : {{$task->milestone_completed}}</p>
            @else
            <input type="number" name="milestone_completed" id="milestone_completed_{{$task->id}}" placeholder="Completed..." class="form-control save-milestone" value="{{$task->milestone_completed}}" data-id="{{$task->id}}">
            @endif
            @else
            <p>No milestone</p>
            @endif
        </div>
    </td>
    <td class="table-hover-cell p-2 {{ ($task->message && $task->message_status == 0) || $task->message_is_reminder == 1 || ($task->message_user_id == $task->assign_from && $task->assign_from != Auth::id()) ? 'text-danger' : '' }}">
        @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))

        <div style="margin-bottom:10px;width: 100%;">
            <?php $text_box = "100"; ?>
            <div class="d-flex">
                <input type="text" style="width: 100%;" class="form-control quick-message-field input-sm" id="getMsg{{$task->id}}" name="message" placeholder="Message" value="">
                <div style="max-width: 30px;">
                    <button type="button" class="btn btn-sm btn-image menu-send-message" title="Send message" data-taskid="{{ $task->id }}"><img src="{{asset('images/filled-sent.png')}}" /></button>
                </div>
                @if (isset($task->message))
                <div style="max-width: 30px;">
                    <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='task' data-id="{{ $task->id }}" title="Load messages"><img src="{{asset('images/chat.png')}}" alt=""></button>
                </div>
                @endif
            </div>
            @if (isset($task->message))
            <div style="margin-bottom:10px;width: 100%;">
                <div class="d-flex justify-content-between expand-row-msg" data-id="{{$task->id}}">
                    <span class="td-mini-container-{{$task->id}}" style="margin:0px;">
                        <?php
                        if (!empty($task->message) && !empty($task->task_subject)) {
                            $pos = strpos($task->message, $task->task_subject);
                            $length = strlen($task->task_subject);
                            if ($pos) {
                                $start = $pos + $length + 1;
                            } else {
                                $start = 0;
                            }
                        } else {
                            $start = 0;
                        }
                        ?>
                        {{substr($task->message, $start,28)}}
                    </span>
                </div>
                <div class="expand-row-msg" data-id="{{$task->id}}">
                    <span class="td-full-container-{{$task->id}} hidden">
                        {{ $task->message }}
                    </span>
                </div>
            </div>
            @endif
        </div>
        @else
        Private
        @endif
    </td>
</tr>