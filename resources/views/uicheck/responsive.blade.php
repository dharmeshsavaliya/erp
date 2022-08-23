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
		color: #757575;
		background-color: #fff !important;
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
</style>
@endsection

@section('large_content')

<div id="myDiv">
	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">Ui Check Responsive({{$uiDevDatas->count()}})</h2>
	</div>

</div>

@if (Session::has('message'))
{{ Session::get('message') }}
@endif
<br />
<div class="row mt-2">
	<div class="col-md-12 margin-tb infinite-scroll">
		<div class="table-responsive">
			<table class="table table-bordered" id="uicheck_table1">
				<thead>
					<tr>
						<th width="10%">ID</th>
						<th width="10%">Ui Check ID</th>
						<th width="10%">Categories</th>
						<th width="5%">Device1</th>
						<th width="6%">Update By</th>
						<th width="6%">Status</th>
						<th width="10%">Change Status</th>
						<th width="5%">Device2</th>
						<th width="6%">Update By</th>
						<th width="6%">Status</th>
						<th width="10%">Change Status</th>
						<th width="5%">Device3</th>
						<th width="6%">Update By</th>
						<th width="6%">Status</th>
						<th width="10%">Change Status</th>
						<th width="5%">Device4</th>
						<th width="6%">Update By</th>
						<th width="6%">Status</th>
						<th width="10%">Change Status</th>
						<th width="5%">Device5</th>
						<th width="6%">Update By</th>
						<th width="6%">Status</th>
						<th width="10%">Change Status</th>
						
					</tr>
				</thead>
				<tbody>
					
					@foreach ($uiDevDatas as $uiDevData)
						<tr>
							<td>{{$uiDevData->id}}</td>
							<td>{{$uiDevData->uicheck_id}}</td>
							<td class="expand-row-msg" data-name="title" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
								<span class="show-short-title-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->title != '') {{ str_limit($uiDevData->title, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-title-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->title != '') {{$uiDevData->title}} @else   @endif</span>
							</td>
							
							<td>@if($uiDevData->device_no == 1) Device1 @else  @endif</td>
							<td class="expand-row-msg" data-name="username" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
								<span class="show-short-username-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->user_id != '' && $uiDevData->device_no == 1) {{ str_limit($uiDevData->username, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-username-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->user_id != '' && $uiDevData->device_no == 1) {{$uiDevData->username}} @else   @endif</span>
							</td>
							<td class="expand-row-msg" data-name="statusname" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
								<span class="show-short-statusname-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->status != '' && $uiDevData->device_no == 1) {{ str_limit($uiDevData->statusname, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-statusname-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->status != '' && $uiDevData->device_no == 1){{$uiDevData->statusname}}@else   @endif</span>
							</td>
							
							<td data-id="{{$uiDevData->id}}" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="{{$uiDevData->device_no}}"  data-old_status="{{$uiDevData->status}}" >@if($uiDevData->device_no == 1) {!! $status !!} <button type="button" class="btn btn-xs btn-status-history" title="Show Status History" data-id="{{$uiDevData->id}}" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="{{$uiDevData->device_no}}"  data-old_status="{{$uiDevData->status}}" ><i class="fa fa-info-circle "></i></button> @else  @endif</td>
							
							<td>@if($uiDevData->device_no == 2) Device2 @else  @endif</td>
							<td class="expand-row-msg" data-name="username" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
								<span class="show-short-username-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->user_id != '' && $uiDevData->device_no == 2) {{ str_limit($uiDevData->username, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-username-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->user_id != '' && $uiDevData->device_no == 2) {{$uiDevData->username}}@else   @endif</span>
							</td>
							<td class="expand-row-msg" data-name="statusname" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
								<span class="show-short-statusname-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->status != '' && $uiDevData->device_no == 2) {{ str_limit($uiDevData->statusname, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-statusname-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->status != '' && $uiDevData->device_no == 2) {{$uiDevData->statusname}}  @else   @endif</span>
							</td>
							
							<td data-id="{{$uiDevData->id}}" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="{{$uiDevData->device_no}}"  data-old_status="{{$uiDevData->status}}" >@if($uiDevData->device_no == 2) {!! $status !!} <button type="button" class="btn btn-xs btn-status-history" title="Show Status History" data-id="{{$uiDevData->id}}" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="{{$uiDevData->device_no}}"  data-old_status="{{$uiDevData->status}}" ><i class="fa fa-info-circle "></i></button> @else  @endif</td>

							<td>@if($uiDevData->device_no == 3) Device3 @else  @endif</td>
							<td class="expand-row-msg" data-name="username" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
								<span class="show-short-username-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->user_id != '' && $uiDevData->device_no == 3) {{ str_limit($uiDevData->username, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-username-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->user_id != '' && $uiDevData->device_no == 3) {{$uiDevData->username}}@else   @endif</span>
							</td>
							<td class="expand-row-msg" data-name="statusname" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
								<span class="show-short-statusname-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->status != '' && $uiDevData->device_no == 3) {{ str_limit($uiDevData->statusname, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-statusname-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->status != '' && $uiDevData->device_no == 3) {{$uiDevData->statusname}}  @else   @endif</span>
							</td>
							
							<td data-id="{{$uiDevData->id}}" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="{{$uiDevData->device_no}}"  data-old_status="{{$uiDevData->status}}" >@if($uiDevData->device_no == 3) {!! $status !!} <button type="button" class="btn btn-xs btn-status-history" title="Show Status History" data-id="{{$uiDevData->id}}" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="{{$uiDevData->device_no}}"  data-old_status="{{$uiDevData->status}}" ><i class="fa fa-info-circle "></i></button> @else  @endif</td>

							<td>@if($uiDevData->device_no == 4) Device4 @else  @endif</td>
							<td class="expand-row-msg" data-name="username" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
								<span class="show-short-username-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->user_id != '' && $uiDevData->device_no == 4) {{ str_limit($uiDevData->username, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-username-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->user_id != '' && $uiDevData->device_no == 4) {{$uiDevData->username}}@else   @endif</span>
							</td>
							<td class="expand-row-msg" data-name="statusname" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
								<span class="show-short-statusname-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->status != '' && $uiDevData->device_no == 4) {{ str_limit($uiDevData->statusname, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-statusname-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->status != '' && $uiDevData->device_no == 4) {{$uiDevData->statusname}}  @else   @endif</span>
							</td>
							
							<td data-id="{{$uiDevData->id}}" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="{{$uiDevData->device_no}}"  data-old_status="{{$uiDevData->status}}" >@if($uiDevData->device_no == 4) {!! $status !!} <button type="button" class="btn btn-xs btn-status-history" title="Show Status History" data-id="{{$uiDevData->id}}" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="{{$uiDevData->device_no}}"  data-old_status="{{$uiDevData->status}}" ><i class="fa fa-info-circle "></i></button> @else  @endif</td>

							<td>@if($uiDevData->device_no == 5) Device5 @else  @endif</td>
							<td class="expand-row-msg" data-name="username" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
								<span class="show-short-username-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->user_id != '' && $uiDevData->device_no == 5) {{ str_limit($uiDevData->username, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-username-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->user_id != '' && $uiDevData->device_no == 5) {{$uiDevData->username}}@else   @endif</span>
							</td>
							<td class="expand-row-msg" data-name="statusname" data-id="{{$uiDevData->id.$uiDevData->device_no}}">
								<span class="show-short-statusname-{{$uiDevData->id.$uiDevData->device_no}}">@if($uiDevData->status != '' && $uiDevData->device_no == 5) {{ str_limit($uiDevData->statusname, 5, '..')}} @else   @endif</span>
								<span style="word-break:break-all;" class="show-full-statusname-{{$uiDevData->id.$uiDevData->device_no}} hidden">@if($uiDevData->status != '' && $uiDevData->device_no == 5) {{$uiDevData->statusname}}  @else   @endif</span>
							</td>
							
							<td data-id="{{$uiDevData->id}}" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="{{$uiDevData->device_no}}"  data-old_status="{{$uiDevData->status}}" >@if($uiDevData->device_no == 5) {!! $status !!} <button type="button" class="btn btn-xs btn-status-history" title="Show Status History" data-id="{{$uiDevData->id}}" data-uicheck_id="{{$uiDevData->uicheck_id}}" data-device_no="{{$uiDevData->device_no}}"  data-old_status="{{$uiDevData->status}}" ><i class="fa fa-info-circle "></i></button> @else  @endif</td>
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



@if (Auth::user()->hasRole('Admin'))
<input type="hidden" id="user-type" value="Admin">
@else
<input type="hidden" id="user-type" value="Not Admin">
@endif

@endsection


@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>
<script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/min/dropzone.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
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
					$(".statuschanges").val("");
					toastr['success'](response.message);
				} else {
					toastr['error'](response.message);
				}
			}
		}).fail(function(response) {
			toastr['error'](response.message);
		});
	});	
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
	});

	$(document).on('click', '.expand-row-msg', function() {
		var name = $(this).data('name');
		var id = $(this).data('id');
		var full = '.expand-row-msg .show-short-' + name + '-' + id;
		var mini = '.expand-row-msg .show-full-' + name + '-' + id;
		$(full).toggleClass('hidden');
		$(mini).toggleClass('hidden');
	});
	
</script>

@endsection