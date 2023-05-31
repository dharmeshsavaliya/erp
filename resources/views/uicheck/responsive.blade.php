@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Ui Check')

@section('styles')
<link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<style type="text/css">
	.preview-category input.form-control {
		width: auto;
	}

	#loading-image {
		position: fixed;
		top: 50%;
		left: 50%;
		margin: -50px 0px 0px -50px;
	}

	.dis-none {
		display: none;
	}

	.pd-5 {
		padding: 3px;
	}

	.toggle.btn {
		min-height: 25px;
	}

	.toggle-group .btn {
		padding: 2px 12px;
	}

	.latest-remarks-list-view tr td {
		padding: 3px !important;
	}

	#latest-remarks-modal .modal-dialog {
		max-width: 1100px;
		width: 100%;
	}

	.btn-secondary {
		border: 1px solid #ddd;
		/* color: #757575; */
		/* background-color: #fff !important; */
	}

	.modal {
		overflow-y: auto;
	}

	body.overflow-hidden {
		overflow: hidden;
	}

	span.user_point_none button,
	span.admin_point_none button {
		pointer-events: none;
		cursor: not-allowed;
	}

	table tr:last-child td {
		border-bottom: 1px solid #ddd !important;
	}

	select.globalSelect2+span.select2 {
		width: calc(100% - 26px) !important;
	}
	#uicheck_table1 td .div-message-language img, #uicheck_table1 td .view-uploaded-files-button img{
		width: 12px!important;
	}
	#uicheck_table1 td .upload-ui-responsive-button, #uicheck_table1 td .devHistorty{
		font-size: 14px!important;
	}
</style>
@endsection

@section('large_content')

<div id="myDiv">
	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">Ui Check Responsive({{$uiDevDatas->total()}})</h2>
	</div>

</div>

@if (Session::has('message'))
{{ Session::get('message') }}
@endif
<br />
<div class="col-lg-12 margin-tb">
	<div class="row">
		<div class="col-md-9">
			<form>
				<div class="row">
					<div class="col-md-2">
						<div class="form-group">
							<input type="text" name="id" id="id" class="form-control" value="{{request('id')}}" placeholder="Please Enter Uicheck Id" />
						</div>
					</div>
					
					<div class="col-md-2">
						<div class="form-group">
							<?php 
								if(request('categories')){   $categoriesArr = request('categories'); }
								else{ $categoriesArr = ''; }
							  ?>
							<select name="categories" id="store-categories" class="form-control select2">
								<option value="" @if($categoriesArr=='') selected @endif>-- Select a categories --</option>
								@forelse($site_development_categories as $ctId => $ctName)
								<option value="{{ $ctId }}" @if($categoriesArr==$ctId) selected @endif>{!! $ctName !!}</option>
								@empty
								@endforelse
							</select>
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							{{Form::select('type', [''=>'Select a type']+$allUicheckTypes, request('type') ?? '', array('class'=>'form-control select2'))}}
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group">
							<?php 
								if(request('user_name')){   $userNameArr = request('user_name'); }
								else{ $userNameArr = []; }
							?>
							<select name="user_name[]" id="user_name" class="form-control select2" multiple>
								<option value="" @if($userNameArr=='') selected @endif>-- Select a User --</option>
								@forelse($allUsers as $uId => $uName)
								<option value="{{ $uName->id }}" @if(in_array($uName->id, $userNameArr)) selected @endif>{!! $uName->name !!}</option>
								@empty
								@endforelse
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<?php 
								if(request('status')){   $statusArr = request('status'); }
								else{ $statusArr = ''; }
							  ?>
							<select name="status" id="status" class="form-control select2">
								<option value="" @if($statusArr=='') selected @endif>-- Status --</option>
								@forelse($allStatus as $key => $as)
								<option value="{{ $key }}" @if($statusArr==$key) selected @endif>{{ $as }}</option>
								@empty
								@endforelse
							</select>
						</div>
					</div>
					<div class="col-md-2">
						<button type="submit" class="btn btn btn-image custom-filter"><img src="/images/filter.png" style="cursor: nwse-resize;"></button>
						<a href="{{route('uicheck.responsive')}}" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
					</div>
				</div>
			</form>
		</div>
		<div class="col-md-3">
				<a href="/uicheck/device-logs" class="btn btn-secondary my-3"> UI Check Logs</a>&nbsp;
				@if (Auth::user()->isAdmin())
				@php
					if(request('website') && request('website') != '' && request('user') && request('user') != ''){
						echo '<i class="btn btn-s fa fa-plus addUsers" title="Add user to records" data-toggle="modal" data-target="#addUsers"></i>';
					}
				@endphp
				<button class="btn btn-secondary my-3"  data-toggle="modal" data-target="#uiResponsive"> UI Responsive</button>&nbsp;
				<button class="btn btn-secondary my-3" data-toggle="modal" data-target="#newStatusColor"> Status Color</button>&nbsp;
			@endif
		</div>
	</div>
</div>
<div class="row mt-2">
	<div class="col-md-12 margin-tb infinite-scroll">
		<div class="table-responsive" style="overflow-x: auto!important">
			<table class="table table-bordered" style="width: 135%;max-width:unset" id="uicheck_table1">
				<thead>
					<tr>
						{{-- <th width="10%">ID</th> --}}
						<th >#</th>
						<th style="width: auto">Categories</th>
						<th style="width: auto">Website</th>
						{{-- <th>Upload file</th> --}}
						@if (Auth::user()->isAdmin())
							<th style="width: auto">User Name</th>
						@endif
						<th style="width: 5%">Type</th>
						<th style="width: auto">Device1</th>
						<th style="width: auto">Device2</th>
						<th style="width: auto">Device3</th>
						<th style="width: auto">Device4</th>
						<th style="width: auto">Device5</th>
						<th style="width: auto">Device6</th>
						<th style="width: auto">Device7</th>
						<th style="width: auto">Device8</th>
						<th style="width: auto">Device9</th>
						<th style="width: auto">Device10</th>
						<th style="width: 150px">Status</th>
						
					</tr>
				</thead>
				<tbody>
					@foreach ($uiDevDatas as $uiDevData)
						@php
							$deviceBgColors =  array_fill(1, 10, '#ffffff');

							if (isset($uiDevData->uichecks) && isset($uiDevData->uichecks->uiDevice)) {
								foreach ($uiDevData->uichecks->uiDevice as $device) {
									$deviceNo = $device->device_no;
									if (isset($device->lastUpdatedHistory) && $device->lastUpdatedHistory->status != ''){
										$color = $device->lastUpdatedHistory->stausColor->color;
										if ($color != '')
											$deviceBgColors[$deviceNo] = $color;
									}
								}
							}
						@endphp
							<tr>
								{{-- <td>{{$uiDevData->id}}</td> --}}
								<td>{{$uiDevData->uicheck_id}}</td>
								<td class="expand-row-msg" data-name="title" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
									<span class="show-short-title-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->title != '') {{ Str::limit($uiDevData->title, 30, '..')}} @else   @endif</span>
									<span style="word-break:break-all;" class="show-full-title-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->title != '') {{$uiDevData->title}} @else   @endif</span>
								</td>
								<td class="expand-row-msg" data-name="website" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
									<span style="word-break:break-all;" class="show-short-website-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->title != '') {{ Str::limit($uiDevData->website, 30, '..')}} @else   @endif</span>
									<span style="word-break:break-all;" class="show-full-website-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->website != '') {{$uiDevData->website}} @else   @endif</span>
								</td>
								{{-- <td>
									<button class="btn btn-sm upload-ui-responsive-button" type="button" title="Uploaded Files" data-ui_check_id="{{$uiDevData->uicheck_id}}">
										<i class="fa fa-cloud-upload" aria-hidden="true"></i>
									</button>
									<button class="btn btn-sm view-uploaded-files-button" type="button" title="View Uploaded Files" data-ui_check_id="{{$uiDevData->uicheck_id}}">
										<img src="/images/google-drive.png" style="cursor: nwse-resize; width: 12px;">
									</button>
								</td> --}}
								@if (Auth::user()->isAdmin())
									<td class="expand-row-msg" data-name="username" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
										<span class="show-short-username-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->user_accessable != '') {{ Str::limit($uiDevData->user_accessable, 30, '..')}} @else   @endif</span>
										<span style="word-break:break-all;" class="show-full-username-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->user_accessable != '') {{$uiDevData->user_accessable}} @else   @endif</span>
										<i class="btn btn-xs fa fa-info-circle devHistorty" onclick="funGetUserHistory({{$uiDevData->uicheck_id}});"></i>
									</td>
								@endif

								<td>{{$uiDevData->uicheck_type_id ? $allUicheckTypes[$uiDevData->uicheck_type_id] : ''}}</td>
							
								<td>
									<input type="text"  name="uidevmessage1{{$uiDevData->uicheck_id}}" class="uidevmessage1{{$uiDevData->uicheck_id}}" style="margin-top: 0px; width: 100% !important;background-color: {{$deviceBgColors['1']}} !important" />
									<button class="btn pr-0 btn-xs btn-image div-message-language" data-device_no="1" data-uicheck_id="{{$uiDevData->uicheck_id}}" onclick="funDevUpdate('1', '{{$uiDevData->uicheck_id}}', '1');"><img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;" /></button>
									<i class="btn btn-xs fa fa-info-circle devHistorty" onclick="funGetDevHistory('1', '{{$uiDevData->uicheck_id}}');"></i>
									<i class="btn btn-xs fa fa-clock-o toggle-event" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="1"></i>
									@include('uicheck.partials.device-google-screencast-button')
									
									<button title="Estimated Time" class="btn pr-0 btn-xs btn-image showDevice" data-device_no="1" data-uicheck_id="{{$uiDevData->uicheck_id}}"><i class="fa fa-hourglass-start" aria-hidden="true"></i></button>
								</td>
								<td>
									<input type="text"  name="uidevmessage2{{$uiDevData->uicheck_id}}" class="uidevmessage2{{$uiDevData->uicheck_id}}" style="margin-top: 0px; width: 100% !important;background-color: {{$deviceBgColors['2']}} !important" />
									<button class="btn pr-0 btn-xs btn-image div-message-language" data-device_no="2" data-uicheck_id="{{$uiDevData->uicheck_id}}" onclick="funDevUpdate('2', '{{$uiDevData->uicheck_id}}', '2');"><img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;" /></button>
									<i class="btn btn-xs fa fa-info-circle devHistorty" onclick="funGetDevHistory('2', '{{$uiDevData->uicheck_id}}');"></i>
									<i class="btn btn-xs fa fa-clock-o toggle-event" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="2"></i>
									@include('uicheck.partials.device-google-screencast-button')
									<button title="Estimated Time" class="btn pr-0 btn-xs btn-image showDevice" data-device_no="2" data-uicheck_id="{{$uiDevData->uicheck_id}}"><i class="fa fa-hourglass-start" aria-hidden="true"></i></button>
								</td>
								<td>
									<input type="text"  name="uidevmessage3{{$uiDevData->uicheck_id}}" class="uidevmessage3{{$uiDevData->uicheck_id}}" style="margin-top: 0px; width: 100% !important;background-color: {{$deviceBgColors['3']}} !important" />
									<button class="btn pr-0 btn-xs btn-image div-message-language" data-device_no="3" data-uicheck_id="{{$uiDevData->uicheck_id}}" onclick="funDevUpdate('3', '{{$uiDevData->uicheck_id}}', '3');"><img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;" /></button>
									<i class="btn btn-xs fa fa-info-circle devHistorty" onclick="funGetDevHistory('3', '{{$uiDevData->uicheck_id}}');"></i>
									<i class="btn btn-xs fa fa-clock-o toggle-event" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="3"></i>
									@include('uicheck.partials.device-google-screencast-button')
									<button title="Estimated Time" class="btn pr-0 btn-xs btn-image showDevice" data-device_no="3" data-uicheck_id="{{$uiDevData->uicheck_id}}"><i class="fa fa-hourglass-start" aria-hidden="true"></i></button>
								</td>
								<td>
									<input type="text"  name="uidevmessage4{{$uiDevData->uicheck_id}}" class="uidevmessage4{{$uiDevData->uicheck_id}}" style="margin-top: 0px; width: 100% !important;background-color: {{$deviceBgColors['4']}} !important" />
									<button class="btn pr-0 btn-xs btn-image div-message-language" data-device_no="4" data-uicheck_id="{{$uiDevData->uicheck_id}}" onclick="funDevUpdate('4', '{{$uiDevData->uicheck_id}}', '4');"><img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;" /></button>
									<i class="btn btn-xs fa fa-info-circle devHistorty" onclick="funGetDevHistory('4', '{{$uiDevData->uicheck_id}}');"></i>
									<i class="btn btn-xs fa fa-clock-o toggle-event" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="4"></i>
									@include('uicheck.partials.device-google-screencast-button')
									<button title="Estimated Time" class="btn pr-0 btn-xs btn-image showDevice" data-device_no="4" data-uicheck_id="{{$uiDevData->uicheck_id}}"><i class="fa fa-hourglass-start" aria-hidden="true"></i></button>
								</td>
								<td>
									<input type="text"  name="uidevmessage5{{$uiDevData->uicheck_id}}" class="uidevmessage5{{$uiDevData->uicheck_id}}" style="margin-top: 0px; width: 100% !important;background-color: {{$deviceBgColors['5']}} !important" />
									<button class="btn pr-0 btn-xs btn-image div-message-language" data-device_no="5" data-uicheck_id="{{$uiDevData->uicheck_id}}" onclick="funDevUpdate('5', '{{$uiDevData->uicheck_id}}', '5');"><img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;" /></button>
									<i class="btn btn-xs fa fa-info-circle devHistorty" onclick="funGetDevHistory('5', '{{$uiDevData->uicheck_id}}');"></i>
									<i class="btn btn-xs fa fa-clock-o toggle-event" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="5"></i>
									@include('uicheck.partials.device-google-screencast-button')
									<button title="Estimated Time" class="btn pr-0 btn-xs btn-image showDevice" data-device_no="5" data-uicheck_id="{{$uiDevData->uicheck_id}}"><i class="fa fa-hourglass-start" aria-hidden="true"></i></button>
								</td>
								<td>
									<input type="text"  name="uidevmessage6{{$uiDevData->uicheck_id}}" class="uidevmessage6{{$uiDevData->uicheck_id}}" style="margin-top: 0px; width: 100% !important;background-color: {{$deviceBgColors['6']}} !important" />
									<button class="btn pr-0 btn-xs btn-image div-message-language" data-device_no="6" data-uicheck_id="{{$uiDevData->uicheck_id}}" onclick="funDevUpdate('6', '{{$uiDevData->uicheck_id}}', '6');"><img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;" /></button>
									<i class="btn btn-xs fa fa-info-circle devHistorty" onclick="funGetDevHistory('6', '{{$uiDevData->uicheck_id}}');"></i>
									<i class="btn btn-xs fa fa-clock-o toggle-event" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="6"></i>
									@include('uicheck.partials.device-google-screencast-button')
									<button title="Estimated Time" class="btn pr-0 btn-xs btn-image showDevice" data-device_no="6" data-uicheck_id="{{$uiDevData->uicheck_id}}"><i class="fa fa-hourglass-start" aria-hidden="true"></i></button>
								</td>
								<td>
									<input type="text"  name="uidevmessage7{{$uiDevData->uicheck_id}}" class="uidevmessage7{{$uiDevData->uicheck_id}}" style="margin-top: 0px; width: 100% !important;background-color: {{$deviceBgColors['7']}} !important" />
									<button class="btn pr-0 btn-xs btn-image div-message-language" data-device_no="7" data-uicheck_id="{{$uiDevData->uicheck_id}}" onclick="funDevUpdate('7', '{{$uiDevData->uicheck_id}}', '7');"><img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;" /></button>
									<i class="btn btn-xs fa fa-info-circle devHistorty" onclick="funGetDevHistory('7', '{{$uiDevData->uicheck_id}}');"></i>
									<i class="btn btn-xs fa fa-clock-o toggle-event" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="7"></i>
									@include('uicheck.partials.device-google-screencast-button')
									<button title="Estimated Time" class="btn pr-0 btn-xs btn-image showDevice" data-device_no="7" data-uicheck_id="{{$uiDevData->uicheck_id}}"><i class="fa fa-hourglass-start" aria-hidden="true"></i></button>
								</td>
								<td>
									<input type="text"  name="uidevmessage8{{$uiDevData->uicheck_id}}" class="uidevmessage8{{$uiDevData->uicheck_id}}" style="margin-top: 0px; width: 100% !important;background-color: {{$deviceBgColors['8']}} !important" />
									<button class="btn pr-0 btn-xs btn-image div-message-language" data-device_no="8" data-uicheck_id="{{$uiDevData->uicheck_id}}" onclick="funDevUpdate('8', '{{$uiDevData->uicheck_id}}', '8');"><img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;" /></button>
									<i class="btn btn-xs fa fa-info-circle devHistorty" onclick="funGetDevHistory('8', '{{$uiDevData->uicheck_id}}');"></i>
									<i class="btn btn-xs fa fa-clock-o toggle-event" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="8"></i>
									@include('uicheck.partials.device-google-screencast-button')
									<button title="Estimated Time" class="btn pr-0 btn-xs btn-image showDevice" data-device_no="8" data-uicheck_id="{{$uiDevData->uicheck_id}}"><i class="fa fa-hourglass-start" aria-hidden="true"></i></button>
								</td>
								<td>
									<input type="text"  name="uidevmessage9{{$uiDevData->uicheck_id}}" class="uidevmessage9{{$uiDevData->uicheck_id}}" style="margin-top: 0px; width: 100% !important;background-color: {{$deviceBgColors['9']}} !important" />
									<button class="btn pr-0 btn-xs btn-image div-message-language" data-device_no="9" data-uicheck_id="{{$uiDevData->uicheck_id}}" onclick="funDevUpdate('9', '{{$uiDevData->uicheck_id}}', '9');"><img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;" /></button>
									<i class="btn btn-xs fa fa-info-circle devHistorty" onclick="funGetDevHistory('9', '{{$uiDevData->uicheck_id}}');"></i>
									<i class="btn btn-xs fa fa-clock-o toggle-event" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="9"></i>
									@include('uicheck.partials.device-google-screencast-button')
									<button title="Estimated Time" class="btn pr-0 btn-xs btn-image showDevice" data-device_no="9" data-uicheck_id="{{$uiDevData->uicheck_id}}"><i class="fa fa-hourglass-start" aria-hidden="true"></i></button>
								</td>
								<td>
									<input type="text"  name="uidevmessage10{{$uiDevData->uicheck_id}}" class="uidevmessage10{{$uiDevData->uicheck_id}}" style="margin-top: 0px; width: 100% !important;background-color: {{$deviceBgColors['10']}} !important" />
									<button class="btn pr-0 btn-xs btn-image div-message-language" data-device_no="10" data-uicheck_id="{{$uiDevData->uicheck_id}}" onclick="funDevUpdate('10', '{{$uiDevData->uicheck_id}}', '10');"><img src="/images/filled-sent.png" style="cursor: nwse-resize; width: 0px;" /></button>
									<i class="btn btn-xs fa fa-info-circle devHistorty" onclick="funGetDevHistory('10', '{{$uiDevData->uicheck_id}}');"></i>
									<i class="btn btn-xs fa fa-clock-o toggle-event" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="10"></i>
									@include('uicheck.partials.device-google-screencast-button')
									<button title="Estimated Time" class="btn pr-0 btn-xs btn-image showDevice" data-device_no="10" data-uicheck_id="{{$uiDevData->uicheck_id}}"><i class="fa fa-hourglass-start" aria-hidden="true"></i></button>
								</td>
								
								<?php 
										$devid = '';
										$status = '';
										$uiDev = App\UiDevice::where('device_no', 1)
											->where('uicheck_id', $uiDevData->uicheck_id)
											->first();
											$device_no = $uiDev->device_no  ?? '';
											$status = ($status) ? $status : ''; if($device_no == 1) { $status = $uiDev->status; }  
											$devid = ($devid) ? $devid : $uiDev->id ?? ''; 
											?>
								<td data-id="{{$devid }}" data-uicheck_id="{{$uiDevData->uicheck_id }}" data-device_no="1"  data-old_status="{{$status }}" >
									
									<?php echo Form::select("statuschanges",[ "" => "-- None --"] + $allStatus ,$status , ["class" => "form-control statuschanges statusVal".$uiDevData->uicheck_id, "style" => "width:80% !important;float: left;"]); ?>
									<button type="button" class="btn btn-xs btn-status-history" style="float: left;" title="Show Status History" data-id="{{$uiDevData->id}}" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="{{$uiDevData->device_no}}"  data-old_status="{{$uiDevData->status}}" ><i class="fa fa-info-circle "></i></button></td>
							</tr>
						
					@endforeach
				</tbody>
			</table>
			<div class="text-center">
				{!! $uiDevDatas->appends(Request::except('page'))->links() !!}
			  </div>
		</div>
	</div>
</div>
@if (Auth::user()->isAdmin())
<div id="uiResponsive" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4>Assign new category to User:</h4>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="from-group">
					<label for="">Select User:</label>
					<select name="users" id="assign-new-user" class="form-control select2" style="width: 100%!important">
						<option value="" selected disabled>-- Select a user --</option>
						@forelse($allUsers as $key => $user)
								<option value="{{ $user->id }}">{{ $user->name }}</option>
						@empty
						@endforelse
					</select>
				</div>
				<div class="from-group mt-3">
					<label for="">Select Website:</label>
					<select name="users" id="assign-new-website" class="form-control select2" style="width: 100%!important">
						<option value="" selected disabled>-- Select a Website --</option>
						@forelse($store_websites as $website_id => $website_name)
							<option value="{{ $website_id }}">{{ $website_name }}</option>
						@empty
						@endforelse
					</select>
				</div>
				<div class="from-group mt-3">
					<button class="btn btn-primary" id="assign_user_to_website">Assign</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="addUsers" class="modal fade" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4>Add another user to records:</h4>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="from-group">
					<input type="hidden" name="website_id" id="website_id" value="{{request('website')}}" />
					<input type="hidden" name="old_user_id" id="old_user_id" value="{{request('user')}}" />
					<label for="">Select User:</label>
					<select name="new_user_id" id="new_user_id" class="form-control select2" style="width: 100%!important">
						<option value="" selected disabled>-- Select a user --</option>
						@forelse($allUsers as $key => $user)
							<option value="{{ $user->id }}">{{ $user->name }}</option>
						@empty
						@endforelse
					</select>
				</div>
				<div class="from-group mt-3">
					<button class="btn btn-primary" id="add_user_to_website">Add</button>
				</div>
			</div>
		</div>
	</div>
</div>
@endif
<div id="userHistoryModel" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4>User history:</h4>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body">
				<div class="table-responsive">
					<table class="table">
						<thead>
							<tr>
								<th>#</th>
								<th>User name</th>
								<th>Timestamp</th>
							</tr>
						</thead>
						<tbody id="userHistoryModelContent"></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="status_history_model" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h2>Status History</h2>
				<button type="button" class="close" data-dismiss="modal">×</button>
			</div>
			<div class="modal-body" id="">
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>ID</th>
								<th>User Name</th>
								<th>Old Status</th>
								<th>Status</th>
								<th>Date</th>

							</tr>
						</thead>
						<tbody class="status_history_tboday">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
<div id="modalGetDevMessageHistory" class="modal fade" role="dialog" >
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Ui Device Message History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="5%">ID</th>
								<th width="8%">Update By</th>
								<th width="25%" style="word-break: break-all;">Message</th>
								<th width="15%" style="word-break: break-all;">Estimated Time</th>
								<th width="15%" style="word-break: break-all;">Status</th>
								<th width="15%">Created at</th>
							</tr>
						</thead>
						<tbody>
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
<div id="newStatusColor" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status Color</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="{{ route('uicheck.statuscolor') }}" method="POST">
                <?php echo csrf_field(); ?>
                {{--                <div class="modal-content">--}}
                <div class="form-group col-md-12">
                    <table cellpadding="0" cellspacing="0" border="1" class="table table-bordered">
                        <tr>
                            <td class="text-center"><b>Status Name</b></td>
                            <td class="text-center"><b>Color Code</b></td>
                            <td class="text-center"><b>Color</b></td>
                        </tr>
                        <?php
                         foreach ($siteDevelopmentStatuses as $status) { ?>
                        <tr>
                            <td>&nbsp;&nbsp;&nbsp;<?php echo $status->name; ?></td>
                            <td class="text-center"><?php echo $status->color; ?></td>
                            <td class="text-center"><input type="color" name="color_name[<?php echo $status->id; ?>]" class="form-control" data-id="<?php echo $status->id; ?>" id="color_name_<?php echo $status->id; ?>" value="<?php echo $status->color; ?>" style="height:30px;padding:0px;"></td>
                        </tr>
                        <?php }  ?>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary submit-status-color">Save changes</button>
                </div>
            </form>
        </div>

    </div>
</div>
<div id="uploadeUiResponsiveModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Upload Screencast/File to Google Drive</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<form action="{{ route('uicheck.upload-file') }}" method="POST" enctype="multipart/form-data">
				@csrf
				<input type="hidden" name="ui_check_id" id="ui_check_id">
				<input type="hidden" name="device_no" id="device_no">
				<div class="modal-body">						
					<div class="form-group">
						<strong>Upload File</strong>
						<input type="file" name="file[]" id="fileInput" class="form-control input-sm" placeholder="Upload File" style="height: fit-content;" multiple required>
						@if ($errors->has('file'))
							<div class="alert alert-danger">{{$errors->first('file')}}</div>
						@endif
					</div>
					<div class="form-group">
						<strong>File Creation Date:</strong>
						<input type="date" name="file_creation_date" value="{{ old('file_creation_date') }}" class="form-control input-sm" placeholder="Drive Date" required>
					</div>
					<div class="form-group">
							<label>Remarks:</label>
							<textarea id="remarks" name="remarks" rows="4" cols="64" value="{{ old('remarks') }}" placeholder="Remarks" required class="form-control"></textarea>

							@if ($errors->has('remarks'))
								<div class="alert alert-danger">{{$errors->first('remarks')}}</div>
							@endif
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-default">Upload</button>
				</div>
			</form>
		</div>

	</div>
</div>

<div id="displayFileUpload" class="modal fade" role="dialog">
	<div class="modal-dialog modal-xl">

		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Google Drive Uploaded files</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>

			<div class="modal-body">
				<div class="table-responsive mt-3">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Filename</th>
								<th>File Creation Date</th>
								<th>URL</th>
								<th>Remarks</th>
							</tr>
						</thead>
						<tbody id="fileUploadedData">
							
						</tbody>
					</table>
				</div>
			 </div>


		</div>

	</div>
</div>

@if (Auth::user()->hasRole('Admin'))
<input type="hidden" id="user-type" value="Admin">
@else
<input type="hidden" id="user-type" value="Not Admin">
@endif
<div id="modalCreateDevice" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Estimated Time</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body">
				<div class="col-md-12">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th width="5%">ID</th>
								<th width="15%" style="word-break: break-all;">Language</th>
								<th width="22%" style="word-break: break-all;">Message & Estimated Time[In Minutes]</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><input type="text" readonly id="uidev_id" name="uidev_id" value="" style="margin-top: 0px;width:80% !important;"></td>
								<td>Device <span id="uidev_num"></span></td>
								<td>
									<input type="hidden" name="uidev_uicheck_id" class="uidev_uicheck_id" style="margin-top: 0px;width:40% !important;" id="uidev_uicheck_id"/>
									<input type="text" name="uidev_message" class="uidev_message" style="margin-top: 0px;width:40% !important;" id="uidev_message" placeholder="Message"/>
									<input type="number" name="uidev_estimated_time" class="uidev_estimated_time" id="uidev_estimated_time" style="margin-top: 0px;width:40% !important;" placeholder="Estimated Time[In Minutes]"/>
									
									<button id="uidev_update_esttime" class="btn pr-0 btn-xs btn-image div-message-language">
										<img src="{{asset('/images/filled-sent.png')}}" style="cursor: nwse-resize; width: 0px;">
									</button>
									
								</td>
								
							</tr>
							
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
@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">

	@if($errors->any())
		@php
			$error = $errors->all()
		@endphp
		toastr["error"]("{{$error[0] ?? 'Something went wrong.'}}");
	@endif
		
	@if ($message = Session::get('success'))
		toastr["success"]("{{$message}}");
	@endif
	@if ($message = Session::get('error'))
		toastr["error"]("{{$message}}");
	@endif
	var urlUicheckGet = "{{ route('uicheck.get') }}";
	var urlUicheckHistoryDates = "{{ route('uicheck.history.dates') }}";
	var isAdmin = "{{ Auth::user()->hasRole('Admin') ? 1 : 0 }}";

	$(document).on("change", ".statuschanges", function(e) {
		e.preventDefault();
		var id = $(this).parent().data('id');
		var uicheck_id = $(this).parent().data('uicheck_id');
		var device_no = $(this).parent().data('device_no');
		var old_status = $(this).parent().data('old_status');

		var status = $(this).val();

		$.ajax({
			url: "{{route('uicheck.responsive.status')}}",
			type: 'POST',
			data: {
				id: id,
				uicheck_id: uicheck_id,
				device_no : device_no,
				old_status : old_status,
				status: status,
				"_token": "{{ csrf_token() }}",
			},
			beforeSend: function() {
				
			},
			success: function(response) {
				if (response.code == 200) {
					//$(".statuschanges").val("");
					toastr['success'](response.message);
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.message);
		});
	});	

	
	$(document).on("click", "#uidev_update_esttime", function(e) {
		var uicheckId = $("#uidev_uicheck_id").val();
		let uidevmessage = jQuery('#uidev_message').val();
		let uidevstatus = '';
		var device_no = jQuery('#uidev_id').val();
		let uidevdatetime = jQuery('#uidev_estimated_time').val();
		let mdl = jQuery('#modalCreateDevice');
		//console.log(uidevmessage);
		//console.log(uidevdatetime);
		jQuery.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: "/uicheck/set/device",
			type: 'POST',
			data: {
				device_no : device_no,
				uicheck_id : uicheckId,
				message : uidevmessage,
				uidevdatetime : uidevdatetime,
				uidevstatus : uidevstatus
			},
			beforeSend: function() {
				//jQuery("#loading-image").show();
			}
		}).done(function(response) {
			toastr["success"]("Record updated successfully!!!");
			//mdl.find('tbody').html(response.html);
			//mdl.modal("show");
			
			mdl.modal("hide");
		}).fail(function(errObj) {
			console.log(errObj);
			toastr["error"](errObj.message);
			mdl.modal("show");
		});
	});
	$(document).on("click", ".showDevice", function(e) {
		let mdl = jQuery('#modalCreateDevice');
		var uidev_id=$(this).attr('data-device_no');
		var uidev_uicheck_id=$(this).attr('data-uicheck_id');
		console.log(uidev_id);
		$("#uidev_id").val(uidev_id);
		$("#uidev_uicheck_id").val(uidev_uicheck_id);
		$("#uidev_message").val('');
		$("#uidev_estimated_time").val('');
		$("#uidev_num").html(uidev_id);
		
		mdl.modal("show");
	});
	function funDevUpdate(id, uicheckId, device_no) {
		//siteLoader(true);
		//let mdl = jQuery('#modalCreateDevice');
		var uicheckId = uicheckId;
		let uidevmessage = jQuery('.uidevmessage'+id+uicheckId).val();
		let uidevstatus = jQuery('.statusVal'+uicheckId).val();
		var device_no = device_no;
		let uidevdatetime = jQuery('.uidevestimated_time'+id).val();
		jQuery.ajax({
			
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: "/uicheck/set/device",
			type: 'POST',
			data: {
				device_no : id,
				uicheck_id : uicheckId,
				message : uidevmessage,
				uidevdatetime : uidevdatetime,
				uidevstatus : uidevstatus
			},
			beforeSend: function() {
				//jQuery("#loading-image").show();
			}
		}).done(function(response) {
			toastr["success"]("Record updated successfully!!!");
			//mdl.find('tbody').html(response.html);
			//mdl.modal("show");
		}).fail(function(errObj) {
			console.log(errObj);
			toastr["error"](errObj.message);
		});
	}

	function funGetDevHistory(id,uicheckId) {
		//siteLoader(true);
		let mdl = jQuery('#modalGetDevMessageHistory');
		var uicheckId = uicheckId;
		
		jQuery.ajax({
			headers: {
				'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
			},
			url: "/uicheck/get/message/history/dev",
			type: 'POST',
			data: {
				device_no: id,
				uicheck_id : uicheckId,
			},
			beforeSend: function() {
				//jQuery("#loading-image").show();
			}
		}).done(function(response) {
			//siteLoader(false);
			//siteSuccessAlert("Listed successfully!!!");
			$("#modalCreateLanguage").modal("hide");
			mdl.find('tbody').html(response.html);
			mdl.modal("show");
		}).fail(function (jqXHR, ajaxOptions, thrownError) {      
			toastr["error"](jqXHR.responseJSON.message);
			$("#loading-image").hide();
		});
	}

	$(document).on("click",".btn-status-history",function(e) {
        e.preventDefault();
        var $this = $(this);
        var id = $(this).parent().data('id');
		var device_no = $(this).parent().data('device_no');
		
        $.ajax({
          	url: '/uicheck/get/responsive/status/history',
          	type: 'POST',
        	headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
			data: {
				id: id,
				device_no : device_no,
			},
			dataType:"json",
          	beforeSend: function() {
            	$("#loading-image").show();
			}
		}).done(function (response) {
          $("#loading-image").hide();
          var html = "";
			if(response.code == 200){
				
				$.each(response.data,function(k,v){
					html += "<tr>";
					html += "<td>"+v.id+"</td>";
					html += "<td>"+v.username+"</td>";
					html += "<td><div class='form-row'>"+v.oldstatusname+"</div></td>";
					html += "<td><div class='form-row'>"+v.statusname+"</div></td>";
					html += "<td><div class='form-row'>"+v.created_at+"</div></td>";
					html += "</tr>";
				});
				$(".status_history_tboday").html(html);
				$("#status_history_model").modal("show");
			} else {
				toastr["error"](response.message);	
			}
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
			console.log(jqXHR);
			toastr["error"](jqXHR.responseJSON.message);
          $("#loading-image").hide();
        });
      });

	  $(document).on("click",".link-delete-document",function(e) {
        e.preventDefault();
        var id = $(this).data("id");
        var $this = $(this);
        if(confirm("Are you sure you want to delete records ?")) {
          $.ajax({
            url: '/uicheck/delete/attachment',
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
              dataType:"json",
            data: { id : id},
            beforeSend: function() {
              $("#loading-image").show();
                  }
          }).done(function (data) {
            $("#loading-image").hide();
            toastr["success"]("Document deleted successfully");
            $this.closest("tr").remove();
          }).fail(function (jqXHR, ajaxOptions, thrownError) {
            toastr["error"](jqXHR.responseJSON.message);
            $("#loading-image").hide();
          });
        }
      });

	jQuery(document).ready(function() {
		applyDateTimePicker(jQuery('.cls-start-due-date'));


		$(document).on("click", ".upload-ui-responsive-button", function (e) {
			e.preventDefault();
			let button = $(this).closest('td').find('.div-message-language');
			let ui_check_id = $(button).data("uicheck_id");
			let device_no = $(button).data("device_no");
			$("#uploadeUiResponsiveModal #ui_check_id").val(ui_check_id || 0);
			$("#uploadeUiResponsiveModal #device_no").val(device_no || 0);
			$("#uploadeUiResponsiveModal").modal("show")
		});

		$(document).on("click", ".view-uploaded-files-button", function (e) {
			e.preventDefault();

			let button = $(this).closest('td').find('.div-message-language');
			let ui_check_id = $(button).data("uicheck_id");
			let device_no = $(button).data("device_no");
			$.ajax({
				type: "get",
				url: "{{route('uicheck.files.record')}}",
				data: {
					ui_check_id,
					device_no
				},
				success: function (response) {
					$("#fileUploadedData").html(response.data);
					$("#displayFileUpload").modal("show")
				},
				error: function (response) {
					toastr['error']("Something went wrong!");
				}
			});
		});
	});

	$(document).on('click', '.expand-row-msg', function() {
		var name = $(this).data('name');
		var id = $(this).data('id');
		var full = '.expand-row-msg .show-short-' + name + '-' + id;
		var mini = '.expand-row-msg .show-full-' + name + '-' + id;
		$(full).toggleClass('hidden');
		$(mini).toggleClass('hidden');
	});
	$('.select2').select2();

	function copyToClipboard(text) {
		var sampleTextarea = document.createElement("textarea");
		document.body.appendChild(sampleTextarea);
		sampleTextarea.value = text; //save main text in it
		sampleTextarea.select(); //select textarea contenrs
		document.execCommand("copy");
		document.body.removeChild(sampleTextarea);
	}
	$(document).on("click", ".fa-copy", function() {
		console.log("asdasdasd");		
		var id = $(this).data("text");
		copyToClipboard(id);
		toastr['success']("Text copy successfully");
	});

	$(document).on("change", ".historystatus", function(e) {
		var id = $(this).data("id");
		var status_id = $(this).val();
		var deviceno = $(this).data("deviceno");
		var uicheckid = $(this).data("uicheckid");
		if(confirm("Are you sure you want to change status?")) {
			$.ajax({
			url: "{{route('uicheck.device.status')}}",
            type: 'POST',
            headers: {
                  'X-CSRF-TOKEN': "{{ csrf_token() }}"
              },
            dataType:"json",
            data: { id : id, status_id:status_id},
            beforeSend: function() {
				$("#loading-image").show();
            }
          }).done(function (response) {
            $("#loading-image").hide();
			toastr["success"](response.message);

			//update respective td background
			var dynamicClass = '.uidevmessage' + deviceno + uicheckid;
			// $(dynamicClass).parent('td').css("background-color",response.data);
			$(dynamicClass).css("background-color",response.data);

			let mdl = jQuery('#modalGetDevMessageHistory');
			mdl.modal("hide");
          }).fail(function (jqXHR, ajaxOptions, thrownError) {      
			toastr["error"](jqXHR.responseJSON.message);
			$("#loading-image").hide();
		  });
		}
	});
	
	$(document).ready(function () {
		$("#assign_user_to_website").click(function (e) { 
			e.preventDefault();
			let user = $("#assign-new-user").val()
			let website = $("#assign-new-website").val()

			if(user == null) {
				toastr['error']("Please select user.");
				return
			}
			if(website == null) {
				toastr['error']("Please select website.");
				return
			}

			$.ajax({
				type: "POST",
				url: "{{route('uicheck.assignNewuser')}}",
				beforeSend: function () {
                    $("#loading-image").show();
                },
				data: {
					_token: "{{csrf_token()}}",
					website,
					user
				},
				success: function (response) {
					if(response.status == true) {
						toastr['success'](response.message, 'success');
					} else {
						toastr['error'](response.message, 'error');
					}
					$("#loading-image").hide();
					$("#uiResponsive").modal('hide');

					window.location.assign("{{route('uicheck.responsive')}}?website=" + website + "&user=" + user);
				},
				error: function (error) {
					toastr['error']("Something went wrong", 'error');
					$("#loading-image").hide();
					$("#uiResponsive").modal('hide');
				}
			});
		});

		$("#add_user_to_website").click(function (e) { 
			e.preventDefault();
			let oldUserId = $("#old_user_id").val()
			let websiteId = $("#website_id").val()
			let newUserId = $("#new_user_id").val()

			if(newUserId == null) {
				toastr['error']("Please select user.");
				return
			}

			$.ajax({
				type: "POST",
				url: "{{route('uicheck.addNewuser')}}",
				beforeSend: function () {
                    $("#loading-image").show();
                },
				data: {
					_token: "{{csrf_token()}}",
					oldUserId,
					newUserId,
					websiteId
				},
				success: function (response) {
					if(response.status == true) {
						toastr['success'](response.message, 'success');
					} else {
						toastr['error'](response.message, 'error');
					}
					$("#loading-image").hide();
					$("#uiResponsive").modal('hide');

					location.reload();
				},
				error: function (error) {
					toastr['error']("Something went wrong", 'error');
					$("#loading-image").hide();
					$("#uiResponsive").modal('hide');
				}
			});
		});
	});

	@if (Auth::user()->isAdmin())
	function funGetUserHistory(uicheck_id) { 
		$.ajax({
			type: "get",
			url: "{{route('uicheck.userhistory')}}",
			data: {
				uicheck_id
			},
			beforeSend: function () {
				$("#loading-image").show();
			},
			success: function (response) {
				$("#userHistoryModelContent").html(response.data || "")
				$('#userHistoryModel').modal('show');
				$("#loading-image").hide();
			},
			error: function (error) {
				toastr['error']("Something went wrong", 'error');
				$("#loading-image").hide();
			}
		});
	}
	@endif

	$(function() {
		$('.toggle-event').click(function() {
			var $this = $(this);

			var uicheckId = $this.data('uicheck_id');
			var deviceNo = $this.data('device_no');
			if ($this.hasClass('text-danger')) {
				var eventType = false;
				$this.removeClass('text-danger');
			} else {
				var eventType = true;
				$this.addClass('text-danger');
			}
			

			jQuery.ajax({
				headers: {
					'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
				},
				url: "/uicheck/set/device-log",
				type: 'POST',
				data: {
					deviceNo : deviceNo,
					uicheckId : uicheckId,
					eventType : eventType
				},
				beforeSend: function() {
					//jQuery("#loading-image").show();
				}
			}).done(function(response) {
				toastr["success"](response.message);
				//mdl.find('tbody').html(response.html);
				//mdl.modal("show");
			}).fail(function(errObj) {
				console.log(errObj);
				$this.removeClass('text-danger');
				toastr["error"](errObj.responseJSON.message);
			});
		})
	});
</script>

@endsection