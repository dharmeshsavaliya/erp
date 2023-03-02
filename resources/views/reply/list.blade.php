@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Quick Replies List ({{ $replies->total() }})</h2>
        <div class="pull">
            <div class="row" style="margin:10px;">
                <div class="col-12">
                    <form action="{{ route('reply.replyList') }}" method="get" class="search">
                        <div class="row">
                            <div class="col-md-2 pd-sm">
                                {{ Form::select("store_website_id[]", \App\StoreWebsite::pluck('website','id')->toArray(),request('store_website_id'),["class" => "form-control globalSelect2", "multiple", "data-placeholder" => "Select Website"]) }}
                            </div>
                            <!-- <div class="col-md-2 pd-sm">
                                {{ Form::select("category_id", ["" => "-- Select Category/Sub Category --"] + \App\ReplyCategory::pluck('name','id')->toArray(),request('category_id'),["class" => "form-control"]) }}
                            </div> -->
                            <div class="col-md-2 pd-sm">
                                <select class="form-control globalSelect2" style="width:100%" name="parent_category_ids[]" data-placeholder="Search Parent Category By Name.." multiple >
                                    @if ($parentCategory)
                                        @foreach($parentCategory as $key => $parentCategory)
                                            <option value="{{ $parentCategory->id }}" @if(in_array($parentCategory->id, $parent_category)) echo selected @endif>{{ $parentCategory->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2 pd-sm">
                                <select class="form-control globalSelect2" style="width:100%" name="category_ids[]" data-placeholder="Search Category By Name.." multiple >
                                    @if ($category)
                                        @foreach($category as $key => $category)
                                        <option value="{{ $key }}" @if(in_array($key, $category_ids)) echo selected @endif>{{ $category }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2 pd-sm">
                                <select class="form-control globalSelect2" style="width:100%" name="sub_category_ids[]" data-placeholder="Search Sub Category By Name.." multiple >
                                    @if ($subCategory)
                                        @foreach($subCategory as $key => $subCategory)
                                        <option value="{{ $key }}" @if(in_array($key, $sub_category_ids)) echo selected @endif>{{ $subCategory }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-2 pd-sm">
                                <input type="text" name="keyword" placeholder="keyword" class="form-control h-100" value="{{ request()->get('keyword') }}">
                            </div>
                            <div class="col-md-2 pd-sm pl-0 mt-2">
                                 <button type="submit" class="btn btn-image search" onclick="document.getElementById('download').value = 1;">
                                    <img src="{{ asset('images/search.png') }}" alt="Search">
                                </button>
                                 <button type="submit" class="btn btn-primary search push_all_faq">
                                    Push FAQ
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
                    <table class="table table-bordered" style="table-layout: fixed;" id="quick-reply-list">
                        <tr>
                            <th width="5%">ID</th>
                            <th width="12%">Store website</th>
                            <th width="9%">Parent Category</th>
                            <th width="8%">Category </th>
                            <th width="10%">Sub Category</th>
                            <th width="10%">Reply</th>
                            <th width="7%">Model</th>
                            <th width="5%">Intent Id</th>
                            <th width="10%">Updated On</th>
                            <th width="9%">Is Pushed To Watson</th>
                            <th width="5%">Action</th>
                        </tr>
                        @foreach ($replies as $key => $reply)
                            <tr class="quick-website-task-{{ $reply->id }}" data-id="{{ $reply->id }}">
                                <td id="reply_id">{{ $reply->id }}</td>
                                <td class="expand-row" id="reply-store-website" style="word-break: break-all;">
                                    <span class="td-mini-container">
                                        {{ strlen($reply->website) > 10 ? substr($reply->website, 0, 10).'...' : $reply->website }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$reply->website}}
                                    </span>
                                </td>
                                <td class="expand-row" id="reply_category_parent_first" style="word-break: break-all;">
                                     <span class="td-mini-container">
                                        {{ strlen($reply->parent_first) > 10 ? substr($reply->parent_first, 0, 8).'...' : $reply->parent_first }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$reply->parent_first}}
                                    </span>
                                </td>
                                <td class="expand-row" id="reply_category_parent_secound" style="word-break: break-all;">
                                    <span class="td-mini-container">
                                        {{ strlen($reply->parent_secound) > 10 ? substr($reply->parent_secound, 0, 8).'...' : $reply->parent_secound }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$reply->parent_secound}}
                                    </span>
                                </td>
                                <td class="expand-row" id="reply_category_name" style="word-break: break-all;">
                                     <span class="td-mini-container">
                                        {{ strlen($reply->category_name) > 10 ? substr($reply->category_name, 0, 10).'...' : $reply->category_name }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$reply->category_name}}
                                    </span>
                                </td>
                                <td style="cursor:pointer; word-break: break-all;" id="reply_text" class="change-reply-text expand-row" data-id="{{ $reply->id }}" data-message="{{ $reply->reply }}">
                                     <span class="td-mini-container">
                                        {{ strlen($reply->reply) > 10 ? substr($reply->reply, 0, 10).'...' : $reply->reply }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$reply->reply}}
                                    </span>
                                </td>
                                <td class="expand-row" id="reply_model" style="word-break: break-all;">
                                   <span class="td-mini-container">
                                    {{ strlen($reply->model) > 10 ? substr($reply->model, 0, 6).'...' : $reply->model }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$reply->model}}
                                    </span>
                                </td>
                                <td class="expand-row" style="word-break: break-all;">
                                    <span class="td-mini-container">
                                    {{ strlen($reply->intent_id ) > 10 ? substr($reply->intent_id , 0, 10).'...' : $reply->intent_id  }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$reply->intent_id }}
                                    </span>
                                </td>
                                <td class="expand-row" id="reply_model" style="word-break: break-all;">
                                    <span class="td-mini-container">
                                    {{ strlen($reply->created_at ) > 10 ? substr($reply->created_at , 0, 10).'...' : $reply->created_at  }}
                                    </span>
                                    <span class="td-full-container hidden">
                                        {{$reply->created_at }}
                                    </span>
                                </td>
                                <td id="">@if($reply['pushed_to_watson'] == 0) No @else Yes @endif</td>
                                <td class="Website-task"title="">
                                    <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="Showactionbtn('{{$reply->id}}')"><i class="fa fa-arrow-down"></i></button>
                                </td>
                            </tr>
                            <tr class="action-btn-tr-{{$reply->id}} d-none">
                                <td>Action</td>
                                <td id="reply_action"  colspan="10" >
                                    <i class="fa fa-eye show_logs" data-id="{{ $reply->id }}" style="color: #808080;"></i>
                                    @if($reply['pushed_to_watson'] == 0)  <i  class="fa fa-upload push_to_watson" data-id="{{ $reply->id }}" style="color: #808080;"></i> @endif
                                    <i onclick="return confirm('Are you sure you want to delete this record?')" class="fa fa-trash fa-trash-bin-record" data-id="{{ $reply->reply_cat_id }}" style="color: #808080;"></i>
                                    <!-- To push the FAQ Over every website using the API -->
                                    <i class="fa fa-upload  upload_faq" data-id="{{ $reply->id }}" alt="Push To FAQ" style="color: #808080;"></i>

                                    <button type="button" class="btn btn-xs show-reply-history" title="Show Reply Update History" data-id="{{$reply->id}}" data-type="developer"><i class="fa fa-info-circle" style="color: #808080;"></i></button>
                                    <button type="button" title="Flagged for Translate" data-reply_id="{{ $reply->id }}" data-is_flagged="<?php if($reply->is_flagged=='1') { echo '1'; } else { echo '0'; } ?>" onclick="updateTranslateReply(this)" class="btn" style="padding: 0px 1px;">
                                        <?php if($reply->is_flagged == '1') { ?>
                                        <i class="fa fas fa-toggle-on"></i>
                                        <?php } else { ?>
                                        <i class="fa fas fa-toggle-off"></i>
                                        <?php } ?>
                                    </button>
                                </td>
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
            $("#loading-image-preview").show();
        }
      }).done( function(response) {
            $("#loading-image-preview").hide();
            if(response.code == 200) {
                toastr["success"](response.message);
                location.reload();
            }else{
               toastr["error"]('Record is unable to delete!');
            }
      }).fail(function(errObj) {
            $("#loading-image-preview").hide();
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
            $("#loading-image-preview").show();
        }
      }).done( function(response) {
            $("#loading-image-preview").hide();
            if(response.code == 200) {
                toastr["success"](response.message);
                //location.reload();
            }else{
               toastr["error"]('Unable to push!');
            }
      }).fail(function(errObj) {
            $("#loading-image-preview").hide();
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
        beforeSend: function () {
            jQuery("#loading-image-preview").show();
        },
        success: function (data) {
            jQuery("#loading-image-preview").hide();
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
        },
        error: function(er){
            jQuery("#loading-image-preview").hide();
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
        beforeSend: function () {
            jQuery("#loading-image-preview").show();
        },
        success: function (data) {
            jQuery("#loading-image-preview").hide();
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
        },
        error:function(err){
            jQuery("#loading-image-preview").hide();
        }
    });
    $('#reply_logs_modal').modal('show');
});

$(document).on("click",".upload_faq",function() {
    if(!confirm('Are you sure you want to push FAQ?')){
        return false;
    }

    var $this = $(this);
    $.ajax({
        url: "{{ url('push/faq') }}",
        type: 'POST',
        data: {
          _token: "{{ csrf_token() }}",
          id: $this.data("id")
        },
        beforeSend: function() {
            $("#loading-image-preview").show();
        }
      }).done( function(response) {
            $("#loading-image-preview").hide();
            if(response.code == 200) {
                toastr["success"](response.message);
                // location.reload();
            }else{
               toastr["error"]('Something went wrong!');
            }
      }).fail(function(errObj) {
            $("#loading-image-preview").hide();
      });
});


$(document).ready(function(){

    $(document).on("click",".push_all_faq",function(e) {
        e.preventDefault();
        var $this = $(this);
        $.ajax({
            url: "{{ url('push/faq/all') }}",
            type: 'POST',
            data: {
              _token: "{{ csrf_token() }}"
            },
            beforeSend: function() {
                $("#loading-image-preview").show();
            }
          }).done( function(response) {
                $("#loading-image-preview").hide();
                if(response.code == 200) {
                    toastr["success"](response.message);
                    // location.reload();
                }else{
                   toastr["error"]('Something went wrong!');
                }
          }).fail(function(errObj) {
                $("#loading-image-preview").hide();
          });
    });
    
    
})

function updateTranslateReply(ele) {
        let btn = jQuery(ele);
        let reply_id = btn.data('reply_id');
        let is_flagged = btn.data('is_flagged');
        
        
        
        //alert(is_flagged)

        if (confirm(btn.data('is_flagged') == 1 ? 'Are you sure want unflagged this ?' : 'Are you sure want flagged this ?')) {
            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('reply.replytranslate') }}",
                type: 'POST',
                data: {
                    reply_id: reply_id,
                    is_flagged: is_flagged,
                },
                dataType: 'json',
                beforeSend: function () {
                    jQuery("#loading-image-preview").show();
                },
                success: function (res) {
                    if(res.code == 200){
                        toastr["success"](res.message);
                    }
                    else{
                        toastr["error"](res.message);                    
                    }
                    jQuery("#loading-image-preview").hide();
                    
                    if (is_flagged == 1 && res.code == 200) {
                        btn.find('.fa').removeClass('fa-toggle-on fa-toggle-off');
                        btn.find('.fa').addClass('fa-toggle-off');
                        btn.data('is_flagged', is_flagged == 1 ? 0 : 1);
                    }
                    else if(res.code == 200){
                        btn.find('.fa').removeClass('fa-toggle-on fa-toggle-off');
                        btn.find('.fa').addClass('fa-toggle-on');
                        btn.data('is_flagged', is_flagged == 1 ? 0 : 1);
                    }
                },
                error: function (res) {
                    if (res.responseJSON != undefined) {
                        toastr["error"](res.responseJSON.message);
                    }
                    jQuery("#loading-image-preview").hide();
                }
            });
        }
    }

$(document).on('click', '#quick-reply-list .quick-website-task', function() {
    var trclass = $(this).parent()[0].className;
    $("."+trclass+" .quick-website-task").addClass("content-open-on-click");
});
function Showactionbtn(id) {
    $(".action-btn-tr-" + id).toggleClass('d-none')
}

$(document).on('click', '.expand-row', function () {
    var selection = window.getSelection();
    if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
    }
});
</script>
@endsection