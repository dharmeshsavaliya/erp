
          @foreach ($emailAddress as $server)
            <tr>
               <td>
				<input type="checkbox" class="checkbox_ch" id="u{{ $server->id }}" name="userIds[]" value="{{ $server->id }}"></td>
              <td class="expand-row-msg" data-name="username" data-id="{{$server->id}}">
                  <span class="show-short-username-{{$server->id}}">{{ Str::limit($server->username, 12, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-username-{{$server->id}} hidden">{{$server->username}}</span>
              </td>
              <td  class="expand-row-msg" data-name="password" data-id="{{$server->id}}">
                    <span class="show-short-password-{{$server->id}}">{{ Str::limit($server->password, 10, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-password-{{$server->id}} hidden">{{$server->password}}</span>
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
              <td class="expand-row-msg" data-name="host" data-id="{{$server->id}}">
              <span class="show-short-host-{{$server->id}}">{{ Str::limit($server->host, 10, '..')}}</span>
                  <span style="word-break:break-all;" class="show-full-host-{{$server->id}} hidden">{{$server->host}}</span>
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
              <td>
                @if($server->is_success == 1) {{ 'Success' }} @elseif(isset($server->is_success)) {{'Error'}} @else {{'-'}} @endif
              </td>
              <td>
              <button type="button" class="btn btn-xs assign-users p-0 m-0 text-secondary mr-2"  title="Assign users"  data-toggle="modal" data-target="#assignUsersModal{{$server->id}}" data-email-id="{{ $server->id }}" data-users="{{json_encode($server->email_assignes)}}">
                  <i class="fa fa-plus"></i>
                </button>
                <button type="button" class="btn btn-xs edit-email-addresses p-0 m-0 text-secondary mr-2"  data-toggle="modal" data-target="#emailAddressEditModal" data-email-addresses="{{ json_encode($server) }}">
                  <i class="fa fa-edit"></i>
                </button>
                <button type="button" class="btn btn-xs view-email-history p-0 m-0 text-secondary mr-2" data-id="{{ $server->id }}">
                  <i class="fa fa-eye"></i>
                </button>
                {!! Form::open(['method' => 'DELETE','route' => ['email-addresses.destroy', $server->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-xs p-0 m-0 text-secondary mr-2">
                      <i class="fa fa-trash"></i>
                    </button>
                {!! Form::close() !!}
                <a href="javascript:;" data-id="{{ $server->from_address }}" class="show-related-accounts p-0 m-0 text-secondary mr-2" title="Show Account">
                  <i class="fa fa-eye"></i>
                </a>
                <a href="javascript:;" onclick="sendtoWhatsapp({{ $server->id }})" title="Send to Whatsapp" class="btn btn-xs p-0 m-0 text-secondary mr-2">
                  <i class="fa fa-send-o"></i>
                </a>
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
                              {!!$uops!!}
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
      