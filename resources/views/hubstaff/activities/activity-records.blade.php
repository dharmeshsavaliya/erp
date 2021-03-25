
    <form>
    @csrf

    <input type="hidden" name="user_id" value="{{$user_id}}">
    <input type="hidden" name="date" value="{{$date}}">

    <div class="modal-header">
        <h4 class="modal-title"></h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    </div>
    <div class="modal-body">
    <div>
        <table class="table table-bordered" style="table-layout:fixed;">
        <tr>
          <th style="width:20%">Date & time</th>
          <th style="width:15%">Time tracked</th>
          <th style="width:15%">Time Approved</th>
          <th style="width:25%">Task</th>
          <th style="width:18%">Efficiency</th>
          <th style="width:7%" class="text-center">Action</th>
        </tr>
          @foreach ($activityrecords as $record)
            <tr>
            <td>{{ $record->OnDate }} {{$record->onHour}}:00:00 </td>
              <td>{{ number_format($record->total_tracked / 60,2,".",",") }}</td>
              <td>{{ number_format($record->totalApproved / 60,2,".",",") }}</td>
              <td>
                <?php $listOFtask = []; ?>
                @foreach ($record->activities as $a)
                    @if(!empty($a->taskSubject)) 
                      <?php 
                        list($type, $id) = explode("-",$a->taskSubject);
                      ?>
                      <a href="javascript:;" data-id="{{$a->task_id}}" class="show-task-history">{{$a->taskSubject}}</a><br>
                    @endif
                @endforeach
              </td>
              <td>
              <div class="form-group" style="margin:0px;">
                   @if(isset($member))
                   <?php
                    $eficiency = \App\HubstaffTaskEfficiency::where('user_id',$member->user_id)->where('date',$record->OnDate)->where('time',$record->onHour)->first();
                    $user_input = null;
                    $admin_input = null;
                    if($eficiency) {
                      $user_input = $eficiency->user_input;
                      $admin_input = $eficiency->admin_input;
                    }
                    ?>
                     <p style="margin:0px;"> <strong>Admin : {{$admin_input}}</strong></p>
                     <p style="margin:0px;"> <strong>User : {{$user_input}}</strong></p>
                   @if(Auth::user()->id == $member->user_id) 
                    <select name="efficiency" class="task_efficiency form-control"  data-type="user" data-date="{{ $record->OnDate }}" data-hour="{{$record->onHour}}" data-user_id="{{$member->user_id}}">
                        <option value="">Select One</option>
                        <option value="Excellent" {{$user_input == 'Excellent' ? 'selected' : ''}}>Excellent</option>
                        <option value="Good" {{$user_input == 'Good' ? 'selected' : ''}}>Good</option>
                        <option value="Average" {{$user_input == 'Average' ? 'selected' : ''}}>Average </option>
                        <option value="Poor" {{$user_input == 'Poor' ? 'selected' : ''}}>Poor</option>
                    </select>
                    @endif
                    @if(Auth::user()->isAdmin()) 
                    <select name="efficiency" class="task_efficiency form-control"  data-type="admin" data-date="{{ $record->OnDate }}" data-hour="{{$record->onHour}}" data-user_id="{{$member->user_id}}">
                        <option value="">Select One</option>
                        <option value="Excellent" {{$admin_input == 'Excellent' ? 'selected' : ''}}>Excellent</option>
                        <option value="Good" {{$admin_input == 'Good' ? 'selected' : ''}}>Good</option>
                        <option value="Average" {{$admin_input == 'Average' ? 'selected' : ''}}>Average </option>
                        <option value="Poor" {{$admin_input == 'Poor' ? 'selected' : ''}}>Poor</option>
                    </select>
                    @endif
                    @endif
                </div>
              </td>
              <td>
              &nbsp;<input type="checkbox" name="sample" {{$record->sample ? 'checked' : ''}}  data-id="{{ $record->OnDate }}{{$record->onHour}}" class="selectall"/>
                <a data-toggle="collapse" href="#collapse_{{ $record->OnDate }}{{$record->onHour}}"><img style="height:15px;" src="/images/forward.png"></a>
              </td>
            </tr>
            <tr style="width:100%;" id="collapse_{{ $record->OnDate }}{{$record->onHour}}" class="panel-collapse collapse">
            <td colspan="6" style="padding:0px;">
              <table style="table-layout:fixed;" class="table table-bordered">
              @foreach ($record->activities as $a)
                <tr>
                <td style="width:18%">{{ $a->starts_at}}</td>
                  <td style="width:15%">{{ number_format($a->tracked / 60,2,".",",") }}@if($a->is_manual) (Manual time) @endif</td>
                  <td style="width:15%">{{ number_format($a->totalApproved / 60,2,".",",") }}</td>
                  <td style="width:25%">{{ $a->taskSubject}}</td>
                  <td style="width:20%"></td>
                  <td style="width:7%">
                    <input type="checkbox" class="{{ $record->OnDate }}{{$record->onHour}}" value="{{$a->id}}" name="activities[]" {{$a->status ? 'checked' : ''}}>
                  </td>
                </tr>
              @endforeach
              </table>
            </td>
            </tr>
          @endforeach
      </table>
    </div>
    <input type="hidden" id="hidden-forword-to" name="forworded_person">
    @if($isAdmin)
    <!-- <div class="form-group">
        <label for="forword_to">Forword to user</label>
        <select name="forword_to_user" id="" data-person="user" class="form-control select-forword-to">
          <option value="">Select</option>
          @foreach($users as $user)
          <option value="{{$user->id}}">{{$user->name}}</option>
          @endforeach
        </select>
    </div> -->
    @if(count($teamLeaders) > 0)
      <div class="form-group">
          <label for="forword_to">Forword to team leader</label>
          <select name="forword_to_team_leader" id="" data-person="team_lead" class="form-control select-forword-to">
            <option value="">Select</option>
            @foreach($teamLeaders as $ld)
            <option value="{{$ld->id}}">{{$ld->name}}</option>
            @endforeach
          </select>
      </div>
      @endif
    @endif
    @if($isTeamLeader)
      <div class="form-group">
          <label for="forword_to">Forword to admin</label>
          <select name="forword_to_admin" id="" data-person="admin" class="form-control select-forword-to">
            <option value="">Select</option>
            @foreach($admins as $admin)
            <option value="{{$admin->id}}">{{$admin->name}}</option>
            @endforeach
          </select>
      </div>
    @endif
    @if($taskOwner)
      <div class="form-group">
          <label for="forword_to">Forword to admin</label>
          <select name="forword_to_admin" id="" data-person="admin" class="form-control select-forword-to">
            <option value="">Select</option>
            @foreach($admins as $admin)
            <option value="{{$admin->id}}">{{$admin->name}}</option>
            @endforeach
          </select>
      </div>
      @if(count($teamLeaders) > 0)
      <div class="form-group">
          <label for="forword_to">Forword to team leader</label>
          <select name="forword_to_team_leader" id="" data-person="team_lead" class="form-control select-forword-to">
            <option value="">Select</option>
            @foreach($teamLeaders as $ld)
            <option value="{{$ld->id}}">{{$ld->name}}</option>
            @endforeach
          </select>
      </div>
      @endif
    @endif
    @if($hubActivitySummery)
    <div class="form-group">
        <label for="">Previous remarks</label>
        <textarea class="form-control" cols="30" rows="5" name="previous_remarks" placeholder="Rejection note...">@if($hubActivitySummery){{$hubActivitySummery->rejection_note}}@endif</textarea>
    </div>
    @endif
    <div class="form-group">
        <label for="">New remarks</label>
        <textarea class="form-control" name="rejection_note" id="rejection_note" cols="30" rows="5" placeholder="Rejection note..."></textarea>
    </div>
    </div>
    <div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    @if($isAdmin)
    <button type="submit" class="btn btn-danger final-submit-record">Approve</button>
    @if(count($teamLeaders) > 0)
    <button type="submit" class="btn btn-danger submit-record">Forword</button>
    @endif
    @else
    <button type="submit" class="btn btn-danger submit-record">Forword</button> 
    @endif
    
    </div>
</form>

<script type="text/javascript">
    $('#date_of_payment').datetimepicker({
      format: 'YYYY-MM-DD'
    });

    $(document).on("click",".show-task-history",function() {
        var taskid = $(this).data("id");
        $.ajax({
              type: 'GET',
              url: '/hubstaff-activities/activities/task-history',
              data: {
                  task_id: taskid
              }
          }).done(response => {
              console.log(response);
          }).fail(function (response) {
              
          });
    });

</script>
