@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
@endsection

@section('content')
	<?php $base_url = URL::to('/');?>
	<div class = "row">
		<div class="col-lg-6 margin-tb">
			<h2 class="page-heading">Charity List</h2>
			@if(Session::has('flash_type'))
				<p class="alert alert-{{Session::get('flash_type')}}">{{ Session::get('message') }}</p>
			@endif
        </div>
	</div>
	<div class="row">
		<div class="col-lg-6 margin-tb">
			<div class="pull-left cls_filter_box">
                <form class="form-inline" action="{{ route('routes.index') }}" method="GET">
                    <div class="form-group ml-3 cls_filter_inputbox">
                        <label for="with_archived">Search</label>
                       <input type="text" name="search" class="form-control-sm cls_commu_his form-control" value="{{request()->get('search')}}">
                    </div>
                </form>
            </div>
		
			<div class="margin-tb pull-right">
				<button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#createCharityModal">Create</button>
			</div>		
		</div>
    </div>
   
    <div class="row">
        <div class="col-lg-6 margin-tb">
			<div class="panel-group" style="margin-bottom: 5px;">
                <div class="panel mt-3 panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">Charity</h4>
                    </div>
					<div class="panel-body">
						<table class="table table-bordered table-striped">
							<tr>
								<th>Name</th>
								<th>Contact No</th>
								<th>Email</th>
								<th>Action</th>
							</tr>
							@foreach ($charityData as $data )
								<tr>
									<td>{{$data->name}}</td>
									<td>{{$data->contact_no}}</td>
									<td>{{$data->email}}</td>
									<td>
										<button type="button" class="btn btn-default edit_charity" data-toggle="modal" data-name="{{$data->name}}" data-contact_no="{{$data->contact_no}}" data-id="{{$data->id}}" data-email="{{$data->email}}" data-target="#updateCharityModal">Update</button>
									</td>
								</tr>
							@endforeach
						</table>
						
						{{ $charityData->links() }}
                    </div>
                </div>
            </div>
		</div>
	</div>
	
	<div id="createCharityModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Charity</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{url('/charity/store')}}" method="POST">
					@csrf
					
                    <div class="modal-body">
                        <div class="form-group">
							<strong>Charity Name</strong>
							<input type='text' class="form-control" name="name" id="charity_name" required/>
                        </div>
						<div class="form-group">
							<strong>Contact No</strong>
							<input type='text' class="form-control" name="contact_no" id="contact_no" required/>
                        </div>
						<div class="form-group">
							<strong>Email</strong>
							<input type='text' class="form-control" name="email" id="email" required/>
						</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
	
	<div id="updateCharityModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Create Charity</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form action="{{url('/charity/update')}}" method="POST">
					@csrf
					<input type='hidden' name="id" id="update_charity_id" />
                    <div class="modal-body">
                        <div class="form-group">
							<strong>Charity Name</strong>
							<input type='text' class="form-control" name="name" id="update_charity_name" required/>
                        </div>
						<div class="form-group">
							<strong>Contact No</strong>
							<input type='text' class="form-control" name="contact_no" id="update_charity_contact_no" required/>
                        </div>
						<div class="form-group">
							<strong>Email</strong>
							<input type='text' class="form-control" name="email" id="update_charity_email" required/>
						</div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-secondary">Save</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function() {
		$(".edit_charity").on('click',function() {
			var email = $(this).data('email');
			var contact_no = $(this).data('contact_no');
			var name = $(this).data('name');
			var id = $(this).data('id');
			
			$("#update_charity_id").val(id);
			$("#update_charity_email").val(email);
			$("#update_charity_contact_no").val(contact_no);
			$("#update_charity_name").val(name);
		});
	});
</script>
@endsection