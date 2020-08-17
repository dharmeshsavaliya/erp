@php
    $isAdmin = Auth::user()->hasRole('Admin');
    $isHrm = Auth::user()->hasRole('HOD of CRM')

@endphp
@foreach ($vendors as $vendor)
<tr>
    <td>{{ $vendor->id }}</td>
    <td class="expand-row table-hover-cell">
<span class="td-mini-container">
 @if(isset($vendor->category->title)) {{ strlen($vendor->category->title ) > 7 ? substr($vendor->category->title , 0, 7) : $vendor->category->title  }} @endif
</span>
       {{ strlen($vendor->category_name) > 7 ? substr($vendor->category_name, 0, 7) : $vendor->category_name }}
</span>
    </td>
    <td style="word-break: break-all;" class="expand-row">
        <div class="row">
            <div class="col-md-6 cls_remove_rightpadding">
                <span class="td-mini-container">
                  {{ strlen($vendor->name) > 7 ? substr($vendor->name, 0, 7) : $vendor->name }}
                </span>
                <span class="td-full-container hidden">
                  {{ $vendor->name }}
                </span>
            </div>
            <div class="col-md-6 cls_remove_allpadding clsphonebox">
                 @if($vendor->phone)
                        <button type="button" class="btn btn-image call-select popup" data-id="{{ $vendor->id }}"><img src="<?php echo $base_url;?>/images/call.png"/></button>
                          <div class="numberSend" id="show{{ $vendor->id }}">
                          <select class="form-control call-twilio" data-context="vendors" data-id="{{ $vendor->id }}" data-phone="{{ $vendor->phone }}">
                            <option disabled selected>Select Number</option>
                            @foreach(\Config::get("twilio.caller_id") as $caller)
                            <option value="{{ $caller }}">{{ $caller }}</option>
                            @endforeach
                          </select>
                          </div>

                        @if ($vendor->is_blocked == 1)
                            <button type="button" class="btn btn-image block-twilio" data-id="{{ $vendor->id }}"><img src="<?php echo $base_url;?>/images/blocked-twilio.png"/></button>
                        @else
                            <button type="button" class="btn btn-image block-twilio" data-id="{{ $vendor->id }}"><img src="<?php echo $base_url;?>/images/unblocked-twilio.png"/></button>
                        @endif
                @endif
            </div>
        </div>
    </td>
    <td>{{ $vendor->phone }} 
        @if ($vendor->status == 1)
          <button type="button" class="btn btn-image vendor-update-status-icon" id="btn_vendorstatus_{{ $vendor->id }}" title="On" data-id="{{ $vendor->id }}" id="do_not_disturb"><img src="{{asset('images/do-disturb.png')}}" /></button>
          <input type="hidden" name="hdn_vendorstatus" id="hdn_vendorstatus_{{ $vendor->id }}" value="false" />  
        @else
          <button type="button" class="btn btn-image vendor-update-status-icon" id="btn_vendorstatus_{{ $vendor->id }}" title="Off" data-id="{{ $vendor->id }}" id="do_not_disturb"><img src="{{asset('images/do-not-disturb.png')}}" /></button>
          <input type="hidden" name="hdn_vendorstatus" id="hdn_vendorstatus_{{ $vendor->id }}" value="true" />
        @endif
    </td>
    <td class="expand-row table-hover-cell" style="word-break: break-all;">
        {{ $vendor->email }}
    </td>
    {{-- <td style="word-break: break-all;">{{ $vendor->social_handle }}</td>
    <td style="word-break: break-all;">{{ $vendor->website }}</td> --}}
    <td class="expand-row table-hover-cell {{ $vendor->message_status == 0 ? 'text-danger' : '' }}" style="word-break: break-all;padding: 5px;">
        <div class="row">
            <div class="col-md-8 form-inline cls_remove_rightpadding">
                <div class="row cls_textarea_subbox">
                    <div class="col-md-11 cls_remove_rightpadding">
                        <textarea rows="1" class="form-control quick-message-field cls_quick_message" id="messageid_{{ $vendor->id }}" name="message" placeholder="Message"></textarea>
                    </div>
                    <div class="col-md-1 cls_remove_allpadding">
                        <button class="btn btn-sm btn-image send-message1" data-vendorid="{{ $vendor->id }}"><img src="<?php echo $base_url;?>/images/filled-sent.png"/></button>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-12" style="padding: 3px;">
                        @if(isset($vendor->chat_messages[0])) 
                            <span class="td-mini-container message-chat-txt" id="message-chat-txt-{{ $vendor->id }}">    
                            {{ strlen($vendor->chat_messages[0]->message) > 30 ? substr($vendor->chat_messages[0]->message, 0, 30) . '...' : $vendor->chat_messages[0]->message }}
                            </span>
                            <span class="td-full-container hidden">
                              {{ $vendor->chat_messages[0]->message }}
                            </span>
                        @endif    
                        @if(isset($vendor->message)) 
                            <span class="td-mini-container message-chat-txt" id="message-chat-txt-{{ $vendor->id }}">
                            {{ strlen($vendor->message) > 30 ? substr($vendor->message, 0, 30) . '...' : $vendor->message }}
                            </span>
                            <span class="td-full-container hidden">
                              {{ $vendor->message }}
                            </span>
                        @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="communication-div-5">
                    <div class="row">
                        <div class="col-md-10 cls_remove_allpadding">
                            <div class="d-flex">
                                <?php echo Form::select("quickComment",["" => "--Auto Reply--"]+$replies, null, ["class" => "form-control quickComment select2-quick-reply","style" => "width:100%" ]); ?>
                                <a class="btn btn-image delete_quick_comment"><img src="<?php echo $base_url;?>/images/delete.png" style="cursor: default; width: 16px;"></a>
                            </div>
                        </div> 
                    </div>
                </div>        
            </div> 
        </div>
    </td>
    <td>
        <div class="cls_action_btn" style="width: 233px;">
            <a href="{{ route('vendors.show', $vendor->id) }}" class="btn btn-image" href=""><img src="<?php echo $base_url;?>/images/view.png"/></a>

            <button data-toggle="modal" data-target="#reminderModal" class="btn btn-image set-reminder"
             data-id="{{ $vendor->id }}"
             data-frequency="{{ $vendor->frequency ?? '0' }}"
             data-reminder_message="{{ $vendor->reminder_message }}"
             data-reminder_from="{{ $vendor->reminder_from }}"
             data-reminder_last_reply="{{ $vendor->reminder_last_reply }}"
             >
                <img src="{{ asset('images/alarm.png') }}" alt="" style="width: 18px;">
            </button>

            <button type="button" class="btn btn-image edit-vendor" data-toggle="modal" data-target="#vendorEditModal" data-vendor="{{ json_encode($vendor) }}"><img src="<?php echo $base_url;?>/images/edit.png"/></button>
            <a href="{{route('vendors.payments', $vendor->id)}}" class="btn btn-sm" title="Vendor Payments" target="_blank"><i class="fa fa-money"></i> </a>
            <button type="button" class="btn btn-image make-remark" data-toggle="modal" data-target="#makeRemarkModal" data-id="{{ $vendor->id }}"><img src="<?php echo $base_url;?>/images/remark.png"/></a>
                <button data-toggle="modal" data-target="#zoomModal" class="btn btn-image set-meetings" data-title="Meeting with {{ $vendor->name }}" data-id="{{ $vendor->id }}" data-type="vendor"><i class="fa fa-video-camera" aria-hidden="true"></i></button>
                {!! Form::open(['method' => 'DELETE','route' => ['vendors.destroy', $vendor->id],'style'=>'display:inline']) !!}
                <button type="submit" class="btn btn-image"><img src="<?php echo $base_url;?>/images/delete.png"/></button>
            {!! Form::close() !!}
            <span class="btn">
                <input type="checkbox" class="select_vendor" name="select_vendor[]" value="{{$vendor->id}}" {{ request()->get('select_all') == 'true' ? 'checked' : '' }}>
            </span>
            
            <button type="button" class="btn send-email-to-vender" data-id="{{$vendor->id}}"><i class="fa fa-envelope-square"></i></button>
            <button type="button" class="btn create-user-from-vender" onclick="createUserFromVendor({{ $vendor->id }}, '{{ $vendor->email }}')"><i class="fa fa-user"></i></button>
        </div>
    </td>
</tr>
@endforeach
   
