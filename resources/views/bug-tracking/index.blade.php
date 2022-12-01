@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
    <style type="text/css">
        .preview-category input.form-control {
            width: auto;
        }
        .break{
            word-break: break-all !important;
        }
    </style>

	<div class="row" id="common-page-layout">
		<div class="col-lg-12 margin-tb">
			<h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
		</div>
		<br>
		<div class="col-lg-12 margin-tb">
			<div class="row">
				<div class=" col-md-12">
					<div class="h" style="margin-bottom:10px;">
						<div class="row">
							<form class="form-inline message-search-handler" method="get">
								<div class="col">

									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-3">
										<?php
										$bug_type = request('bugtype');
										?>
										<select class="form-control" name="bug_type" id="bug_type">
											<option value="">Select BugType</option>
											<?php
											foreach ($bugTypes as $bugtype) { ?>
											<option value="<?php echo $bugtype->id; ?>" <?php if ($bug_type == $bugtype->id) echo "selected"; ?>><?php echo $bugtype->name; ?></option>
											<?php }
											?>
										</select>
									</div>
									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2">
										<?php
										$bug_environment = request('bug_enviornment');
										?>
										<select class="form-control" name="bug_enviornment" id="bug_enviornment">
											<option value="">Select BugEnvironment</option>
											<?php
											foreach ($bugEnvironments as $bugenvironment) { ?>
											<option value="<?php echo $bugenvironment->id; ?>" <?php if ($bug_environment == $bugenvironment->id) echo "selected"; ?>><?php echo $bugenvironment->name; ?></option>
											<?php }
											?>
										</select>
									</div>
									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2">
										<?php
										$bug_severity = request('bug_severity');
										?>
										<select class="form-control" name="bug_severity" id="bug_severity">
											<option value="">Select BugSeverity</option>
											<?php
											foreach ($bugSeveritys as $bugseverity) { ?>
											<option value="<?php echo $bugseverity->id; ?>" <?php if ($bug_severity == $bugseverity->id) echo "selected"; ?>><?php echo $bugseverity->name; ?></option>
											<?php }
											?>
										</select>
									</div>
									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2">
										<?php
										$bug_status = request('bugstatus');
										?>
										<select class="form-control" name="bug_status" id="bug_status">
											<option value="">Select BugStatus</option>
											<?php
											foreach ($bugStatuses as $bugstatus) { ?>
											<option value="<?php echo $bugstatus->id; ?>" <?php if ($bug_status == $bugstatus->id) echo "selected"; ?>><?php echo $bugstatus->name; ?></option>
											<?php }
											?>
										</select>
									</div>
									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2">
										<?php
										$module_id = request('module_id');
										?>
										<select class="form-control" name="module_id" id="module_id">
											<option value="">Select Module</option>
											@foreach($filterCategories as  $filterCategory)
												<option value="{{$filterCategory}}">{{$filterCategory}} </option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<input name="step_to_reproduce" type="text" class="form-control" placeholder="Search Reproduce" id="bug-search" data-allow-clear="true" />
									</div>
									<div class="form-group m-3">
										<input name="summary" type="text" class="form-control" placeholder="Search Summary" id="bug-summary" data-allow-clear="true" />
									</div>
									<div class="form-group m-1">
										<input name="url" type="text" class="form-control" placeholder="Search Url" id="bug-url" data-allow-clear="true" />
									</div>
									<div class="form-group col-md-1 cls_filter_inputbox p-2 mr-2">
										<?php
										$website = request('website');
										?>
										<select class="form-control" name="website" id="website">
											<option value="">Select Website</option>
											@foreach($filterWebsites as  $filterWebsite)

												<option value="{{$filterWebsite->id}}">{{$filterWebsite->title}} </option>
											@endforeach
										</select>
									</div>
									<div class="form-group">
										<input name="date" type="date" class="form-control" placeholder="Search Date" id="bug-date" data-allow-clear="true" />
									</div>
									<div class="form-group">
										<label for="button">&nbsp;</label>
										<button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
											<img src="/images/search.png" style="cursor: default;">
										</button>
										<a href="/bug-tracking" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
				<div class="col col-md-6">
					<div class="row">

						<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action"
								data-toggle="modal" data-target="#bugtrackingCreateModal">
							<img src="/images/add.png" style="cursor: default;">
						</button>
						<div class="pull-left">
							<button class="btn btn-secondary btn-xs btn-add-environment" style="color:white;"
									data-toggle="modal" data-target="#newEnvironment"> Environment
							</button>&nbsp;&nbsp;
							<button class="btn btn-secondary btn-xs btn-add-type" style="color:white;"
									data-toggle="modal" data-target="#newType"> Type
							</button>&nbsp;&nbsp;
							<button class="btn btn-secondary btn-xs btn-add-status" style="color:white;"
									data-toggle="modal" data-target="#newStatus"> Status
							</button>&nbsp;&nbsp;
							<button class="btn btn-secondary btn-xs btn-add-severity" style="color:white;"
									data-toggle="modal" data-target="#newSeverity"> Severity
							</button>
						</div>&nbsp;&nbsp;
					</div>
				</div>

			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="alert alert-success" id="alert-msg" style="display: none;">
						<p></p>
					</div>
				</div>
			</div>
			<div class="col-md-12 margin-tb" id="page-view-result">

			</div>
		</div>
	</div>
	<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
          50% 50% no-repeat;display:none;">
	</div>

	<div class="common-modal modal" role="dialog">
		<div class="modal-dialog" role="document">

		</div>
	</div>


	@include("bug-tracking.templates.list-template")
    @include("bug-tracking.create")
    @include("bug-tracking.edit")
	@include("bug-tracking.templates.create-bug-tracking-template")
	@include("bug-tracking.templates.bug-environment")
	@include("bug-tracking.templates.bug-severity")
	@include("bug-tracking.templates.bug-status")
	@include("bug-tracking.templates.bug-type")

	<div id="newHistoryModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h3>Bug Tracker History</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<table class="table">
					<tr>

						<th>Type of Bug</th>
						<th>Summary</th>
						<th>Environment</th>
						<th>Status</th>
						<th>Severity</th>
						<th>Module/Feature</th>
						<th>Updated By </th>
					</tr>
					<tbody class="tbh">

					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div id="newCommunictionModal" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h3>Communication</h3>
					<button type="button" class="close" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
				<table class="table">
					<thead>
					<th></th>
					<th></th>
					</thead>
					<tbody class="tbhc">

					</tbody>
				</table>
				</div>
			</div>
		</div>
	</div>


	<script type="text/javascript" src="{{ asset('/js/jsrender.min.js')}}"></script>
	<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js')}}"></script>
	<script src="{{ asset('/js/jquery-ui.js')}}"></script>
	<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
	<script type="text/javascript" src="{{ asset('/js/bug-tracker.js') }}"></script>

	<script type="text/javascript">
		page.init({
			bodyView: $("#common-page-layout"),
			baseUrl: "<?php echo url("/"); ?>"
		});
        $(document).ready(function () {
            {{--$(".btn-edit-template").click(function (event) {--}}
            {{--    var id = $(this).data('id');--}}
            {{--    $.ajax({--}}
            {{--        url: "/bug-tracking/edit/"+id,--}}
            {{--        type: "GET",--}}
            {{--        data: {--}}
            {{--            id: id,--}}
            {{--            _token: '{{ csrf_token() }}'--}}
            {{--        },--}}
            {{--        cache: false,--}}
            {{--        dataType: 'json',--}}
            {{--        success: function (data) {--}}
            {{--            --}}

            {{--        }--}}
            {{--    });--}}
            {{--});--}}
            $(".assign_to").change(function (event) {
                var id = $(this).data('id');
                var user_id = $(this).val();
                $.ajax({
                    url: "/bug-tracking/assign_user/",
                    type: "POST",
                    data: {
                        id: id,
                        user_id: user_id,
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function (data) {
                        location.reload()
                        toastr["success"]("Bug Tracking Saved Successfully");
                    }
                });
            });
			$(".bug_severity_id").change(function (event) {
				var id = $(this).data('id');
				var severity_id = $(this).val();
				$.ajax({
					url: "/bug-tracking/severity_user/",
					type: "POST",
					data: {
						id: id,
						severity_id: severity_id,
						_token: '{{ csrf_token() }}'
					},
					cache: false,
					dataType: 'json',
					success: function (data) {
						location.reload()
						toastr["success"]("Bug Tracking Saved Successfully");
					}
				});
			});
			$(".bug_status_id").change(function (event) {
				var id = $(this).data('id');
				var status_id = $(this).val();
				$.ajax({
					url: "/bug-tracking/status_user/",
					type: "POST",
					data: {
						id: id,
						status_id: status_id,
						_token: '{{ csrf_token() }}'
					},
					cache: false,
					dataType: 'json',
					success: function (data) {
						location.reload()
						toastr["success"]("Bug Tracking Saved Successfully");
					}
				});
			});
        })
	</script>
	<script type="text/javascript">
		$(document).on('click', '.expand-row-msg', function() {
			$('#bugtrackingShowFullTextModel').modal('toggle');
			$(".bugtrackingmanShowFullTextBody").html("");
			var id = $(this).data('id');
			var name = $(this).data('name');
			var full = '.expand-row-msg .show-full-' + name + '-' + id;
			var fullText = $(full).html();
			console.log(id,name,fullText,full)
			$(".bugtrackingmanShowFullTextBody").html(fullText);
		});
		$(document).on("click",".btn-copy-url",function() {
			var url = $(this).data('id');
			var $temp = $("<input>");
			$("body").append($temp);
			$temp.val(url).select();
			document.execCommand("copy");
			$temp.remove();
			alert("Copied!");
		});


	</script>
@endsection