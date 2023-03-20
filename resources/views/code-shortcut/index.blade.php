@extends('layouts.app')
@section('title', 'Code Shortcut')
@section('content')
<style type="text/css">
	#loading-image {
		position: fixed;
		top: 50%;
		left: 50%;
		margin: -50px 0px 0px -50px;
		z-index: 60;
	}
</style>
<div id="myDiv">
	<img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
</div>
<div class="row" id="product-template-page">
	<div class="col-lg-12 margin-tb">
		<h2 class="page-heading">Code Shortcut (<span id="user_count">{{ count($codeshortcut) }}</span>)</h2>
		<div class="pull-left">
			<div class="form-group">
				<div class="row">
					<div class="col-md-4">
						<select class="form-control select-multiple" id="supplier-select">
							<option value="">Select Supplier</option>
							@foreach($suppliers as $supplier)
							<option value="{{ $supplier->id }}">{{ $supplier->supplier }}</option>
							@endforeach
						</select>
					</div>
					<div class="col-md-4">
						<input name="term" type="text" class="form-control" value="{{ isset($term) ? $term : '' }}" placeholder="Name of Code" id="term">
					</div>

					<div class="col-md-2">
						<button type="button" class="btn btn-image" onclick="submitSearch()"><img src="/images/filter.png" /></button>
					</div>
					<div class="col-md-2">
						<button type="button" class="btn btn-image" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" /></button>
					</div>
				</div>
			</div>
		</div>
		<div class="pull-right pr-4">
			<button type="button" class="btn btn-secondary create-product-template-btn" data-toggle="modal" data-target="#create_code_shortcut">+ Add Code</button>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-12">
		@if(session()->has('success'))
		<div class="alert alert-success" role="alert">{{session()->get('success')}}</div>
		@endif

	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<table class="table table-striped table-bordered" id="code_table">
			<thead>
				<tr>
					<th>ID</th>
					<th>User Name</th>
					<th>Supplier Name</th>
					<th>Code</th>
					<th>Description</th>
					<th>Created At</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
				@include('code-shortcut.partials.list-code')
			</tbody>


		</table>
	</div>
</div>

<!-- Modal -->
<div class="modal fade" id="create_code_shortcut" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Create Code Shortcut</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" enctype="multipart/form-data" id="code-shortcut-from" action="{{route('code-shortcuts.store')}}">
				@csrf
				@method('POST')

				<div class="modal-body">

					<div class="col-sm-12">
						<div class="form-group">
							<label>Supplier</label>
							<select name="supplier" class="form-control code">
								<option value="0">Selet Supplier</option>
								@foreach($suppliers as $supplier)
								<option value="{{$supplier->id}}">{{$supplier->supplier}}</option>
								@endforeach
							</select>

						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Code</label>
							<?php echo Form::text('code', null, ['class' => 'form-control code', 'required' => 'true', 'value' => "{{old('code')}}"]); ?>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Description</label>
							<?php echo Form::text('description', null, ['class' => 'form-control description', 'required' => 'true', 'value' => "{{old('description')}}"]); ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="edit_code_shortcut" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="exampleModalLongTitle">Edit Code Shortcut</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<form method="post" enctype="multipart/form-data" id="edit_code_shortcut_from">
				@csrf
				@method('put')

				<div class="modal-body">

					<div class="col-sm-12">
						<div class="form-group">
							<label>Supplier</label>
							<select name="supplier" id="supplier" class="form-control code">
								<option value="0">Selet Supplier</option>
								@foreach($suppliers as $supplier)
								<option value="{{$supplier->id}}">{{$supplier->supplier}}</option>
								@endforeach
							</select>

						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Code</label>
							<?php echo Form::text('code', null, ['id' => 'code', 'class' => 'form-control code', 'required' => 'true', 'value' => "{{old('code')}}"]); ?>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group">
							<label>Description</label>
							<?php echo Form::text('description', null, ['id' => 'description', 'class' => 'form-control description', 'required' => 'true', 'value' => "{{old('description')}}"]); ?>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
					<button type="submit" class="btn btn-primary">Save changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	function submitSearch() {
		src = '{{route("code-shortcuts")}}'
		term = $('#term').val()
		id = $('#supplier-select').val()
		$.ajax({
			url: src,
			dataType: "json",
			data: {
				term: term,
				id: id,

			},
			beforeSend: function() {
				$("#loading-image").show();
			},

		}).done(function(data) {
			$("#loading-image").hide();
			$("#code_table tbody").empty().html(data.tbody);

		}).fail(function(jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});

	}

	function resetSearch() {
		src = '{{route("code-shortcuts")}}'
		blank = ''
		$.ajax({
			url: src,
			dataType: "json",
			data: {

				blank: blank,

			},
			beforeSend: function() {
				$("#loading-image").show();
			},

		}).done(function(data) {
			$("#loading-image").hide();
			$('#term').val('')
			$('#supplier-select').val('')
			$("#code_table tbody").empty().html(data.tbody);

		}).fail(function(jqXHR, ajaxOptions, thrownError) {
			alert('No response from server');
		});
	}
</script>

<script>
	$(document).ready(function() {
		$('.edit_modal').on('click', function() {
			var id = $(this).attr("data-id")
			var url = '{{route("code-shortcuts.update",0)}}'
			url = url.replace("/0/", "/" + id + "/")
			$("#edit_code_shortcut_from").attr('action', url)
			$('#code').val($(this).attr("data-code"));
			$('#description').val($(this).attr("data-des"));
			$('#supplier').val($(this).attr("data-supplier"));
			$('#edit_code_shortcut').modal('show');
		})
	});
</script>

@endsection