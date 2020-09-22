
    @foreach ($emails as $key => $email)
        <tr id="{{ $email->id }}-email-row" class="search-rows">
            <td>{{ Carbon\Carbon::parse($email->created_at)->format('d-m-Y') }}</td>
            <td>{{ $email->from }}</td>
            <td>{{ $email->to }}</td>
            <td>{{ $email->type }}</td>
			<td>{{ substr($email->subject, 0,  50) }} {{strlen($email->subject) > 50 ? '...' : '' }}</td>
			<td>
				@foreach ($email_categories as $category)
					@if($category->id == $email->email_category_id)
						{{$category->category_name}} 
					@endif
				@endforeach
			</td>
			<td>
				@foreach ($email_status as $status)
					@if($status->id == $email->status)
						{{$status->email_status}} 
					@endif
				@endforeach
			</td>
			<td data-toggle="modal" data-target="#viewMail"  onclick="opnMsg({{$email}})" style="cursor: pointer;">{{ substr($email->message, 0,  50) }} {{strlen($email->message) > 50 ? '...' : '' }}</td>
            <td>
				
                <a title="Resend" class="btn-image resend-email-btn" data-id="{{ $email->id }}" >
                    <i class="fa fa-repeat"></i>
                </a>
                <a title="Reply" class="btn-image reply-email-btn" data-toggle="modal" data-target="#replyMail" data-id="{{ $email->id }}" >
                    <i class="fa fa-reply"></i>
                </a>
                <a title="Forward" class="btn-image forward-email-btn" data-toggle="modal" data-target="#forwardMail" data-id="{{ $email->id }}" >
                    <i class="fa fa-share"></i>
                </a>
                <a title="Bin" class="btn-image bin-email-btn" data-id="{{ $email->id }}" >
                    <i class="fa fa-trash"></i>
                </a>
                <button style="padding:3px;" type="button" class="btn-image make-remark d-inline" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $email->id }}"><img width="2px;" src="images/remark.png"/></button>
				<button style="padding:3px;" type="button" class="btn-image make-remark d-inline mailupdate" data-toggle="modal" data-status="{{ $email->status }}" data-category="{{ $email->email_category_id}}" data-target="#UpdateMail" data-id="{{ $email->id }}"><img width="2px;" src="images/edit.png"/></button>
            </td>
        </tr>
    @endforeach
    {{-- {{$emails->links()}} --}}