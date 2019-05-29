@extends('layouts.app')


@section('content')

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">

    {{-- <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="page-heading">Task & Activity</h2>
        </div>
    </div>

    @include('partials.flash_messages')

        <div class="row">
            @can('view-activity')
                <div class="col-md-5 col-12">
                    <h4>User</h4>
                    <form action="{{ route('task.index') }}" method="GET" enctype="multipart/form-data">
                      <input type="hidden" name="daily_activity_date" value="{{ $data['daily_activity_date'] }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <strong>Select User</strong>
                                    <?php
                                    echo Form::select( 'selected_user', $users, $selected_user, [
                                        'class' => 'form-control',
                                        'name'  => 'selected_user'
                                    ] );?>
                                </div>

                                <div class="form-group">
                                  <strong>Select Category</strong>
                                  <?php
                                  $categories = \App\Http\Controllers\TaskCategoryController::getAllTaskCategory();

                                  echo Form::select('category', $categories, (old('category') ? old('category') : $category), ['placeholder' => 'Select a category','class' => 'form-control']);

                                  ?>
                                </div>
                            </div>
                            <div class="col-md-4 mt-4">
                                <strong>&nbsp;&nbsp;</strong>
                                <button type="submit" class="btn btn-secondary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-7 col-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h4>Export Task</h4></div>
                        <div class="panel-body">
                            <form action="{{ route('task.export') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <strong>User</strong>
                                            <?php
                                            echo Form::select( 'selected_user', $users, '' , [
                                                'class'       => 'form-control',
                                                'multiple' => 'multiple',
                                                'id' => 'userList',
                                                'name' => 'selected_user[]',
                                            ] );?>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <strong>Date Range</strong>
                                            <input type="text" value="" name="range_start" hidden/>
                                            <input type="text" value="" name="range_end" hidden/>
                                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                                <i class="fa fa-calendar"></i>&nbsp;
                                                <span></span> <i class="fa fa-caret-down"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2 mt-4">
                                        <button type="submit" class="btn btn-secondary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

        <div class="row mb-3">
          <div class="col-sm-5">
            <h5>Create Quick Contact</h5>
            <form action="{{ route('contact.store') }}" method="POST">
              @csrf

              <div class="form-inline">
                <div class="form-group flex-fill d-flex">
                  <input type="text" name="category" class="form-control input-sm flex-fill" placeholder="Category" value="{{ old('category') }}">
                </div>

                <div class="form-group flex-fill d-flex ml-1">
                  <input type="text" name="name" class="form-control input-sm flex-fill" placeholder="Contact Name" value="{{ old('name') }}" required>
                </div>
              </div>

              <div class="form-group mt-1">
                <input type="text" name="phone" class="form-control input-sm" placeholder="Contact Phone" value="{{ old('phone') }}" required>
              </div>

              <button type="submit" class="btn btn-xs btn-secondary">Create</button>
            </form>
          </div>

          <div class="col-md-7">
            <h5>Create Task Category</h5>
            <form class="form-inline" action="{{ route('task_category.store') }}" method="POST">
              @csrf

              <div class="form-group">
                <input type="text" name="name" value="{{ old('name') }}" class="form-control input-sm" placeholder="Category Name">
              </div>

              <button type="submit" class="btn btn-xs btn-secondary ml-1">Create</button>
            </form>
          </div>
        </div>


        <?php
        if ( \App\Helpers::getadminorsupervisor() && ! empty( $selected_user ) )
            $isAdmin = true;
        else
            $isAdmin = false;
        ?>
            <div class="row">
                <div class="col-sm-5 col-12">

                    <div class="panel panel-default">
                        <div class="panel-heading"><h4>Assign Task</h4></div>
                        <div class="panel-body">
                            <form action="{{ route('task.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <strong>Task Subject:</strong>
                                <input type="text" class="form-control" name="task_subject" placeholder="Task Subject" value="{{ old('task_subject') }}" required />
                                @if ($errors->has('task_subject'))
                                    <div class="alert alert-danger">{{$errors->first('task_subject')}}</div>
                                @endif
                            </div>
                            <div class="form-group">
                                <strong>Task Details:</strong>
                                <textarea class="form-control" name="task_details" placeholder="Task Details" required>{{ old('task_details') }}</textarea>
                                @if ($errors->has('task_details'))
                                    <div class="alert alert-danger">{{$errors->first('task_details')}}</div>
                                @endif
                            </div>
                            <div class="form-group">
                                <strong>Completion Date:</strong>
                                <div class='input-group date' id='completion-datetime'>
                                    <input type='text' class="form-control" name="completion_date" value="{{ date('Y-m-d H:i') }}" />

                                    <span class="input-group-addon">
                              <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                                </div>

                                @if ($errors->has('completion_date'))
                                    <div class="alert alert-danger">{{$errors->first('completion_date')}}</div>
                                @endif
                            </div>
                            {{-- <div id="completion_date" class="form-group">
                                <strong>Completion Date:</strong>
                                <input type='text' class="form-control" name="completion_date" id="completion-datetime" />
                                {{-- <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span> --}}
                            {{-- <input type="datetime-local" name="completion_date" class="form-control" placeholder="Completion Date" value="{{ date('Y-m-d\TH:i') }}" id="completion-datetime">
                            @if ($errors->has('completion_date'))
                                <div class="alert alert-danger">{{$errors->first('completion_date')}}</div>
                            @endif
                        </div> --}}

                            <div class="form-group">
                                <select name="is_statutory" class="form-control is_statutory">
                                    <option value="0">Other Task </option>
                                    <option value="1">Statutory Task </option>
                                </select>
                            </div>
                            <div class="form-group">
                                <strong>Assigned To (users):</strong>
                                <select class="selectpicker form-control" data-live-search="true" data-size="15" name="assign_to[]" id="first_customer" title="Choose a User" multiple>
                                  @foreach ($data['users'] as $user)
                                    <option data-tokens="{{ $user['name'] }} {{ $user['email'] }}" value="{{ $user['id'] }}">{{ $user['name'] }} - {{ $user['email'] }}</option>
                                  @endforeach
                                </select>

                                @if ($errors->has('assign_to'))
                                  <div class="alert alert-danger">{{$errors->first('assign_to')}}</div>
                                @endif
                            </div>

                            <div class="form-group">
                              <strong>Assigned To (contacts):</strong>
                              <select class="selectpicker form-control" data-live-search="true" data-size="15" name="assign_to_contacts[]" title="Choose a Contact" multiple>
                                @foreach (Auth::user()->contacts as $contact)
                                  <option data-tokens="{{ $contact['name'] }} {{ $contact['phone'] }} {{ $contact['category'] }}" value="{{ $contact['id'] }}">{{ $contact['name'] }} - {{ $contact['phone'] }} ({{ $contact['category'] }})</option>
                                @endforeach
                              </select>

                              @if ($errors->has('assign_to_contacts'))
                                <div class="alert alert-danger">{{$errors->first('assign_to_contacts')}}</div>
                              @endif
                            </div>

                            <div id="recurring-task" style="display: none;">
                                <div class="form-group">
                                    <strong>Recurring Type:</strong>
                                    <select name="recurring_type" class="form-control">
                                        <option value="EveryDay">EveryDay</option>
                                        <option value="EveryWeek">EveryWeek</option>
                                        <option value="EveryMonth">EveryMonth</option>
                                        <option value="EveryYear">EveryYear</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <strong>Recurring Day:</strong>
                                    <div id="recurring_day"></div>
                                </div>
                            </div>

                            <div class="form-group">
                                <strong>Category:</strong>
                                <?php
                                $categories = \App\Http\Controllers\TaskCategoryController::getAllTaskCategory();

                                echo Form::select('category',$categories, ( old('category') ? old('category') : $category ), ['placeholder' => 'Select a category','class' => 'form-control']);

                                ?>
                            </div>

                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-secondary">Submit</button>
                            </div>

                        </form>
                        </div>
                    </div>

                </div>
                <div class="col-sm-7 col-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><h4>Daily Activity</h4></div>
                        <div class="panel-body">
                            <div class="mt-2 mb-2 text-right">
                              <form action="/task" method="GET" class="form-inline">
                                @if (!empty($selected_user))
                                  <input type="hidden" name="selected_user" value="{{ $selected_user }}">
                                @endif
                                <div class='input-group date' id='daily_activity_date'>
                                  <input type='text' class="form-control" name="daily_activity_date" value="{{ $data['daily_activity_date'] }}" />

                                  <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                                <button type="submit" class="btn btn-secondary ml-1">Submit</button>
                                @if(!$isAdmin)
                                  <button id="add-row" type="button" class="btn btn-secondary ml-5">Add Row</button>
                                @endif
                                <button id="save-activity" type="button" class="btn btn-secondary">Save</button>
                                <img id="loading_activty" style="display: none" src="{{ asset('images/loading.gif') }}"/>
                              </form>
                            </div>

                            <div id="daily_activity"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="row">
                <div class="col-12">
                    <h4>Today's Statutory Activity List</h4>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Sr No</th>
                            <th>Date</th>
                            <th class="category">Category</th>
                            <th>Task Details</th>
                            <th>Assigned From</th>
                            <th>Assigned To</th>
                            <th>Remark</th>
                            <th>Completed</th>
                            <th style="width: 80px;">Action</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div> -->

            <form class="form-inline" action="{{ route('task.index') }}" method="GET" enctype="multipart/form-data">
              <div class="form-group">
                <input type="text" name="term" placeholder="Task ID" class="form-control" value="{{ isset($term) ? $term : "" }}">
              </div>

              <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
            </form>

            <br/><br/>
            <div id="exTab2" class="container" style="overflow: auto">
               <ul class="nav nav-tabs">
                  <li class="active">
                     <a  href="#1" data-toggle="tab">Pending Task</a>
                  </li>
                  <li><a href="#2" data-toggle="tab">Statutory Activity</a>
                  </li>
                  <li><a href="#3" data-toggle="tab">Completed Task</a>
                  </li>
                  <li><a href="#unassigned-tab" data-toggle="tab">Unassigned Messages</a></li>
               </ul>
               <div class="tab-content ">
                    <!-- Pending task div start -->
                    <div class="tab-pane active" id="1">
                        <div class="row">
                           <!-- <h4>List Of Pending Tasks</h4> -->
                            <table class="table table-bordered">
                                <thead>
                                  <tr>
                                      <th width="5%">ID</th>
                                      <th width="5%">Date</th>
                                      <th width="10%" class="category">Category</th>
                                      <th width="25%">Task Subject</th>
                                      <th width="5%">Est Completion Date</th>
                                      <th width="5%">Assigned From</th>
                                      <th width="5%">Assigned To</th>
                                      <th width="20%">Communication</th>
                                      <th width="10%">Send Message</th>
                                      {{-- <th>Remarks</th> --}}
                                      <th width="10%">Action</th>
                                  </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1 ?>
                                  @foreach($data['task']['pending'] as $task)
                                <tr class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }}" id="task_{{ $task->id }}">
                                    <td>{{ $task->id }}</td>
                                    <td>{{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}</td>
                                    <td> {{ isset( $categories[$task->category] ) ? $categories[$task->category] : '' }}</td>
                                    <td class="task-subject" data-subject="{{$task->task_subject ? $task->task_subject : 'Task Details'}}" data-details="{{$task->task_details}}" data-switch="0">
                                      {{ $task->task_subject ? $task->task_subject : 'Task Details' }}
                                    </td>
                                    <td> {{ Carbon\Carbon::parse($task->completion_date)->format('d-m H:i')  }}</td>
                                    <td>{{ $users[$task->assign_from] }}</td>
                                    <td>
                                      @php
                                        $special_task = \App\Task::find($task->id);
                                      @endphp

                                      @foreach ($special_task->users as $key => $user)
                                        @if ($key != 0)
                                          ,
                                        @endif

                                        @if (array_key_exists($user->id, $users))
                                          @if ($user->id == Auth::id())
                                            <a href="{{ route('users.show', $user->id) }}">{{ $users[$user->id] }}</a>
                                          @else
                                            {{ $users[$user->id] }}
                                          @endif
                                        @else
                                          User Does Not Exist
                                        @endif
                                      @endforeach

                                      <br>

                                      @foreach ($special_task->contacts as $key => $contact)
                                        @if ($key != 0)
                                          ,
                                        @endif

                                        {{ $contact->name }} - {{ $contact->phone }} ({{ ucwords($contact->category) }})
                                      @endforeach

                                      @if ($special_task->users->contains(Auth::id()) || $task->assign_from == Auth::id())
                                        <a href="/task/complete/{{ $task->id }}" class="btn btn-xs btn-secondary">Complete</a>
                                      @else
                                        {{-- @foreach ($special_task->users as $key => $task_user)
                                          @if ($key != 0)
                                            ,
                                          @endif
                                          {{ array_key_exists($task_user->id, $users) ? $users[$task_user->id] : 'No User' }}
                                        @endforeach

                                        @foreach ($special_task->contacts as $key => $task_user)
                                          @if ($key != 0)
                                            ,
                                          @endif
                                          Contact
                                        @endforeach --}}
                                      @endif
                                    </td>

                                    <td>
                                      @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                        @if (isset($task->message))
                                          {{ strlen($task->message) > 100 ? substr($task->message, 0, 97) . '...' : $task->message }}
                                        @endif
                                      @else
                                        Private
                                      @endif
                                    </td>
                                    <td>
                                      @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                        <div class="d-inline">
                                          <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="">
                                          <button class="btn btn-sm btn-image send-message" data-taskid="{{ $task->id }}"><img src="/images/filled-sent.png" /></button>
                                        </div>
                                      @else
                                        Private
                                      @endif
                                    </td>

                                    <td>
                                        @if ($task->assign_to != Auth::id())
                                          @if ($task->is_private == 1)
                                            <button type="button" class="btn btn-image"><img src="/images/private.png" /></button>
                                          @else
                                            <a href="{{ route('task.show', $task->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a>
                                          @endif
                                        @endif

                                        @if ($task->assign_to == Auth::id())
                                          <a href="{{ route('task.show', $task->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a>

                                          @if ($task->is_private == 1)
                                            <button type="button" class="btn btn-image make-private-task" data-taskid="{{ $task->id }}"><img src="/images/private.png" /></button>
                                          @else
                                            <button type="button" class="btn btn-image make-private-task" data-taskid="{{ $task->id }}"><img src="/images/not-private.png" /></button>
                                          @endif
                                        @endif

                                        {{-- <a href id="add-new-remark-btn" class="add-task" data-toggle="modal" data-target="#add-new-remark_{{$task->id}}" data-id="{{$task->id}}">Add</a>
                                        <span> | </span>
                                        <a href id="view-remark-list-btn" class="view-remark  {{ $task->remark ? 'text-danger' : '' }}" data-toggle="modal" data-target="#view-remark-list" data-id="{{$task->id}}">View</a> --}}
                                    </td>
                                </tr>


                                <!-- Modal -->
                                <div id="add-new-remark_{{$task->id}}" class="modal fade" role="dialog">
                                  <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h4 class="modal-title">Add New Remark</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                                      </div>
                                      <div class="modal-body">
                                        <form id="add-remark">
                                          <input type="hidden" name="id" value="">
                                          <textarea id="remark-text_{{$task->id}}" rows="1" name="remark" class="form-control"></textarea>
                                          <button type="button" class="mt-2 " onclick="addNewRemark({{$task->id}})">Add Remark</button>
                                      </form>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                      </div>
                                    </div>

                                  </div>
                                </div>

                                <!-- Modal -->
                                <div id="view-remark-list" class="modal fade" role="dialog">
                                  <div class="modal-dialog">

                                    <!-- Modal content-->
                                    <div class="modal-content">
                                      <div class="modal-header">
                                        <h4 class="modal-title">View Remark</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>

                                      </div>
                                      <div class="modal-body">
                                        <div id="remark-list">

                                        </div>
                                      </div>
                                      <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                      </div>
                                    </div>

                                  </div>
                                </div>
                               @endforeach
                                </tbody>
                              </table>
                        </div>
                    </div>
                    <!-- Pending task div end -->
                    <!-- Statutory task div start -->
                    <div class="tab-pane" id="2">
                        <div class="row">
                            <div class="col-12">
                                <!-- <h4>Statutory Activity Completed</h4> -->
                                <table class="table table-bordered">
                                <thead>
                                  <tr>
                                      <th>ID</th>
                                      <th>Date</th>
                                      <th class="category">Category</th>
                                      <th>Task Details</th>
                                      <th>Assigned From</th>
                                      <th>Assigned To</th>
                                      <th>Remark</th>
                                      <th>Communication</th>
                                      <th>Send Message</th>
                                      <th>Completed at</th>
                                      <th>Actions</th>
                                  </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1 ?>
                                  @foreach(  $data['task']['statutory_completed'] as $task)
                                <tr id="task_{{ $task->id }}">
                                    <td>{{ $task->id }}</td>
                                    <td>{{ Carbon\Carbon::parse($task->created_at)->format('d-m H:i') }}</td>
                                    <td>{{ isset( $categories[$task->category]) ? $categories[$task->category] : '' }}</td>
                                    <td class="task-subject" data-subject="{{$task->task_subject ? $task->task_subject : 'Task Details'}}" data-details="{{ $task->task_details }}" data-switch="0">
                                      {{ $task->task_subject ? $task->task_subject : 'Task Details' }}
                                    </td>
                                    <td>{{ array_key_exists($task->assign_from, $users) ? $users[$task->assign_from] : 'No User' }}</td>
                                    <td>
                                      @php
                                        $special_task = \App\Task::find($task->id);
                                      @endphp

                                      @foreach ($special_task->users as $key => $user)
                                        @if ($key != 0)
                                          ,
                                        @endif

                                        @if (array_key_exists($user->id, $users))
                                          @if ($user->id == Auth::id())
                                            <a href="{{ route('users.show', $user->id) }}">{{ $users[$user->id] }}</a>
                                          @else
                                            {{ $users[$user->id] }}
                                          @endif
                                        @else
                                          User Does Not Exist
                                        @endif
                                      @endforeach

                                      <br>

                                      @foreach ($special_task->contacts as $key => $contact)
                                        @if ($key != 0)
                                          ,
                                        @endif

                                        {{ $contact->name }} - {{ $contact->phone }} ({{ ucwords($contact->category) }})
                                      @endforeach

                                      @if ($special_task->users->contains(Auth::id()) || $task->assign_from == Auth::id())
                                        <a href="/task/complete/{{ $task->id }}" class="btn btn-xs btn-secondary">Complete</a>
                                      @else
                                        {{-- @foreach ($special_task->users as $key => $task_user)
                                          @if ($key != 0)
                                            ,
                                          @endif
                                          {{ array_key_exists($task_user->id, $users) ? $users[$task_user->id] : 'No User' }}
                                        @endforeach

                                        @foreach ($special_task->contacts as $key => $task_user)
                                          @if ($key != 0)
                                            ,
                                          @endif
                                          Contact
                                        @endforeach --}}
                                      @endif
                                    </td>
                                    <td>
                                      {{-- @include('task-module.partials.remark',$task) --}}
                                      <textarea id="remark-text-{{ $task->id }}" rows="1" name="remark" class="form-control"></textarea>
                                      <button class="mt-2 update-remark" data-id="{{$task->id}}">update</button>
                                      <img id="remark-load-{{$task->id}}" style="display: none" src="{{ asset('images/loading.gif') }}"/>
                                      <span id="remarks-{{$task->id}}" >
                                        @foreach(\App\Task::getremarks($task->id) as $remark)
                                          <p> {{$remark['remark']}} <br> <small>updated on {{ Carbon\Carbon::parse($remark['created_at'])->format('d-m H:i') }}</small></p>
                                          <hr>
                                        @endforeach
                                      </span>
                                    </td>
                                    <td>
                                      @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                        @if (isset($task->message))
                                          {{ strlen($task->message) > 100 ? substr($task->message, 0, 97) . '...' : $task->message }}
                                        @endif
                                      @else
                                        Private
                                      @endif
                                    </td>
                                    <td>
                                      @if ($task->assign_to == Auth::id() || ($task->assign_to != Auth::id() && $task->is_private == 0))
                                        <div class="d-inline">
                                          <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="">
                                          <button class="btn btn-sm btn-image send-message" data-taskid="{{ $task->id }}"><img src="/images/filled-sent.png" /></button>
                                        </div>
                                      @else
                                        Private
                                      @endif
                                    </td>
                                    <td>{{ Carbon\Carbon::parse($task->completion_date)->format('d-m H:i') }}</td>
                                    <td>
                                      @if ($task->assign_to != Auth::id())
                                        @if ($task->is_private == 1)
                                          <button type="button" class="btn btn-image"><img src="/images/private.png" /></button>
                                        @else
                                          <a href="{{ route('task.show', $task->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a>
                                        @endif
                                      @endif

                                      @if ($task->assign_to == Auth::id())
                                        <a href="{{ route('task.show', $task->id) }}" class="btn btn-image" href=""><img src="/images/view.png" /></a>

                                        @if ($task->is_private == 1)
                                          <button type="button" class="btn btn-image make-private-task" data-taskid="{{ $task->id }}"><img src="/images/private.png" /></button>
                                        @else
                                          <button type="button" class="btn btn-image make-private-task" data-taskid="{{ $task->id }}"><img src="/images/not-private.png" /></button>
                                        @endif
                                      @endif
                                    </td>
                                </tr>
                               @endforeach
                                </tbody>
                              </table>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h4>All Statutory Activity List</h4>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Date</th>
                                            <th class="category">Category</th>
                                            <th>Task Details</th>
                                            <th>Assigned From</th>
                                            <th>Assigned To</th>
                                            <th>Recurring Type</th>
                                            <th>Remarks</th>
                                            <th>Completed</th>
                                            {{--<th>Remark</th>--}}
                                            {{--<th>Completed</th>--}}
                                            {{--<th style="width: 80px;">Action</th>--}}
                                        </tr>
                                    </thead>
                                <tbody>
                                    <?php $i = 1 ?>
                                    @foreach(  $data['task']['statutory'] as $task)
                                            <tr>
                                                <td>{{ $task['id'] }}</td>
                                                <td> {{ Carbon\Carbon::parse($task['created_at'])->format('d-m H:i') }}</td>
                                                <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
                                                <td class="task-subject" data-subject="{{$task['task_subject'] ? $task['task_subject'] : 'Task Details'}}" data-details="{{$task['task_details']}}" data-switch="0">{{ $task['task_subject'] ? $task['task_subject'] : 'Task Details' }}</td>
                                                <td>{{ $users[$task['assign_from']]}}</td>
                                                <td>
                                                  {{ $task['assign_to'] ?? ($users[$task['assign_to']] ? $users[$task['assign_to']] : 'Nil') }}
                                                </td>
                                                <td>{{ $task['recurring_type'] }}</td>
                                                {{-- <td>{{ $task['recurring_day'] ?? 'nil' }}</td> --}}
                                                <td> @include('task-module.partials.remark',$task) </td>
                                                <td>
                                                  @if( Auth::id() == $task['assign_to'] )
                                                    @if ($task['completion_date'])
                                                      {{ Carbon\Carbon::parse($task['completion_date'])->format('d-m H:i') }}
                                                    @else
                                                      <a href="/statutory-task/complete/{{$task['id']}}">Complete</a>
                                                    @endif
                                                  @endif
                                                </td>
                                                {{--<td>
                                                    <form method="POST" action="task/deleteStatutoryTask" enctype="multipart/form-data">
                                                        @csrf
                                                        <input hidden name="id" value="{{ $task['id'] }}">
                                                        <button type="submit" class="">Delete</button>
                                                    </form>
                                                </td>--}}
                                            </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Statutory task div end -->
                    <!-- Completed task div start -->
                    <div class="tab-pane" id="3">
                        <div class="row">
                           <!-- <h4>List Of Completed Tasks</h4> -->
                            <table class="table table-bordered">
                                <thead>
                                  <tr>
                                  <th>ID</th>
                                  <th>Date</th>
                                  <th class="category">Category</th>
                                  <th>Task Details</th>
                                  <th>Est Completion Date</th>
                                  <th>Assigned From</th>
                                  <th>Assigned To</th>
                                  <th>Remark</th>
                                  <th>Completed On</th>
                                  <th>Action</th>
                                  </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1 ?>
                                  @foreach( $data['task']['completed'] as $task)
                                <tr class="{{ \App\Http\Controllers\TaskModuleController::getClasses($task) }} completed" id="task_{{ $task['id'] }}">
                                    <td>{{ $task['id'] }}</td>
                                    <td>{{ Carbon\Carbon::parse($task['created_at'])->format('d-m H:i') }}</td>
                                    <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
                                    <td class="task-subject" data-subject="{{$task['task_subject'] ? $task['task_subject'] : 'Task Details'}}" data-details="{{$task['task_details']}}" data-switch="0">{{ $task['task_subject'] ? $task['task_subject'] : 'Task Details' }}</td>
                                    <td> {{ Carbon\Carbon::parse($task['completion_date'])->format('d-m H:i') }}</td>
                                    <td>{{$users[$task['assign_from']]}}</td>
                                    <td>
                                      {{-- {{ $task['assign_to'] ?? ($users[$task['assign_to']] ? $users[$task['assign_to']] : 'Nil') }} --}}
                                      @php
                                        $special_task = \App\Task::find($task['id']);
                                      @endphp

                                      @foreach ($special_task->users as $key => $user)
                                        @if ($key != 0)
                                          ,
                                        @endif

                                        @if (array_key_exists($user->id, $users))
                                          @if ($user->id == Auth::id())
                                            <a href="{{ route('users.show', $user->id) }}">{{ $users[$user->id] }}</a>
                                          @else
                                            {{ $users[$user->id] }}
                                          @endif
                                        @else
                                          User Does Not Exist
                                        @endif
                                      @endforeach

                                      <br>

                                      @foreach ($special_task->contacts as $key => $contact)
                                        @if ($key != 0)
                                          ,
                                        @endif

                                        {{ $contact->name }} - {{ $contact->phone }} ({{ ucwords($contact->category) }})
                                      @endforeach
                                    </td>
                                    <td> @include('task-module.partials.remark',$task) </td>
                                    <td>{{ Carbon\Carbon::parse($task['is_completed'])->format('d-m H:i') }}</td>
                                    <td>
                                      <form action="{{ route('task.archive', $task['id']) }}" method="POST">
                                        @csrf

                                        <button type="submit" class="btn-link text-danger">Archive</button>
                                      </form>
                                    </td>
                                </tr>
                               @endforeach
                                </tbody>
                              </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="unassigned-tab">
                      <div class="row">
                        <div class="col-xs-12 col-md-4 my-3">
                          <div class="border">
                            <form action="{{ route('task.assign.messages') }}" method="POST">
                              @csrf

                              <input type="hidden" name="selected_messages" id="selected_messages" value="">

                              <div class="form-group">
                                <select class="selectpicker form-control input-sm" data-live-search="true" data-size="15" name="task_id" title="Choose a Task" required>
                                  @foreach ($data['task']['pending'] as $task)
                                    <option data-tokens="{{ $task->id }} {{ $task->task_subject }} {{ $task->task_details }} {{ array_key_exists($task->assign_from, $users) ? $users[$task->assign_from] : '' }} {{ array_key_exists($task->assign_to, $users) ? $users[$task->assign_to] : '' }}" value="{{ $task->id }}">{{ $task->id }} from {{ $users[$task->assign_from] }} {{ $task->task_subject }}</option>
                                  @endforeach
                                </select>
                              </div>

                              <div class="form-group">
                                <button type="submit" class="btn btn-xs btn-secondary" id="assignMessagesButton">Assign</button>
                              </div>
                            </form>

                          </div>
                        </div>

                        <div class="col-xs-12 col-md-8">
                          <div class="border">

                            <div class="row">
                              <div class="col-12 my-3" id="message-wrapper">
                                <div id="message-container"></div>
                              </div>

                              <div class="col-xs-12 text-center hidden">
                                <button type="button" id="load-more-messages" data-nextpage="1" class="btn btn-secondary">Load More</button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- Completed task div end -->
                </div>
            </div>

           <!-- <div class="row">
                <h4>List Of Deleted Tasks</h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Sr No</th>
                        <th>Date</th>
                        <th class="category">Category</th>
                        <th>Task Details</th>
                        <th>Comment</th>
                        {{--<th>Est Completion Date</th>--}}
                        <th>Deleted On</th>
                    </tr>
                    </thead>
                    <tbody>
			        <?php $i = 1 ?>
                    @foreach( $data['task']['deleted'] as $task)
                        <tr>
                            <td>{{$i++}}</td>
                            <td>{{$task['created_at']}}</td>
                            <td> {{ isset( $categories[$task['category']] ) ? $categories[$task['category']] : '' }}</td>
                            <td> {{$task['task_details']}}</td>
                            <td> {{$task['remark']}}</td>
                            {{--<td> {{$task['completion_date']  }}</td>--}}
                            <td> {{$task['deleted_at']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div> -->
        </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
    <script>

    var cached_suggestions = localStorage['message_suggestions'];
    var suggestions = [];

    $(document).ready(function() {
      var hash = window.location.hash.substr(1);

      if (hash == '3') {
        $('a[href="#3"]').click();
      }
    });

      $(document).on('click', '.task-subject', function() {
        if ($(this).data('switch') == 0) {
          $(this).text($(this).data('details'));
          $(this).data('switch', 1);
        } else {
          $(this).text($(this).data('subject'));
          $(this).data('switch', 0);
        }
      });

        function addNewRemark(id){

          var formData = $("#add-new-remark").find('#add-remark').serialize();
          // console.log(id);
          var remark = $('#remark-text_'+id).val();
          $.ajax({
              type: 'POST',
              headers: {
                  'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
              },
              url: '{{ route('task.addRemark') }}',
              data: {id:id,remark:remark,module_type: "task"},
          }).done(response => {
              alert('Remark Added Success!')
              // $('#add-new-remark').modal('hide');
              // $("#add-new-remark").hide();
              window.location.reload();
          });
        }

        $('#completion-datetime').datetimepicker({
          format: 'YYYY-MM-DD HH:mm'
        });

        $('#daily_activity_date').datetimepicker({
          format: 'YYYY-MM-DD'
        });

        let users = {!! json_encode( $data['users'] ) !!};

        let isAdmin = {{ $isAdmin ? 1 : 0}};

        let table = new Tabulator("#daily_activity", {
            height: "311px",
            layout: "fitColumns",
            resizableRows: true,
            columns: [
                {
                    title: "Time",
                    field: "time_slot",
                    editor: "select",
                    editorParams: {
                        '12:00am - 01:00am': '12:00am - 01:00am',
                        '01:00am - 02:00am': '01:00am - 02:00am',
                        '02:00am - 03:00am': '02:00am - 03:00am',
                        '03:00am - 04:00am': '03:00am - 04:00am',
                        '04:00am - 05:00am': '04:00am - 05:00am',
                        '05:00am - 06:00am': '05:00am - 06:00am',
                        '06:00am - 07:00am': '06:00am - 07:00am',
                        '07:00am - 08:00am': '07:00am - 08:00am',

                        '08:00am - 09:00am': '08:00am - 09:00am',
                        '09:00am - 10:00am': '09:00am - 10:00am',
                        '10:00am - 11:00am': '10:00am - 11:00am',
                        '11:00am - 12:00pm': '11:00am - 12:00pm',
                        '12:00pm - 01:00pm': '12:00pm - 01:00pm',
                        '01:00pm - 02:00pm': '01:00pm - 02:00pm',
                        '02:00pm - 03:00pm': '02:00pm - 03:00pm',
                        '03:00pm - 04:00pm': '03:00pm - 04:00pm',
                        '04:00pm - 05:00pm': '04:00pm - 05:00pm',
                        '05:00pm - 06:00pm': '05:00pm - 06:00pm',
                        '06:00pm - 07:00pm': '06:00pm - 07:00pm',
                        '07:00pm - 08:00pm': '07:00pm - 08:00pm',

                        '08:00pm - 09:00pm': '08:00pm - 09:00pm',
                        '09:00pm - 10:00pm': '09:00pm - 10:00pm',
                        '10:00pm - 11:00pm': '10:00pm - 11:00pm',
                        '11:00pm - 12:00am': '11:00pm - 12:00am',
                    },
                    editable: !isAdmin
                },
                {title: "Activity", field: "activity", editor: "textarea", formatter:"textarea", editable: !isAdmin},
                {title: "Assessment", field: "assist_msg", editor: "input", editable: !!isAdmin, visible: !!isAdmin},
                {title: "id", field: "id", visible: false},
                {title: "user_id", field: "user_id", visible: false},
            ],
        });

        $("#add-row").click(function () {
            table.addRow({});
        });

        $(".add-task").click(function () {
            var taskId = $(this).attr('data-id');
            $("#add-new-remark").find('input[name="id"]').val(taskId);
        });

        $(".view-remark").click(function () {

          var taskId = $(this).attr('data-id');

            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.gettaskremark') }}',
                data: {id:taskId,module_type:"task"},
            }).done(response => {
                console.log(response);

                var html='';

                $.each(response, function( index, value ) {

                  html+=' <p> '+value.remark+' <br> <small>By ' + value.user_name + ' updated on '+ moment(value.created_at).format('DD-M H:mm') +' </small></p>';
                  html+"<hr>";
                });
                $("#view-remark-list").find('#remark-list').html(html);
                // getActivity();
                //
                // $('#loading_activty').hide();
            });
        });

        $("#save-activity").click(function () {

            $('#loading_activty').show();
            console.log(table.getData());

            let data = [];

            if (isAdmin) {
                data = deleteKeyFromObjectArray(table.getData(), ['time_slot', 'activity']);
            }
            else {
                data = deleteKeyFromObjectArray(table.getData(), ['assist_msg']);
            }

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('dailyActivity.store') }}',
                data: {
                    activity_table_data: encodeURI(JSON.stringify(data)),
                },
            }).done(response => {
                console.log(response);
                getActivity();

                $('#loading_activty').hide();
            });
        });

        function deleteKeyFromObjectArray(data, key) {

            let newData = [];

            for (let item of data) {

                for (let eachKey of key)
                    delete  item[eachKey];

                newData = [...newData, item];
            }

            return newData;
        }

        function getActivity() {
            $.ajax({
                type: 'GET',
                data :{
                    selected_user : '{{ $selected_user }}',
                    daily_activity_date: "{{ $data['daily_activity_date'] }}",
                },
                url: '{{ route('dailyActivity.get') }}',
            }).done(response => {
                table.setData(response);
                setTimeout(getActivity, interval_daily_activtiy);
            });
        }

        getActivity();
        let interval_daily_activtiy = 1000*600;  // 1000 = 1 second
        setTimeout(getActivity, interval_daily_activtiy);


        $(document).ready(function() {
            $(document).on('change', '.is_statutory', function () {


                if ($(".is_statutory").val() == 1) {

                    $('input[name="completion_date"]').val("1976-01-01");
                    $("#completion_date").hide();

                    if (!isAdmin)
                        $('select[name="assign_to"]').html(`<option value="${current_userid}">${ current_username }</option>`);

                    $('#recurring-task').show();
                }
                else {

                    $("#completion_date").show();

                    let select_html = '';
                    for (user of users)
                        select_html += `<option value="${user['id']}">${ user['name'] }</option>`;
                    $('select[name="assign_to"]').html(select_html);

                    $('#recurring-task').hide();

                }

            });

            jQuery('#userList').select2(

                {
                    placeholder : 'All user'
                }
            );

            let r_s = '';
            let r_e = '{{ date('y-m-d') }}';

            let start = r_s ? moment(r_s,'YYYY-MM-DD') : moment().subtract(6, 'days');
            let end =   r_e ? moment(r_e,'YYYY-MM-DD') : moment();

            jQuery('input[name="range_start"]').val(start.format('YYYY-MM-DD'));
            jQuery('input[name="range_end"]').val(end.format('YYYY-MM-DD'));

            function cb(start, end) {
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

            $('#reportrange').on('apply.daterangepicker', function(ev, picker) {

                jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
                jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));

            });

            $(".table").tablesorter();
        });

        $(document).on('click', '.send-message', function() {
          var thiss = $(this);
          var data = new FormData();
          var task_id = $(this).data('taskid');
          var message = $(this).siblings('input').val();

          data.append("task_id", task_id);
          data.append("message", message);
          data.append("status", 1);

          if (message.length > 0) {
            if (!$(thiss).is(':disabled')) {
              $.ajax({
                url: '/whatsapp/sendMessage/task',
                type: 'POST',
               "dataType"    : 'json',           // what to expect back from the PHP script, if anything
               "cache"       : false,
               "contentType" : false,
               "processData" : false,
               "data": data,
               beforeSend: function() {
                 $(thiss).attr('disabled', true);
               }
             }).done( function(response) {
                $(thiss).siblings('input').val('');

                if (cached_suggestions) {
                  suggestions = JSON.parse(cached_suggestions);

                  if (suggestions.length == 10) {
                    suggestions.push(message);
                    suggestions.splice(0, 1);
                  } else {
                    suggestions.push(message);
                  }
                  localStorage['message_suggestions'] = JSON.stringify(suggestions);
                  cached_suggestions = localStorage['message_suggestions'];

                  console.log('EXISTING');
                  console.log(suggestions);
                } else {
                  suggestions.push(message);
                  localStorage['message_suggestions'] = JSON.stringify(suggestions);
                  cached_suggestions = localStorage['message_suggestions'];

                  console.log('NOT');
                  console.log(suggestions);
                }

                // $.post( "/whatsapp/approve/customer", { messageId: response.message.id })
                //   .done(function( data ) {
                //
                //   }).fail(function(response) {
                //     console.log(response);
                //     alert(response.responseJSON.message);
                //   });

                $(thiss).attr('disabled', false);
              }).fail(function(errObj) {
                $(thiss).attr('disabled', false);

                alert("Could not send message");
                console.log(errObj);
              });
            }
          } else {
            alert('Please enter a message first');
          }
        });

        $(document).on('click', '.make-private-task', function() {
          var task_id = $(this).data('taskid');
          var thiss = $(this);

          $.ajax({
            type: "POST",
            url: "{{ url('task') }}/" + task_id + "/makePrivate",
            data: {
              _token: "{{ csrf_token() }}",
            },
            beforeSend: function() {
              $(thiss).text('Changing...');
            }
          }).done(function(response) {
            if (response.task.is_private == 1) {
              $(thiss).html('<img src="/images/private.png" />');
            } else {
              $(thiss).html('<img src="/images/not-private.png" />');
            }
          }).fail(function(response) {
            $(thiss).html('<img src="/images/not-private.png" />');

            console.log(response);

            alert('Could not make task private');
          });
        });

        $(document).on('click', ".collapsible-message", function() {
          var selection = window.getSelection();
          if (selection.toString().length === 0) {
            var short_message = $(this).data('messageshort');
            var message = $(this).data('message');
            var status = $(this).data('expanded');

            if (status == false) {
              $(this).addClass('expanded');
              $(this).html(message);
              $(this).data('expanded', true);
              // $(this).siblings('.thumbnail-wrapper').remove();
              $(this).closest('.talktext').find('.message-img').removeClass('thumbnail-200');
              $(this).closest('.talktext').find('.message-img').parent().css('width', 'auto');
            } else {
              $(this).removeClass('expanded');
              $(this).html(short_message);
              $(this).data('expanded', false);
              $(this).closest('.talktext').find('.message-img').addClass('thumbnail-200');
              $(this).closest('.talktext').find('.message-img').parent().css('width', '200px');
            }
          }
        });

            $(document).ready(function() {
            var container = $("div#message-container");
            var suggestion_container = $("div#suggestion-container");
            // var sendBtn = $("#waMessageSend");
            var erpUser = "{{ Auth::id() }}";
                 var addElapse = false;
                 function errorHandler(error) {
                     console.error("error occured: " , error);
                 }
                 function approveMessage(element, message) {
                   if (!$(element).attr('disabled')) {
                     $.ajax({
                       type: "POST",
                       url: "/whatsapp/approve/user",
                       data: {
                         _token: "{{ csrf_token() }}",
                         messageId: message.id
                       },
                       beforeSend: function() {
                         $(element).attr('disabled', true);
                         $(element).text('Approving...');
                       }
                     }).done(function( data ) {
                       element.remove();
                       console.log(data);
                     }).fail(function(response) {
                       $(element).attr('disabled', false);
                       $(element).text('Approve');

                       console.log(response);
                       alert(response.responseJSON.message);
                     });
                   }
                 }

            function renderMessage(message, tobottom = null) {
                var domId = "waMessage_" + message.id;
                var current = $("#" + domId);
                var is_admin = "{{ Auth::user()->hasRole('Admin') }}";
                var is_hod_crm = "{{ Auth::user()->hasRole('HOD of CRM') }}";
                var users_array = {!! json_encode($users) !!};
                var leads_assigned_user = "";

                if ( current.get( 0 ) ) {
                  return false;
                }

               // CHAT MESSAGES
               var row = $("<div class='talk-bubble'></div>");
               var body = $("<span id='message_body_" + message.id + "'></span>");
               var text = $("<div class='talktext'></div>");
               var edit_field = $('<textarea name="message_body" rows="8" class="form-control" id="edit-message-textarea' + message.id + '" style="display: none;">' + message.message + '</textarea>');
               var p = $("<p class='collapsible-message'></p>");

               var forward = $('<button class="btn btn-image forward-btn" data-toggle="modal" data-target="#forwardModal" data-id="' + message.id + '"><img src="/images/forward.png" /></button>');

               if (message.status == 0 || message.status == 5 || message.status == 6) {
                 var meta = $("<em>" + users_array[message.user_id] + " " + moment(message.created_at).format('DD-MM H:mm') + " </em>");
                 var mark_read = $("<a href data-url='/whatsapp/updatestatus?status=5&id=" + message.id + "' style='font-size: 9px' class='change_message_status'>Mark as Read </a><span> | </span>");
                 var mark_replied = $('<a href data-url="/whatsapp/updatestatus?status=6&id=' + message.id + '" style="font-size: 9px" class="change_message_status">Mark as Replied </a>');

                 // row.attr("id", domId);
                 p.appendTo(text);

                 // $(images).appendTo(text);
                 meta.appendTo(text);

                 if (message.status == 0) {
                   mark_read.appendTo(meta);
                 }

                 if (message.status == 0 || message.status == 5) {
                   mark_replied.appendTo(meta);
                 }

                 text.appendTo(row);

                 if (tobottom) {
                   row.appendTo(container);
                 } else {
                   row.prependTo(container);
                 }

                 forward.appendTo(meta);

               } else if (message.status == 4) {
                 var row = $("<div class='talk-bubble' data-messageid='" + message.id + "'></div>");
                 var chat_friend =  (message.assigned_to != 0 && message.assigned_to != leads_assigned_user && message.user_id != message.assigned_to) ? ' - ' + users_array[message.assigned_to] : '';
                 var meta = $("<em>" + users_array[message.user_id] + " " + chat_friend + " " + moment(message.created_at).format('DD-MM H:mm') + " <img id='status_img_" + message.id + "' src='/images/1.png' /> &nbsp;</em>");

                 // row.attr("id", domId);

                 p.appendTo(text);
                 $(images).appendTo(text);
                 meta.appendTo(text);

                 text.appendTo(row);
                 if (tobottom) {
                   row.appendTo(container);
                 } else {
                   row.prependTo(container);
                 }
               } else {
                 if (message.sent == 0) {
                   var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message.user_id] : "Unknown") + " " + moment(message.created_at).format('DD-MM H:mm') + " </em>";
                 } else {
                   var meta_content = "<em>" + (parseInt(message.user_id) !== 0 ? users_array[message.user_id] : "Unknown") + " " + moment(message.created_at).format('DD-MM H:mm') + " <img id='status_img_" + message.id + "' src='/images/1.png' /></em>";
                 }

                 var error_flag = '';
                 if (message.error_status == 1) {
                   error_flag = "<a href='#' class='btn btn-image fix-message-error' data-id='" + message.id + "'><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend</a>";
                 } else if (message.error_status == 2) {
                   error_flag = "<a href='#' class='btn btn-image fix-message-error' data-id='" + message.id + "'><img src='/images/flagged.png' /><img src='/images/flagged.png' /></a><a href='#' class='btn btn-xs btn-secondary ml-1 resend-message' data-id='" + message.id + "'>Resend</a>";
                 }



                 var meta = $(meta_content);

                 edit_field.appendTo(text);

                 if (!message.approved) {
                     var approveBtn = $("<button class='btn btn-xs btn-secondary btn-approve ml-3'>Approve</button>");
                     var editBtn = ' <a href="#" style="font-size: 9px" class="edit-message whatsapp-message ml-2" data-messageid="' + message.id + '">Edit</a>';
                     approveBtn.click(function() {
                         approveMessage( this, message );
                     } );
                     if (is_admin || is_hod_crm) {
                       approveBtn.appendTo( meta );
                       $(editBtn).appendTo( meta );
                     }
                 }

                 forward.appendTo(meta);

                 $(error_flag).appendTo(meta);
               }

               row.attr("id", domId);

               p.attr("data-messageshort", message.message);
               p.attr("data-message", message.message);
               p.attr("data-expanded", "true");
               p.attr("data-messageid", message.id);
               // console.log("renderMessage message is ", message);
               if (message.message) {
                 p.html(message.message);
               } else if (message.media_url) {
                   var splitted = message.content_type.split("/");
                   if (splitted[0]==="image" || splitted[0] === 'm') {
                       var a = $("<a></a>");
                       a.attr("target", "_blank");
                       a.attr("href", message.media_url);
                       var img = $("<img></img>");
                       img.attr("src", message.media_url);
                       img.attr("width", "100");
                       img.attr("height", "100");
                       img.appendTo( a );
                       a.appendTo( p );
                       // console.log("rendered image message ", a);
                   } else if (splitted[0]==="video") {
                       $("<a target='_blank' href='" + message.media_url+"'>"+ message.media_url + "</a>").appendTo(p);
                   }
               }

               var has_product_image = false;

               if (message.images) {
                 var images = '';
                 message.images.forEach(function (image) {
                   images += image.product_id !== '' ? '<a href="/products/' + image.product_id + '" data-toggle="tooltip" data-html="true" data-placement="top" title="<strong>Special Price: </strong>' + image.special_price + '<br><strong>Size: </strong>' + image.size + '<br><strong>Supplier: </strong>' + image.supplier_initials + '">' : '';
                   images += '<div class="thumbnail-wrapper"><img src="' + image.image + '" class="message-img thumbnail-200" /><span class="thumbnail-delete whatsapp-image" data-image="' + image.key + '">x</span></div>';
                   images += image.product_id !== '' ? '<input type="checkbox" name="product" style="width: 20px; height: 20px;" class="d-block mx-auto select-product-image" data-id="' + image.product_id + '" /></a>' : '';

                   if (image.product_id !== '') {
                     has_product_image = true;
                   }
                 });

                 images += '<br>';

                 if (has_product_image) {
                   var show_images_wrapper = $('<div class="show-images-wrapper hidden"></div>');
                   var show_images_button = $('<button type="button" class="btn btn-xs btn-secondary show-images-button">Show Images</button>');

                   $(images).appendTo(show_images_wrapper);
                   $(show_images_wrapper).appendTo(text);
                   $(show_images_button).appendTo(text);
                 } else {
                   $(images).appendTo(text);
                 }

               }

               p.appendTo(body);
               body.appendTo(text);
               meta.appendTo(text);

               var select_box = $('<input type="checkbox" name="selected_message" class="select-message" data-id="' + message.id + '" />');

               select_box.appendTo(meta);

               if (has_product_image) {
                 var create_lead = $('<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-lead">+ Lead</a>');
                 var create_order = $('<a href="#" class="btn btn-xs btn-secondary ml-1 create-product-order">+ Order</a>');

                 create_lead.appendTo(meta);
                 create_order.appendTo(meta);
               }

               text.appendTo( row );

               if (message.status == 7) {
                 if (tobottom) {
                   row.appendTo(suggestion_container);
                 } else {
                   row.prependTo(suggestion_container);
                 }
               } else {
                 if (tobottom) {
                   row.appendTo(container);
                 } else {
                   row.prependTo(container);
                 }
               }


               return true;
            }

            function pollMessages(page = null, tobottom = null, addElapse = null) {
                     var qs = "";
                     qs += "?erpUser=" + erpUser;
                     if (page) {
                       qs += "&page=" + page;
                     }
                     if (addElapse) {
                         qs += "&elapse=3600";
                     }
                     var anyNewMessages = false;

                     return new Promise(function(resolve, reject) {
                         $.getJSON("/whatsapp/pollMessagesCustomer" + qs, function( data ) {

                             data.data.forEach(function( message ) {
                                 var rendered = renderMessage( message, tobottom );
                                 if ( !anyNewMessages && rendered ) {
                                     anyNewMessages = true;
                                 }
                             } );

                             if (page) {
                               $('#load-more-messages').text('Load More');
                               can_load_more = true;
                             }

                             if ( anyNewMessages ) {
                                 // scrollChatTop();
                                 anyNewMessages = false;
                             }
                             if (!addElapse) {
                                 addElapse = true; // load less messages now
                             }


                             resolve();
                         });

                     });
            }

            function startPolling() {
              setTimeout( function() {
                         pollMessages(null, null, addElapse).then(function() {
                             startPolling();
                         }, errorHandler);
                     }, 1000);
            }

            startPolling();

            var can_load_more = true;

            $('#message-wrapper').scroll(function() {
              var top = $('#message-wrapper').scrollTop();
              var document_height = $(document).height();
              var window_height = $('#message-container').height();

              console.log($('#message-wrapper').scrollTop());
              console.log($(document).height());
              console.log($('#message-container').height());

              // if (top >= (document_height - window_height - 200)) {
              if (top >= (window_height - 1500)) {
                console.log('should load', can_load_more);
                if (can_load_more) {
                  var current_page = $('#load-more-messages').data('nextpage');
                  $('#load-more-messages').data('nextpage', current_page + 1);
                  var next_page = $('#load-more-messages').data('nextpage');
                  console.log(next_page);
                  $('#load-more-messages').text('Loading...');

                  can_load_more = false;

                  pollMessages(next_page, true);
                }
              }
            });

            $(document).on('click', '#load-more-messages', function() {
              var current_page = $(this).data('nextpage');
              $(this).data('nextpage', current_page + 1);
              var next_page = $(this).data('nextpage');
              $('#load-more-messages').text('Loading...');

              pollMessages(next_page, true);
            });

          });

          var selected_messages = [];
          $(document).on('click', '.select-message', function() {
            var message_id = $(this).data('id');

            if ($(this).prop('checked')) {
              selected_messages.push(message_id);
            } else {
              var index = selected_messages.indexOf(message_id);

              selected_messages.splice(index, 1);
            }

            console.log(selected_messages);
          });

          $('#assignMessagesButton').on('click', function(e) {
            e.preventDefault();

            if (selected_messages.length > 0) {
              $('#selected_messages').val(JSON.stringify(selected_messages));

              if ($(this).closest('form')[0].checkValidity()) {
                $(this).closest('form').submit();
              } else {
                $(this).closest('form')[0].reportValidity();
              }
            } else {
              alert('Please select atleast 1 message');
            }
          });

    </script>

@endsection
