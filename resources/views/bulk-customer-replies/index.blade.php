@extends('layouts.app')

@section('content')
    <div class="col-md-12">
        <h2 class="page-heading">Bulk Customer Replies</h2>
    </div>
    <div class="col-md-12">
        @if(Session::has('message'))
            <div class="alert alert-info">
                {{ Session::get('message') }}
            </div>
        @endif
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Keywords, Phrases & Sentences</strong>
            </div>
            <div class="panel-body">
                <form method="post" action="{{ action('BulkCustomerRepliesController@storeKeyword') }}">
                    @csrf
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <input type="text" name="keyword" id="keyword" placeholder="Keyword, phrase or sentence..." class="form-control">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button class="btn btn-secondary btn-block">Add New</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="alert alert-warning">
                    <strong>Note: Click any tag below, and it will show the customer with the keywords used.</strong>
                </div>
                <div>
                    <strong>Manually Added</strong><br>
                    @foreach($keywords as $keyword)
                        <a href="{{ action('BulkCustomerRepliesController@index', ['keyword_filter' => $keyword->value]) }}" style="font-size: 14px;" class="label label-default">{{$keyword->value}}</a>
                    @endforeach
                </div>
                <div class="mt-2">
                    <strong>Auto Generated</strong><br>
                    @foreach($autoKeywords as $keyword)
                        <a href="{{ action('BulkCustomerRepliesController@index', ['keyword_filter' => $keyword->value]) }}" style="font-size: 14px; margin-bottom: 2px; display:inline-block;" class="label label-default">{{$keyword->value}}({{$keyword->count}})</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        @if($searchedKeyword)
            @if($searchedKeyword->customers)
                @php
                    $customers = $searchedKeyword->customers()->leftJoin(\DB::raw('(SELECT MAX(chat_messages.id) as  max_id, customer_id ,message as matched_message  FROM `chat_messages` join customers as c on c.id = chat_messages.customer_id  GROUP BY customer_id ) m_max'), 'm_max.customer_id', '=', 'customers.id')->groupBy('customers.id')->orderBy('max_id','desc')->get()
                @endphp
                <form action="{{ action('BulkCustomerRepliesController@sendMessagesByKeyword') }}" method="post">
                    @csrf
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Pick?</th>
                            <th>S.N</th>
                            <th>Customer ({{count($customers)}})</th>
                            <th>Recent Messages</th>
                            <th>Shortcuts</th>
                            <th>Next Action</th>
                            <th>Communication</th>
                        </tr>
                        <tr>
                            <td colspan="7">
                                <div class="row">
                                    <div class="col-md-11">
                                        <textarea name="message" id="message" rows="1" class="form-control" placeholder="Common message.."></textarea>
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-secondary btn-block">Send</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @foreach($customers as $key=>$customer)
                            <tr>
                                <td><input type="checkbox" name="customers[]" value="{{ $customer->id }}"></td>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $customer->name }}</td>
                                <td>
                                    <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="customer" data-limit="10" data-id="{{$customer->id}}" data-is_admin="1" data-is_hod_crm="" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
                                </td>
                                <td>@include('bulk-customer-replies.partials.shortcuts')</td>
                                <td>@include('bulk-customer-replies.partials.next_actions')</td>
                                <td class="communication-td">@include('bulk-customer-replies.partials.communication')</td>
                            </tr>
                        @endforeach
                    </table>
                </form>
            @else
            @endif
        @endif
    </div>
    <div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="text" name="search_chat_pop"  class="form-control search_chat_pop" placeholder="Search Message" style="width: 200px;">
                    <input type="text" name="search_chat_pop_time"  class="form-control search_chat_pop_time" placeholder="Search Time" style="width: 200px;">
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="/js/site-helper.js"></script>
    <script src="https://rawgit.com/jackmoore/autosize/master/dist/autosize.min.js"></script>
    <script>
        autosize(document.getElementById("message"));
    </script>
    <script type="text/javascript">
        $(document).on('click', '.add_next_action', function (event) {
            $.ajax({
                url: "/erp-customer/add-next-actions",
                type: 'POST',
                data: {
                    "name": $('input[name="add_next_action"]').val(),
                    "_token": "{{csrf_token()}}",
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Action added successfully!", "Message");
                    $('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });
        $(document).on('click', '.send-message-open', function (event) {
            var textBox = $(this).closest(".communication-td").find(".send-message-textbox");
            var sendToStr  = $(this).closest(".communication-td").next().find(".send-message-number").val();
            let issueId = textBox.attr('data-customerid');
            let message = textBox.val();
            if (message == '') {
                return;
            }

            let self = textBox;

            $.ajax({
                url: "{{action('WhatsAppController@sendMessage', 'customer')}}",
                type: 'POST',
                data: {
                    "issue_id": issueId,
                    "message": message,
                    "sendTo" : sendToStr,
                    "_token": "{{csrf_token()}}",
                   "status": 2
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Message sent successfully!", "Message");
                    $('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
        });
        $(document).on('click', '.quick_category_add', function () {
            $.ajax({
                url: "/add-reply-category",
                type: 'POST',
                data: {
                    name : $('input[name="category_name"]').val(),
                    "_token": "{{csrf_token()}}",
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Category added successfully!", "Message");
                    $('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
            siteHelpers.quickCategoryAdd($(this));
        });
        $(document).on('click', '.quick_comment_add', function () {
            if ($('input[name="quick_comment"]').val() == "") {
                alert("Please Enter New Quick Comment!!");
                return false;
            }
            if ($('select[name="quickCategory"]').val() == "") {
                alert("Please Select Category!!");
                return false;
            }
            var quickCategoryId = $('select[name="quickCategory"]').children("option:selected").data('id');
            var formData = new FormData();
            formData.append("_token", "{{csrf_token()}}");
            formData.append("reply", $('input[name="quick_comment"]').val());
            formData.append("category_id", quickCategoryId);
            formData.append("model", 'Approval Lead');
            $.ajax({
                url: "/reply",
                type: 'POST',
                data : formData,
                data: {
                    name : $('input[name="category_name"]').val(),
                    "_token": "{{csrf_token()}}",
                },
                dataType: "json",
                success: function (response) {
                    toastr["success"]("Category added successfully!", "Message");
                    $('#message_list_' + issueId).append('<li>' + response.message.created_at + " : " + response.message.message + '</li>');
                    $(self).removeAttr('disabled');
                    $(self).val('');
                },
                beforeSend: function () {
                    $(self).attr('disabled', true);
                },
                error: function () {
                    alert('There was an error sending the message...');
                    $(self).removeAttr('disabled', true);
                }
            });
            siteHelpers.quickCategoryAdd($(this));
        });
    </script>
@endsection