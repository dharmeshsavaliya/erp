@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Quick Replies List</h2>
        <div class="pull-left">
            <div class="row">
                <div class="col-md-12 ml-sm-4">            
                    <form action="{{ route('reply.replyList') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-6 pd-sm">
                                {{ Form::select("store_website_id", ["" => "-- Select Website --"] + \App\StoreWebsite::pluck('website','id')->toArray(),request('store_website_id'),["class" => "form-control"]) }}
                            </div>
                            <div class="col-md-5 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control" value="{{ request()->get('keyword') }}">
                            </div>
                            

                            <div class="col-md-1 pd-sm">
                                 <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<div class="tab-content ">
    <!-- Pending task div start -->
    <div class="tab-pane active" id="1">
        <div class="row" style="margin:10px;"> 
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-bordered" style="table-layout: fixed;">
                        <tr>
                            <th width="2%">ID</th>
                            <th width="8%">Store website</th>
                            <th width="10%">Category</th>
                            <th width="15%">Reply</th>
                            <th width="7%">Model</th>
                            <th width="7%">Updated On</th>
                            <th width="10%">Is Pushed To Watson</th>
                            <th width="4%">Action</th>
                        </tr>
                        @foreach ($replies as $key => $reply)
                            <tr>
                                <td id="reply_id">{{ $reply->id }}</td>
                                <td class="Website-task" id="reply-store-website">{{ $reply->website }}</td>
                                <td class="Website-task" id="reply_category_name">{{ $reply->parentList() }} > {{ $reply->category_name }}</td>
                                <td style="cursor:pointer;" id="reply_text" class="change-reply-text Website-task" data-id="{{ $reply->id }}" data-message="{{ $reply->reply }}">{{ $reply->reply }}</td>
                                <td class="Website-task" id="reply_model">{{ $reply->model }}</td>
                                <td id="reply_model">{{ $reply->created_at }}</td>
                                <td id="">@if($reply['pushed_to_watson'] == 0) No @else Yes @endif</td>
                                <td id="reply_action">
                                    <button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-reply-history" title="Show Reply Update History" data-id="{{$reply->id}}" data-type="developer"><i class="fa fa-info-circle"></i></button>
                                    <i class="fa fa-eye show_logs" data-id="{{ $reply->id }}"></i>
                                  @if($reply['pushed_to_watson'] == 0)  <i  class="fa fa-upload push_to_watson" data-id="{{ $reply->id }}"></i> @endif
                                    <i onclick="return confirm('Are you sure you want to delete this record?')" class="fa fa-trash fa-trash-bin-record" data-id="{{ $reply->reply_cat_id }}"></i></td>
                            </tr>
                        @endforeach
                    </table>
                </div>
                    {!! $replies->appends(request()->except('page'))->links() !!}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="reply-update-form-modal" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
             <form method="POST" action="{{route('reply.replyUpdate')}}" id="reply-update-form" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Update reply</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <input type="hidden" name="id" id="reply-update-model-text-id">
                            <textarea id="reply-update-model-text-reply" class="form-control" name="reply"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="create-camp-btn" class="btn btn-secondary">Update</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="reply_history_modal">
    <div class="modal-dialog" role="document"style="width: 60%; max-width: 100%;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Tracked time history</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="reply_history_div">
                        <table class="table" style="table-layout:fixed;">
                            <thead>
                                <tr>
                                    <th width="11%">User Name</th>
                                    <th width="60%">Last Message</th>
                                    <th width="17%">Updated Time</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<div class="modal" tabindex="-1" role="dialog" id="reply_logs_modal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Watson push Logs</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="reply_logs_div">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Request</th>
                                    <th>Response</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

$(document).on("click",".fa-trash-bin-record",function() {
    var $this = $(this);
    $.ajax({
        url: "{{ url('reply-list/delete') }}",
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          id: $this.data("id")
        },
        beforeSend: function() {
            $("#loading-image").show();
        }
      }).done( function(response) {
            $("#loading-image").hide();
            if(response.code == 200) {
                toastr["success"](response.message);
                location.reload();
            }else{
               toastr["error"]('Record is unable to delete!');
            }
      }).fail(function(errObj) {
            $("#loading-image").hide();
      });
});

$(document).on("click",".push_to_watson",function() {
    var $this = $(this);
    $.ajax({
        url: "{{ url('push-reply-to-watson') }}",
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          id: $this.data("id")
        },
        beforeSend: function() {
            $("#loading-image").show();
        }
      }).done( function(response) {
            $("#loading-image").hide();
            if(response.code == 200) {
                toastr["success"](response.message);
                //location.reload();
            }else{
               toastr["error"]('Unable to push!');
            }
      }).fail(function(errObj) {
            $("#loading-image").hide();
      });
});

$(document).on("click",".change-reply-text",function(e) {
    e.preventDefault();
    var $this = $(this);
    $("#reply-update-model-text-id").val($this.data("id"));
    $("#reply-update-model-text-reply").val($this.data("message"));
    $("#reply-update-form-modal").modal("show");
});

$(document).on('click', '.show-reply-history', function() {
    var issueId = $(this).data('id');
    $('#reply_history_div table tbody').html('');
    $.ajax({
        url: "{{ route('reply.replyhistory') }}",
        data: {id: issueId},
        success: function (data) {
            if(data != 'error') {
                $.each(data.histories, function(i, item) {
                    $('#reply_history_div table tbody').append(
                        '<tr>\
                        <td class="Website-task">'+ ((item['name'] != null) ? item['name'] : '') +'</td>\
                        <td class="Website-task">'+ ((item['last_message'] != null) ? item['last_message'] : '') +'</td>\
                        <td>'+ ((item['created_at'] != null) ? item['created_at'] : '') +'</td>\
                        </tr>'
                        );
                });
            }
        }
    });
    $('#reply_history_modal').modal('show');
});

$(document).on('click', '.show_logs', function() {
    var issueId = $(this).data('id');
    $('#reply_logs_div table tbody').html('');
    $.ajax({
        url: "{{ route('reply.replylogs') }}",
        data: {id: issueId},
        success: function (data) {
            if(data != 'error') {
                $.each(data.logs, function(i, item) {
                    $('#reply_logs_div table tbody').append(
                        '<tr>\
                        <td>'+ ((item['created_at'] != null) ? item['created_at'] : '') +'</td>\
                        <td>'+ ((item['request'] != null) ? item['request'] : '') +'</td>\
                        <td>'+ ((item['response'] != null) ? item['response'] : '') +'</td>\
                        </tr>'
                        );
                });
            }
        }
    });
    $('#reply_logs_modal').modal('show');
});
</script>
@endsection