@extends('layouts.app')

@section('title', 'Email Addresses List')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Email Addresses List</h2>
            <div class="pull-right">

                <button class="btn btn-secondary ml-3 error-email-history">View Errors</button>

                <button type="button" class="btn btn-secondary ml-3" data-toggle="modal" data-target="#emailAddressModal">+</button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="row">
        <div class="col-xs-12">
            <form class="form-search-data">
                <div class="row">
                    <div class="col-xs-12 col-md-3 pd-2">
                        <div class="form-group cls_task_subject">
                            <input type="text" name="keyword" placeholder="Search Keyword" class="form-control input-sm" value="{{ request('keyword') }}">
                        </div>
                    </div>
                    <!-- Language Selection -->
                    <div class="col-xs-12 col-md-3 pd-2">
                        <div class="form-group">
                            <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                        </div>
                    </div>
                    <!-- Search Network -->

                </div>
                <!-- FILTERS -->
            </form>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Email address Passwords Manager</h2>
            <div class="pull-left">

            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#passwordCreateModal">+</button>
            </div>
            <div>
                {{ Form::open(array('url' => route('email.password.change'), 'method' => 'post')) }}
                    <input type="hidden" name="users" id="userIds">
                    <button type="submit" class="btn btn-secondary"> Generate password </button>
                {{ Form::close() }}
            </div>
        </div>
    </div>

    <div class="mt-3 col-md-12">
      <table class="table table-bordered table-striped">
        <thead>
          <tr>
            <th>ID</th>
            <!--th>From Name</th>
            <th>From Address</th-->
			<th>Username</th>
            <th>Password</th>
            <th>Recovery Phone</th>
            <th>Recovery Email</th>
            <th>Driver</th>
            <th>Host</th>
            <th>Port</th>
            <th>Encryption</th>
            <th>Store Website</th>
            <th>Status</th>

            <th>Action</th>
          </tr>
        </thead>

        <tbody class="pending-row-render-view infinite-scroll-cashflow-inner">
          @foreach ($emailAddress as $server)
            <tr>
               <td>
				<input type="checkbox" class="checkbox_ch" id="u{{ $server->id }}" name="userIds[]" value="{{ $server->id }}"></td>
              <td><!--td>
                  {{ $server->from_name }}
              </td>
              <td>
                  {{ $server->from_address }}
              </td-->

                  {{ $server->username }}
              </td>
              <td>
                  {{ $server->password }}
              </td>
              <td>
                  {{ $server->recovery_phone }}
              </td>
              <td>
                  {{ $server->recovery_email }}
              </td>
              <td>
                  {{ $server->driver }}
              </td>
              <td>
                  {{ $server->host }}
              </td>
              <td>
                  {{ $server->port }}
              </td>
              <td>
                  {{ $server->encryption }}
              </td>

			  <td>
                  @if($server->website){{ $server->website->title }} @endif
              </td>
              <td>@if($server->is_success == 1) {{ 'Success' }} @elseif(isset($server->is_success)) {{'Error'}} @else {{'-'}} @endif</td>
              <td>

              <button type="button" class="btn btn-image assign-users d-inline"  title="Assign users"  data-toggle="modal" data-target="#assignUsersModal{{$server->id}}" data-email-id="{{ $server->id }}" data-users="{{json_encode($server->email_assignes)}}"><img src="/images/add.png" /></button>
                  <button type="button" class="btn btn-image edit-email-addresses d-inline"  data-toggle="modal" data-target="#emailAddressEditModal" data-email-addresses="{{ json_encode($server) }}"><img src="/images/edit.png" /></button>

                  <button type="button" class="btn btn-image view-email-history d-inline" data-id="{{ $server->id }}"><img width="2px;" src="/images/view.png"/></button>

                  {!! Form::open(['method' => 'DELETE','route' => ['email-addresses.destroy', $server->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image d-inline"><img src="/images/delete.png" /></button>
                  {!! Form::close() !!}
                   <a href="javascript:;" data-id="{{ $server->from_address }}" class="show-related-accounts" title="Show Account"><i class="fa fa-eye" aria-hidden="true"></i>
</a>

				   <a href="javascript:;" onclick="sendtoWhatsapp({{ $server->id }})" title="Send to Whatsapp"><i class="fa fa-send-o"></i></button></td>

					<div id="sendToWhatsapp{{$server->id}}" class="modal fade" role="dialog">
						<div class="modal-dialog">
							<!-- Modal content-->
							<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title">Send to Whatsapp</h4>
										<button type="button" class="close" data-dismiss="modal">&times;</button>
									</div>
									<form action="{{ route('email.password.sendwhatsapp') }}" method="POST">
									@csrf
									<div class="modal-body">
										<div class="form-group">
											<select class="form-control" name="user_id">
												@foreach($users as $user)
												<option class="form-control" value="{{ $user->id }}">{{ $user->name }}</option>
												@endforeach
											</select>
											 <input type="hidden" name="id" value="{{ $server->id }}"/>
											<input type="hidden" name="send_message" value="1">
											<input type="hidden" name="send_on_whatsapp" value="1">
										</div>
									</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
										<button type="submit" class="btn btn-secondary">Update</button>
									</div>
									</form>
							</div>

						</div>
					</div>
			  </td>
            </tr>



          @endforeach
        </tbody>
      </table>
    </div>
    <img class="infinite-scroll-products-loader center-block" src="{{asset('/images/loading.gif')}}" alt="Loading..." style="display: none" />

    @foreach ($emailAddress as $server)
    @php
      $assignids = [];
      $assigned_users = json_decode($server->email_assignes);
      foreach($assigned_users as $_assigned){
        $assignids[] = $_assigned->user_id;
      }
    @endphp
    <div id="assignUsersModal{{$server->id}}" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <form action="" method="POST" enctype="multipart/form-data" >
            @csrf
            @method('POST')
            <input type="hidden" name="email_id" id="email_id" value=''>
            <div class="modal-header">
              <h4 class="modal-title">Assign users</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

          <div class="form-group">
                  <strong>Users:</strong>
            <Select name="users[]" id="users" required multiple class="form-control select-multiple globalSelect2">
              <option value = ''>None</option>
              @foreach ($users as $key => $val)
                <option value="{{ $val->id }}" {{in_array($val->id,$assignids)?'selected':''}}>{{ $val->name }}</option>
              @endforeach
            </Select>
            @if ($errors->has('users'))
                <div class="alert alert-danger">{{$errors->first('users')}}</div>
            @endif
          </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Assign</button>
            </div>
          </form>
        </div>

      </div>
</div>
@endforeach
<div id="emailAddressModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="{{ route('email-addresses.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="modal-header">
          <h4 class="modal-title">Store a Email Address</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>From Name:</strong>
            <input type="text" name="from_name" class="form-control" value="{{ old('from_name') }}" required>

            @if ($errors->has('from_name'))
              <div class="alert alert-danger">{{$errors->first('from_name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>From Address:</strong>
            <input type="text" name="from_address" class="form-control" value="{{ old('from_address') }}" required>

            @if ($errors->has('from_address'))
              <div class="alert alert-danger">{{$errors->first('from_address')}}</div>
            @endif
          </div>


          <div class="form-group">
            <strong>Recovery Phone:</strong>
            <input type="text" name="recovery_phone" class="form-control" value="{{ old('recovery_phone') }}" required>

            @if ($errors->has('recovery_phone'))
              <div class="alert alert-danger">{{$errors->first('recovery_phone')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Recovery Email:</strong>
            <input type="text" name="recovery_email" class="form-control" value="{{ old('recovery_email') }}" required>

            @if ($errors->has('recovery_email'))
              <div class="alert alert-danger">{{$errors->first('recovery_email')}}</div>
            @endif
          </div>


			<div class="form-group">
            	<strong>Store Website:</strong>
				<Select name="store_website_id" class="form-control">
					<option value>None</option>
					@foreach ($allStores as $key => $val)
						<option value="{{ $val->id }}">{{ $val->title }}</option>
					@endforeach
				</Select>
				@if ($errors->has('store_website_id'))
						<div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
				@endif
			</div>
          <div class="form-group">
            <strong>Driver:</strong>
            <input type="text" name="driver" class="form-control" value="{{ old('driver') }}" required>

            @if ($errors->has('driver'))
              <div class="alert alert-danger">{{$errors->first('driver')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Host:</strong>
            <input type="text" name="host" class="form-control" value="{{ old('host') }}" required>

            @if ($errors->has('host'))
              <div class="alert alert-danger">{{$errors->first('host')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Port:</strong>
            <input type="text" name="port" class="form-control" value="{{ old('port') }}" required>

            @if ($errors->has('port'))
              <div class="alert alert-danger">{{$errors->first('port')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Encryption:</strong>
            <input type="text" name="encryption" class="form-control" value="{{ old('encryption') }}" required>

            @if ($errors->has('encryption'))
              <div class="alert alert-danger">{{$errors->first('encryption')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Username:</strong>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>

            @if ($errors->has('username'))
              <div class="alert alert-danger">{{$errors->first('username')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Password:</strong>
            <input type="text" name="password" class="form-control" value="{{ old('password') }}" required>

            @if ($errors->has('password'))
              <div class="alert alert-danger">{{$errors->first('password')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Name:</strong>
            <input type="text" name="signature_name" class="form-control" value="{{ old('signature_name') }}" required>

            @if ($errors->has('signature_name'))
              <div class="alert alert-danger">{{$errors->first('signature_name')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Signature Title:</strong>
            <input type="text" name="signature_title" class="form-control" value="{{ old('signature_title') }}" required>

            @if ($errors->has('signature_title'))
              <div class="alert alert-danger">{{$errors->first('signature_title')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Phone:</strong>
            <input type="text" name="signature_phone" class="form-control" value="{{ old('signature_phone') }}" required>

            @if ($errors->has('signature_phone'))
              <div class="alert alert-danger">{{$errors->first('signature_title')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Email:</strong>
            <input type="text" name="signature_email" class="form-control" value="{{ old('signature_email') }}" required>

            @if ($errors->has('signature_email'))
              <div class="alert alert-danger">{{$errors->first('signature_email')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Website:</strong>
            <input type="text" name="signature_website" class="form-control" value="{{ old('signature_website') }}" required>

            @if ($errors->has('signature_website'))
              <div class="alert alert-danger">{{$errors->first('signature_website')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Address:</strong>
            <textarea name="signature_address" id="address" class="form-control" >{{ old('signature_address') }}</textarea>

            @if ($errors->has('signature_address'))
              <div class="alert alert-danger">{{$errors->first('signature_address')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Logo:</strong>
            <input type="file" name="signature_logo" class="form-control" value="{{ old('signature_logo') }}" >

            @if ($errors->has('signature_logo'))
              <div class="alert alert-danger">{{$errors->first('signature_logo')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Image:</strong>
            <input type="file" name="signature_image" class="form-control" value="{{ old('signature_image') }}" >

            @if ($errors->has('signature_image'))
              <div class="alert alert-danger">{{$errors->first('signature_image')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Social Tag:</strong>

            <textarea id="social" name="signature_social" class="form-control" >{{ old('signature_social') }}</textarea>
            @if ($errors->has('signature_tag'))
              <div class="alert alert-danger">{{$errors->first('signature_tag')}}</div>
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit"  class="btn btn-secondary">Add</button>
        </div>
      </form>
    </div>

  </div>
</div>

<div id="EmailRunHistoryModal" class="modal fade" role="dialog">
	<div class="modal-dialog modal-lg">
		<!-- Modal content-->
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Email Run History</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
      <div class="modal-body">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Id</th>
                <th>From Name</th>
                <th>Status</th>
                <th>Message</th>
                <th>Created</th>
              </tr>
            </thead>

            <tbody>

            </tbody>
          </table>
        </div>
      </div>
			</div>
		</div>
	</div>

</div><div id="ErrorHistoryModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">

        <h4 class="modal-title">Email Error History</h4>

        <a class="btn btn-secondary ml-3" href="{{ route('email.failed.download') }}">Download</a>

        <button type="button" class="close" data-dismiss="modal">&times;</button>

      </div>
      <div class="modal-body">
        <div class="table-responsive mt-3">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Id</th>
                <th>From Name</th>
                <th>Status</th>
                <th>Message</th>
                <th>Created</th>
              </tr>
            </thead>

            <tbody>

            </tbody>
          </table>
        </div>
      </div>
      </div>
    </div>
  </div>
</div>



<div id="emailAddressEditModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <form action="" method="POST" enctype="multipart/form-data" >
        @csrf
        @method('PUT')

        <div class="modal-header">
          <h4 class="modal-title">Update a Email Address</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <strong>From Name:</strong>
            <input type="text" name="from_name" class="form-control" value="{{ old('from_name') }}" required>

            @if ($errors->has('from_name'))
              <div class="alert alert-danger">{{$errors->first('from_name')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>From Address:</strong>
            <input type="text" name="from_address" class="form-control" value="{{ old('from_address') }}" required>

            @if ($errors->has('from_address'))
              <div class="alert alert-danger">{{$errors->first('from_address')}}</div>
            @endif
          </div>


          <div class="form-group">
            <strong>Recovery Phone:</strong>
            <input type="text" name="recovery_phone" class="form-control" value="{{ old('recovery_phone') }}" required>

            @if ($errors->has('recovery_phone'))
              <div class="alert alert-danger">{{$errors->first('recovery_phone')}}</div>
            @endif
          </div>


          <div class="form-group">
            <strong>Recovery Email:</strong>
            <input type="text" name="recovery_email" class="form-control" value="{{ old('recovery_email') }}" required>

            @if ($errors->has('recovery_email'))
              <div class="alert alert-danger">{{$errors->first('recovery_email')}}</div>
            @endif
          </div>


		  <div class="form-group">
            	<strong>Store Website:</strong>
				<Select name="store_website_id" id="edit_store_website_id" class="form-control">
					<option value = ''>None</option>
					@foreach ($allStores as $key => $val)
						<option value="{{ $val->id }}">{{ $val->title }}</option>
					@endforeach
				</Select>
				@if ($errors->has('store_website_id'))
						<div class="alert alert-danger">{{$errors->first('store_website_id')}}</div>
				@endif
			</div>
          <div class="form-group">
            <strong>Driver:</strong>


            <!-- <input type="text" name="driver" class="form-control" value="{{ old('driver') }}" required> -->

            <Select name="driver" id="edit_driver" class="form-control required">
              <option value = ''>Select Driver</option>
              @foreach ($allDriver as $driver)
                <option value="{{ $driver }}">{{ $driver }}</option>
              @endforeach
            </Select>
            @if ($errors->has('driver'))
              <div class="alert alert-danger">{{$errors->first('driver')}}</div>
            @endif




          </div>
          <div class="form-group">
            <strong>Host:</strong>
            <input type="text" name="host" class="form-control" value="{{ old('host') }}" required>

            @if ($errors->has('host'))
              <div class="alert alert-danger">{{$errors->first('host')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Port:</strong>
            <!-- <input type="text" name="port" class="form-control" value="{{ old('port') }}" required> -->

            <Select name="port" id="edit_port" class="form-control required">
              <option value = ''>Select Port</option>
              @foreach ($allPort as $port)
                <option value="{{ $port }}">{{ $port }}</option>
              @endforeach
            </Select>

            @if ($errors->has('port'))
              <div class="alert alert-danger">{{$errors->first('port')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Encryption:</strong>

            <!-- <input type="text" name="encryption" class="form-control" value="{{ old('encryption') }}" required> -->

            <Select name="encryption" id="edit_encryption" class="form-control required">
              <option value = ''>Select Encryption</option>
              @foreach ($allEncryption as $encryption)
                <option value="{{ $encryption }}">{{ $encryption }}</option>
              @endforeach
            </Select>

            @if ($errors->has('encryption'))
              <div class="alert alert-danger">{{$errors->first('encryption')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Username:</strong>
            <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>

            @if ($errors->has('username'))
              <div class="alert alert-danger">{{$errors->first('username')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Password:</strong>
            <input type="text" name="password" class="form-control" value="{{ old('password') }}" required>

            @if ($errors->has('password'))
              <div class="alert alert-danger">{{$errors->first('password')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Name:</strong>
            <input type="text" name="signature_name" class="form-control" value="{{ old('signature_name') }}" required>

            @if ($errors->has('signature_name'))
              <div class="alert alert-danger">{{$errors->first('signature_name')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Signature Title:</strong>
            <input type="text" name="signature_title" class="form-control" value="{{ old('signature_title') }}" required>

            @if ($errors->has('signature_title'))
              <div class="alert alert-danger">{{$errors->first('signature_title')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Phone:</strong>
            <input type="text" name="signature_phone" class="form-control" value="{{ old('signature_phone') }}" required>

            @if ($errors->has('signature_phone'))
              <div class="alert alert-danger">{{$errors->first('signature_title')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Email:</strong>
            <input type="text" name="signature_email" class="form-control" value="{{ old('signature_email') }}" required>

            @if ($errors->has('signature_email'))
              <div class="alert alert-danger">{{$errors->first('signature_email')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Website:</strong>
            <input type="text" name="signature_website" class="form-control" value="{{ old('signature_website') }}" required>

            @if ($errors->has('signature_website'))
              <div class="alert alert-danger">{{$errors->first('signature_website')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Address:</strong>
            <textarea name="signature_address" id="address1" class="form-control" >{{ old('signature_address') }}</textarea>

            @if ($errors->has('signature_address'))
              <div class="alert alert-danger">{{$errors->first('signature_address')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Logo:</strong>
            <input type="file" name="signature_logo" class="form-control" value="{{ old('signature_logo') }}" >
            <img src="" id="img1" style="width:100px;height:100px">

            @if ($errors->has('signature_logo'))
              <div class="alert alert-danger">{{$errors->first('signature_logo')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Image:</strong>
            <input type="file" name="signature_image" class="form-control" value="{{ old('signature_image') }}" >
            <img src="" id="img2" style="width:100px;height:100px">>


            @if ($errors->has('signature_image'))
              <div class="alert alert-danger">{{$errors->first('signature_image')}}</div>
            @endif
          </div>
          <div class="form-group">
            <strong>Signature Social Tag:</strong>

            <textarea id="social1" name="signature_social" class="form-control" >{{ old('signature_social') }}</textarea>
            @if ($errors->has('signature_tag'))
              <div class="alert alert-danger">{{$errors->first('signature_tag')}}</div>
            @endif
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Update</button>
        </div>
      </form>
    </div>

  </div>
</div>

<div id="show-content-model-table" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg">
          <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title"></h4>
                  <button type="button" class="close" data-dismiss="modal">&times;</button>
              </div>
              <div class="modal-body">

              </div>
          </div>
      </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
         50% 50% no-repeat;display:none;">
</div>

@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/8lx26kd08fse8eckrno8tqi4pkf298s9d9hunvvzy4ri6ru4/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<script>
  tinymce.init({
    selector: '#address',
    menubar: false
  });
  tinymce.init({
    selector: '#social',
    menubar: false
  });


</script>
  <script type="text/javascript">
    $(document).on('click', '.edit-email-addresses', function() {
      var emailAddress = $(this).data('email-addresses');
      var url = "{{ route('email-addresses.index') }}/" + emailAddress.id;

      $('#emailAddressEditModal form').attr('action', url);
      $('#emailAddressEditModal').find('input[name="from_name"]').val(emailAddress.from_name);
      $('#emailAddressEditModal').find('input[name="from_address"]').val(emailAddress.from_address);

      // $('#emailAddressEditModal').find('input[name="driver"]').val(emailAddress.driver);

      $('#emailAddressEditModal').find('input[name="host"]').val(emailAddress.host);

      // $('#emailAddressEditModal').find('input[name="port"]').val(emailAddress.port);

      // $('#emailAddressEditModal').find('input[name="encryption"]').val(emailAddress.encryption);

      $('#emailAddressEditModal').find('input[name="username"]').val(emailAddress.username);
      $('#emailAddressEditModal').find('input[name="password"]').val(emailAddress.password);

      $('#emailAddressEditModal').find('input[name="recovery_phone"]').val(emailAddress.recovery_phone);
      $('#emailAddressEditModal').find('input[name="recovery_email"]').val(emailAddress.recovery_email);

      $('#emailAddressEditModal').find('input[name="signature_name"]').val(emailAddress.signature_name);
	    $('#emailAddressEditModal').find('input[name="signature_title"]').val(emailAddress.signature_title);
      $('#emailAddressEditModal').find('input[name="signature_email"]').val(emailAddress.signature_email);
      $('#emailAddressEditModal').find('input[name="signature_phone"]').val(emailAddress.signature_phone);
      $('#emailAddressEditModal').find('input[name="signature_website"]').val(emailAddress.signature_website);
      $('#emailAddressEditModal').find('textarea[name="signature_address"]').val(emailAddress.signature_address);
      $('#emailAddressEditModal').find('textarea[name="signature_social"]').val(emailAddress.signature_social);
      $("#img1").attr("src",  "{{url('/')}}/uploads/" + emailAddress.signature_logo);
      $("#img2").attr("src",  "{{url('/')}}/uploads/" + emailAddress.signature_image);
      tinymce.init({
    selector: '#address1',
    menubar: false
  });
  tinymce.init({
    selector: '#social1',
    menubar: false
  });

	  $('#edit_store_website_id').val(emailAddress.store_website_id).trigger('change');

    $('#edit_driver').val(emailAddress.driver).trigger('change');
    $('#edit_port').val(emailAddress.port).trigger('change');
    $('#edit_encryption').val(emailAddress.encryption).trigger('change');

    });

    $(document).on('click', '.assign-users', function() {
      var emailId = $(this).data('email-id');
      var users = $(this).data('users');
      var url = "{{ route('email-addresses.assign') }}";
      $('#assignUsersModal'+emailId).find('input[name="email_id"]').val(emailId);
      $('#assignUsersModal'+emailId+' form').attr('action', url);

    });


    $(document).on('click', '.view-email-history', function(e) {
        var id = $(this).attr('data-id');
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/getemailhistory/'+id,
          dataType: 'json',
          type: 'post',
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          // Show data in modal
          $('#EmailRunHistoryModal tbody').html(response.data);
          $('#EmailRunHistoryModal').modal('show');

          $("#loading-image").hide();
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });

    $(document).on('click', '.error-email-history', function(e) {
        $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: '/email/geterroremailhistory',
          dataType: 'json',
          type: 'post',
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          // Show data in modal
          $('#ErrorHistoryModal tbody').html(response.data);
          $('#ErrorHistoryModal').modal('show');

          $("#loading-image").hide();
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });


    $(document).on("click",".show-related-accounts",function(e){
        var id = $(this).data('id');
        $.ajax({
          url: '/email/get-related-account/'+id,
          type: 'get',
            beforeSend: function () {
                $("#loading-image").show();
            },
        }).done( function(response) {
          // Show data in modal

          var model  = $("#show-content-model-table");
              model.find(".modal-title").html("Email Used Places");
              model.find(".modal-body").html(response);
              model.modal("show");

          $("#loading-image").hide();
        }).fail(function(errObj) {
          $("#loading-image").hide();
        });
    });
  $('.checkbox_ch').change(function(){
             var values = $('input[name="userIds[]"]:checked').map(function(){return $(this).val();}).get();
             $('#userIds').val(values);
         });
function sendtoWhatsapp(password_id) {
		$("#sendToWhatsapp"+ password_id +"" ).modal('show');
	}
  </script>
  <script>

        var isLoading = false;
        var page = 1;
        $(document).ready(function () {

            $(window).scroll(function() {
                if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
                    loadMore();
                }
            });

            function loadMore() {
                if (isLoading)
                    return;
                isLoading = true;
                var $loader = $('.infinite-scroll-products-loader');
                page = page + 1;
                $.ajax({
                    url: "{{url('email-addresses')}}?ajax=1&page="+page,
                    type: 'GET',
                    data: $('.form-search-data').serialize(),
                    beforeSend: function() {
                        $loader.show();
                    },
                    success: function (data) {

                        $loader.hide();
                        if('' === data.trim())
                            return;
                        $('.infinite-scroll-cashflow-inner').append(data);


                        isLoading = false;
                    },
                    error: function () {
                        $loader.hide();
                        isLoading = false;
                    }
                });
            }
        });


  </script>
@endsection
