
@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Message List | Chatbot')

@section('content')
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    
    <!-- <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
 -->
    <link rel="stylesheet" type="text/css" href="/css/dialog-node-editor.css">
    <style type="text/css">
        .panel-img-shorts {
            width: 80px;
            height: 80px;
            display: inline-block;
        }
        .panel-img-shorts .remove-img {
            display: block;
            float: right;
            width: 15px;
            height: 15px;
        }
    </style>
<div id="common-page-layout">

    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Chat Message List <span class="count-text">0</span></h2>
        </div>
    </div>

    <div class="row ml-2 mr-2">
        <div class="col-lg-12 margin-tb" style="margin-bottom: 10px;">
            <div class="pull-left">
                <div class="form-inline">
                    <form class="form-inline message-search-handler form-search-data" method="get">
                        <div class="row">
                            <div class="col-auto p-0">
                                <div class="form-group mr-2">
                                    <?php echo Form::text("created_at",request("date"),["class"=> "form-control datepicker","placeholder" => "Select Date", "id"=> "created_at"]) ?>
                                </div>
                            </div>

                            <div class="col-auto p-0">
                                <div class="form-group mr-2">
                                    <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control","placeholder" => "Enter keyword", "id"=> "search-keywords"]) ?>
                                </div>
                            </div>

                            <div class="col-auto p-0">
                                <div class="form-group mr-2">
                                    <select class="form-control" name="user_id">
                                        <option value="">Select user</option>
                                        @foreach(\App\User::pluck('name','id')->toArray() as $k => $user)
                                            <option value="{{ $k }}">{{ $user }}</option>
                                        @endforeach    
                                    </select>
                                </div>
                            </div>

                            <div class="col-auto p-0">
                                <div class="form-group mr-2">
                                    <select class="form-control" name="vendor_id">
                                        <option value="">Select vendor</option>
                                            @foreach(\App\Vendor::pluck('name','id')->toArray() as $k => $vendor)
                                                <option value="{{ $k }}">{{ $vendor }}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-auto p-0">
                                <div class="form-group mr-2">
                                    <select class="form-control" name="customer_id">
                                        <option value="">Select customer</option>
                                            @foreach(\App\Customer::pluck('name','id')->toArray() as $k => $customer)
                                                <option value="{{ $k }}">{{ $customer }}</option>
                                            @endforeach
                                    </select>
                                </div>
                            </div>



                            <div class="pull-right">
                                <button type="button" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-secondary btn-search-action">
                                    <img src="/images/search.png" style="cursor: default;">
                                </button>
                            </div>

                        
                            
                        </div>
                    </form>

                </div>
            </div>
            <div class="pull-right">
                <div class="form-inline">
                    
                </div>
            </div>

        </div>
    </div>


    <div class="row ml-2 mr-2">
        <div class="col-md-12">
            <div class="margin-tb custom-table" id="page-view-result">
                <div class="table-responsive mt-3">
                    <table class="table table-bordered" style="table-layout: fixed;">
                        <thead>
                          <tr>
                            <th width="5%">Date</th>
                            <th width="30%">Message</th>
                            <th width="4%">Sender Type</th>
                            <th width="4%">Sender Name</th>
                            <th width="5%">Action</th>
                          </tr>
                        </thead>
                        <tbody id="chatmessagecontent">
                            
                        </tbody>
                    </table>
                     <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="leaf-editor-model" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Are you sure wants to search all keywords? </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary search" data-id="no" data-dismiss="modal">No</button>
        <button type="button" class="btn btn-secondary save-dialog-btn search" data-id="yes">Yes</button>
      </div>
    </div>
  </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>

<div class="common-modal modal" role="dialog">
    <div class="modal-dialog" role="document" style="width: 1000px; max-width: 1000px;">
    </div>  
</div>

@include("custom-chat-message.templates.list-template")

<!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
 -->
<script type="text/javascript" src="{{ asset('/js/jsrender.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/jquery.validate.min.js') }}"></script>
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script type="text/javascript" src="{{ asset('/js/common-helper.js') }}"></script>
<!-- <script type="text/javascript" src="{{ asset('/js/custom_chat_message.js') }}"></script> -->

<script type="text/javascript">
    $(document).on("click",".copy_chat_message", function (){
        var thiss = $(this);
        var remark_text = thiss.data('message');
        copyToClipboard(remark_text);
        /* Alert the copied text */
        toastr['success']("Copied the text: " + remark_text);
    });

    function copyToClipboard(text) {
        var sampleTextarea = document.createElement("textarea");
        document.body.appendChild(sampleTextarea);
        sampleTextarea.value = text; //save main text in it
        sampleTextarea.select(); //select textarea contenrs
        document.execCommand("copy");
        document.body.removeChild(sampleTextarea);
    }

    $(document).on('submit','.form-search-data',function(e){
        e.preventDefault();
        var keyword = $("#search-keywords").val();
        if(keyword != null) {
            if(keyword.indexOf(",") != -1 || keyword.indexOf("/") != -1) {
                $("#leaf-editor-model").modal('show');
            } else {
                page = 0;
                $("#page-view-result #chatmessagecontent").html('');
                loadMore('no')
            }
        }

    });
    $(document).on("click",".search", function(e) {
        e.preventDefault();
        page = 0;
        $("#page-view-result #chatmessagecontent").html('');
        var _this = $(this);
        var search = _this.attr('data-id');
        loadMore(search)
    });
    // page.init({
    //  bodyView : $("#common-page-layout"),
    //  baseUrl : "//echo url("/"); ?>"
    // });

var isLoading = false;
var page = 0;

function loadMore(search = null) {
    if (isLoading)
        return;
    isLoading = true;
    type = $("#tasktype").val();
    var $loader = $('.infinite-scroll-products-loader');
    page = page + 1;
    $.ajax({
        url: "/custom-chat-message/records?page="+page+"&search="+search,
        type: 'GET',
        data: $('.form-search-data').serialize(),
        beforeSend: function() {
            $loader.show();
        },
        success: function (response) {
            $loader.hide();
            var addProductTpl = $.templates("#template-result-block");
            var tplHtml       = addProductTpl.render(response);
            $(".count-text").html("("+response.total+")");
            $("#page-view-result #chatmessagecontent").append(tplHtml);
            isLoading = false;
            $("#leaf-editor-model").modal('hide');
        },
        error: function () {
            $loader.hide();
            isLoading = false;
        }
    });
}

        
$(document).ready(function () {
    loadMore();
    $(window).scroll(function() {
        if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
            loadMore();
        }
    });
    $("#common-page-layout").on("click",".btn-search-action",function(e) {
        e.preventDefault();
        page = 0;
        $("#page-view-result #chatmessagecontent").html('');
        loadMore()
    });            
});

$( function() {
    $( ".datepicker" ).datepicker({
        dateFormat: 'dd-M-yy'
    });
} );
</script>


@endsection