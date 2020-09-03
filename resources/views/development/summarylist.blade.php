@extends('layouts.app')

@section('favicon' , 'vendor.png')

@section('title', 'Vendor Info')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link href="https://gitcdn.github.io/bootstrap-toggle/2.2.2/css/bootstrap-toggle.min.css" rel="stylesheet">
    <style type="text/css">
    .numberSend {
          width: 160px;
          background-color: transparent;
          color: transparent;
          text-align: center;
          border-radius: 6px;
          position: absolute;
          z-index: 1;
          left: 19%;
          margin-left: -80px;
          display: none;
    }
 .input-sm{
    width: 60px;
    }

    #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    .cls_filter_inputbox{
        width: 14%;
        text-align: center;
    }
    .message-chat-txt {
        color: #333 !important;
    }
    .cls_remove_leftpadding{
        padding-left: 0px !important;
    }
    .cls_remove_rightpadding{
        padding-right: 0px !important;
    }
    .cls_action_btn .btn{
        padding: 6px 12px;
    } 
    .cls_remove_allpadding{
        padding-left: 0px !important;
        padding-right: 0px !important;   
    }
    .cls_quick_message{
        width: 100% !important;
        height: 35px !important;
    }
    .cls_filter_box{
        width: 100%;
    }
    .select2-selection.select2-selection--single{
        height: 35px;
    }
    .cls_action_btn .btn-image img {
        width: 13px !important;
    }
    .cls_action_btn .btn {
        padding: 6px 2px;
    }
    .cls_textarea_subbox{
        width: 100%;
    }
    .btn.btn-image.delete_quick_comment {
        padding: 4px;
    }
    .vendor-update-status-icon {
        padding: 0px;
    }
    .cls_commu_his{
        width: 100% !important;
    }
    .vendor-update-status-icon{
        margin-top: -7px;
    }
    .clsphonebox .btn.btn-image{
        padding: 5px;
    }
    .clsphonebox {
        margin-top: -8px;
    }
    .send-message1{
        padding: 0px 10px;
    }
    .load-communication-modal{
        margin-top: -6px;
        margin-left: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height: 35px;
    }
    .select2-selection__arrow{
        display: none;
    }
    .cls_mesg_box{
        margin-top: -7px;
        font-size: 12px;
    }

  </style>
@endsection

@section('large_content')
     <div id="myDiv">
       <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
   </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <?php $base_url = URL::to('/');?>
            <h2 class="page-heading">Summary Info</h2>
            <div class="pull-left cls_filter_box">
                <form class="form-inline" action="{{ route('development.summarylist') }}" method="GET">
                    
                   
                    <div class="form-group" style="margin-left: 50px;">
                    <label for="with_archived">Issue Id / Subject</label>
                         <input type="text" name="subject" id="subject_query" placeholder="Issue Id / Subject" class="form-control-mg" value="{{ (!empty(app('request')->input('subject'))  ? app('request')->input('subject') : '') }}">
                    </div>

                    <div class="form-group ml-3 cls_filter_inputbox" style="margin-left: 10px;">
                        <label for="with_archived">Module</label>
                        
                        <select class="form-control" name="module_id" id="module_id">
                             <option value>Select a Module</option>
                     @foreach($modules as $module)
                    <option {{ $request->get('module') == $module->id ? 'selected' : '' }} value="{{ $module->id }}">{{ $module->name }}</option>
                @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-left: 50px;">
                    <label for="with_archived">DEVELOPER</label>
                         <input type="text" name="developer" id="developer" placeholder="DEVELOPER" class="form-control-mg" value="">
                    </div>
                    <div class="form-group" style="margin-left: 50px;">
                        <label for="with_archived">Status</label>
                        <?php echo Form::select("task_status[]",$statusList,request()->get('task_status', ['In Progress']),["class" => "form-control multiselect","multiple" => true]); ?>
                    </div>
                    
                    
                    <button type="submit" style="margin-top: 20px;padding: 5px;" class="btn btn-image"><img src="<?php echo $base_url;?>/images/filter.png"/></button>
                </form>
            </div>
        </div>
       
    </div>

    @include('partials.flash_messages')

    <div class="infinite-scroll">
    <div class="table-responsive mt-3">
        <table class="table table-bordered" id="vendor-table">
            <thead>
            <tr>
                <th width="5%">ID</a></th>
                <th width="7%">MODULE</th>
                <th width="7%">DEVELOPER</th>
                <th width="25%">Communication</th>
                {{-- <th width="10%">Social handle</th>
                <th width="10%">Website</th> --}}
               
                <th width="7%">Status</th>
            </tr>
            </thead>

            <tbody id="vendor-body">
                 <?php
        $isReviwerLikeAdmin =  auth()->user()->isReviwerLikeAdmin();
        $userID =  Auth::user()->id;
    ?>
    @foreach ($issues as $key => $issue)
        @if($isReviwerLikeAdmin)
            @include("development.partials.summarydata")
        @elseif($issue->created_by == $userID || $issue->master_user_id == $userID || $issue->assigned_to == $userID)
            @include("development.partials.developer-row-view")
        @endif
    @endforeach


                     </tbody>
 <?php echo $issues->appends(request()->except("page"))->links(); ?>
        </table>
    </div>

   </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="{{asset('js/zoom-meetings.js')}}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://gitcdn.github.io/bootstrap-toggle/2.2.2/js/bootstrap-toggle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script>

    <script type="text/javascript">



        var vendorToRemind = null;
        $('#vendor-search').select2({
            tags: true,
            width : '100%',
            ajax: {
                url: BASE_URL+'/vendor-search',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    for (var i in data) {
                        data[i].id = data[i].name ? data[i].name : data[i].text;
                    }
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search by name',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {

                if (customer.name) {
                    //return "<p> <b>Id:</b> " + customer.id + (customer.name ? " <b>Name:</b> " + customer.name : "") + (customer.phone ? " <b>Phone:</b> " + customer.phone : "") + "</p>";
                    return "<p>" + customer.name +"</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.name,

        });

        var vendorToRemind = null;
        $('#vendor-phone-number').select2({
            tags: true,
            width : '100%',
            ajax: {
                url: BASE_URL+'/vendor-search-phone',
                dataType: 'json',
                delay: 750,
                data: function (params) {
                    return {
                        q: params.term, // search term
                    };
                },
                processResults: function (data, params) {
                    for (var i in data) {
                        data[i].id = data[i].phone ? data[i].phone : data[i].text;
                    }
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
            },
            placeholder: 'Search by phone number',
            escapeMarkup: function (markup) {
                return markup;
            },
            minimumInputLength: 1,
            templateResult: function (customer) {

                if (customer.name) {
                    return "<p style='color:#BABABA;'>"+ customer.phone+ "</p>";
                }
            },
            templateSelection: (customer) => customer.text || customer.phone,

        });

        $(document).on('click', '.emailToAllModal', function () {
            var select_vendor = [];
            $('.select_vendor').each(function(){
                if ($(this).prop("checked")) {
                    select_vendor.push($(this).val());
                }
            });

            if (select_vendor.length === 0) {
                alert('Please Select vendors!!');
                return false;
            }

            $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val(select_vendor.join());

            $('#emailToAllModal').modal("show");

        });

        $(document).on('click', '.send-email-to-vender', function () {
            $('#emailToAllModal').find('form').find('input[name="vendor_ids"]').val($(this).data('id'));
            $('#emailToAllModal').modal("show");
        });

        $(document).on('click', '.set-reminder', function () {
            let vendorId = $(this).data('id');
            let frequency = $(this).data('frequency');
            let message = $(this).data('reminder_message');
            let reminder_from = $(this).data('reminder_from');
            let reminder_last_reply = $(this).data('reminder_last_reply');

            $('#frequency').val(frequency);
            $('#reminder_message').val(message);
            $("#reminderModal").find("#reminder_from").val(reminder_from);
            if(reminder_last_reply == 1) {
                $("#reminderModal").find("#reminder_last_reply").prop("checked",true);
            }else{
                $("#reminderModal").find("#reminder_last_reply_no").prop("checked",true);
            }
            vendorToRemind = vendorId;
        });

        $(document).on('click', '.save-reminder', function () {
            var reminderModal = $("#reminderModal");
            let frequency = $('#frequency').val();
            let message = $('#reminder_message').val();
            let reminder_from = reminderModal.find("#reminder_from").val();
            let reminder_last_reply = (reminderModal.find('#reminder_last_reply').is(":checked")) ? 1 : 0;

            $.ajax({
                url: "{{action('VendorController@updateReminder')}}",
                type: 'POST',
                success: function () {
                    toastr['success']('Reminder updated successfully!');
                },
                data: {
                    vendor_id: vendorToRemind,
                    frequency: frequency,
                    message: message,
                    reminder_from: reminder_from,
                    reminder_last_reply: reminder_last_reply,
                    _token: "{{ csrf_token() }}"
                }
            });
        });

        $(document).on('click', '.edit-vendor', function () {
            var vendor = $(this).data('vendor');
            var url = "{{ url('vendors') }}/" + vendor.id;

            $('#vendorEditModal form').attr('action', url);
            $('#vendor_category option[value="' + vendor.category_id + '"]').attr('selected', true);
            $('#vendor_name').val(vendor.name);
            $('#vendor_address').val(vendor.address);
            $('#vendor_phone').val(vendor.phone);
            $('#vendor_email').val(vendor.email);
            $('#vendor_social_handle').val(vendor.social_handle);
            $('#vendor_website').val(vendor.website);
            $('#vendor_login').val(vendor.login);
            $('#vendor_password').val(vendor.password);
            $('#vendor_gst').val(vendor.gst);
            $('#vendor_account_name').val(vendor.account_name);
            $('#vendor_account_iban').val(vendor.account_iban);
            $('#vendor_account_swift').val(vendor.account_swift);
        });

        $(document).on('click', '.create-agent', function () {
            var id = $(this).data('id');

            $('#agent_vendor_id').val(id);
        });

        $(document).on('click', '.edit-agent-button', function () {
            var agent = $(this).data('agent');
            var url = "{{ url('agent') }}/" + agent.id;

            $('#editAgentModal form').attr('action', url);
            $('#agent_name').val(agent.name);
            $('#agent_address').val(agent.address);
            $('#agent_phone').val(agent.phone);
            $('#agent_email').val(agent.email);
        });

        $(document).on('click', '.make-remark', function (e) {
            e.preventDefault();

            var id = $(this).data('id');
            $('#add-remark input[name="id"]').val(id);

            $.ajax({
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.gettaskremark') }}',
                data: {
                    id: id,
                    module_type: "vendor"
                },
            }).done(response => {
                var html = '';

                $.each(response, function (index, value) {
                    html += ' <p> ' + value.remark + ' <br> <small>By ' + value.user_name + ' updated on ' + moment(value.created_at).format('DD-M H:mm') + ' </small></p>';
                    html + "<hr>";
                }); 
                $("#makeRemarkModal").find('#remark-list').html(html);
            });
        });

        $('#addRemarkButton').on('click', function () {
            var id = $('#add-remark input[name="id"]').val();
            var remark = $('#add-remark').find('textarea[name="remark"]').val();

            $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('task.addRemark') }}',
                data: {
                    id: id,
                    remark: remark,
                    module_type: 'vendor'
                },
            }).done(response => {
                $('#add-remark').find('textarea[name="remark"]').val('');

                var html = ' <p> ' + remark + ' <br> <small>By You updated on ' + moment().format('DD-M H:mm') + ' </small></p>';

                $("#makeRemarkModal").find('#remark-list').append(html);
            }).fail(function (response) {
                console.log(response);

                alert('Could not fetch remarks');
            });
        });

        $(document).on('click', '.expand-row', function () {
            var selection = window.getSelection();
            if (selection.toString().length === 0) {
                $(this).find('.td-mini-container').toggleClass('hidden');
                $(this).find('.td-full-container').toggleClass('hidden');
            }
        });

        $(document).on('click', '.load-email-modal', function () {
            var id = $(this).data('id');
             $.ajax({
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: '{{ route('vendors.email') }}',
                data: {
                    id: id
                },
            }).done(function (response) {
                var html = '<div class="speech-wrapper">';
                response.forEach(function (message) {
                    var content = '';
                    content += 'To : '+message.to+'<br>';
                    content += 'From : '+message.from+'<br>';
                    if (message.cc) {
                        content += 'CC : '+message.cc+'<br>';
                    }
                    if (message.bcc) {
                        content += 'BCC : '+message.bcc+'<br>';
                    }
                    content += 'Subject : '+message.subject+'<br>';
                    content += 'Message : '+message.message+'<br>';
                    if (message.attachment.length) {
                        content += 'Attachment : ';
                    }
                    for (var i = 0; i < message.attachment.length; i++) {
                        var imageUrl = message.attachment[i];
                        imageUrl = imageUrl.trim();

                        // Set empty imgSrc
                        var imgSrc = '';

                        // Set image type
                        var imageType = imageUrl.substr(imageUrl.length - 4).toLowerCase();

                        // Set correct icon/image
                        if (imageType == '.jpg' || imageType == 'jpeg') {
                            imgSrc = imageUrl;
                        } else if (imageType == '.png') {
                            imgSrc = imageUrl;
                        } else if (imageType == '.gif') {
                            imgSrc = imageUrl;
                        } else if (imageType == 'docx' || imageType == '.doc') {
                            imgSrc = '/images/icon-word.svg';
                        } else if (imageType == '.xlsx' || imageType == '.xls' || imageType == '.csv') {
                            imgSrc = '/images/icon-excel.svg';
                        } else if (imageType == '.pdf') {
                            imgSrc = '/images/icon-pdf.svg';
                        } else if (imageType == '.zip' || imageType == '.tgz' || imageType == 'r.gz') {
                            imgSrc = '/images/icon-zip.svg';
                        } else {
                            imgSrc = '/images/icon-file-unknown.svg';
                        }

                        // Set media
                        if (imgSrc != '') {
                            content += '<div class="col-4"><a href="' + message.attachment[i] + '" target="_blank"><label class="label-attached-img" for="cb1_' + i + '"><img src="' + imgSrc + '" style="max-width: 100%;"></label></a></div>';
                        }
                    }
                    if (message.inout == 'in') {
                        html += '<div class="bubble"><div class="txt"><p class="name"></p><p class="message">' + content + '</p><br/><span class="timestamp">' + message.created_at.date.substr(0, 19) + '</span></div><div class="bubble-arrow"></div></div>';
                    } else if (message.inout == 'out') {
                        html += '<div class="bubble alt"><div class="txt"><p class="name alt"></p><p class="message">' + content + '</p><br/><span class="timestamp">' + message.created_at.date.substr(0, 19) + '</span></div> <div class="bubble-arrow alt"></div></div>';
                    }
                });

                html += '</div>';

                $("#email-list-history").find(".modal-body").html(html);
                $("#email-list-history").modal("show");
            }).fail(function (response) {
                console.log(response);

                alert('Could not load email');
            });
        });
        $(document).on("keyup", '.search_email_pop', function() {
            var value = $(this).val().toLowerCase();
            $(".speech-wrapper .bubble").filter(function() {
                $(this).toggle($(this).find('.message').text().toLowerCase().indexOf(value) > -1)
            });
        });
        $(document).on('click', '.send-message', function () {
            var thiss = $(this);
            var data = new FormData();
            var vendor_id = $(this).data('vendorid');
            var message = $(this).siblings('input').val();

            data.append("vendor_id", vendor_id);
            data.append("message", message);
            data.append("status", 1);

            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/vendor',
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function (response) {
                        thiss.closest('tr').find('.chat_messages').html(thiss.siblings('input').val());
                        $(thiss).siblings('input').val('');

                        $(thiss).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(thiss).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
        $(document).on('click', '.send-message1', function () {
            var thiss = $(this);
            var data = new FormData();
            var vendor_id = $(this).data('vendorid');
            var message = $("#messageid_"+vendor_id).val();
            data.append("vendor_id", vendor_id);
            data.append("message", message);
            data.append("status", 1);

            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: BASE_URL+'/whatsapp/sendMessage/vendor',
                        type: 'POST',
                        "dataType": 'json',           // what to expect back from the PHP script, if anything
                        "cache": false,
                        "contentType": false,
                        "processData": false,
                        "data": data,
                        beforeSend: function () {
                            $(thiss).attr('disabled', true);
                        }
                    }).done(function (response) {
                        //thiss.closest('tr').find('.message-chat-txt').html(thiss.siblings('textarea').val());
                        $("#message-chat-txt-"+vendor_id).html(message);
                        $("#messageid_"+vendor_id).val('');
                        
                        $(thiss).attr('disabled', false);
                    }).fail(function (errObj) {
                        $(thiss).attr('disabled', false);

                        alert("Could not send message");
                        console.log(errObj);
                    });
                }
            } else {
                alert('Please enter a message first');
            }
        });
        $(document).on('change', '.update-category-user', function () {
            let catId = $(this).attr('data-categoryId');
            let userId = $(this).val();

            $.ajax({
                url: '{{ action('VendorController@assignUserToCategory') }}',
                data: {
                    user_id: userId,
                    category_id: catId
                },
                success: function (response) {
                    toastr['success']('User assigned to category completely!')
                }
            });

        });

        $(document).on('click', '.add-cc', function (e) {
            e.preventDefault();

            if ($('#cc-label').is(':hidden')) {
                $('#cc-label').fadeIn();
            }

            var el = `<div class="row cc-input">
            <div class="col-md-10">
                <input type="text" name="cc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image cc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

            $('#cc-list').append(el);
        });

        $(document).on('click', '.cc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.cc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#cc-label').fadeOut();
                }
            });
        });

        // bcc

        $(document).on('click', '.add-bcc', function (e) {
            e.preventDefault();

            if ($('#bcc-label').is(':hidden')) {
                $('#bcc-label').fadeIn();
            }

            var el = `<div class="row bcc-input">
            <div class="col-md-10">
                <input type="text" name="bcc[]" class="form-control mb-3">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-image bcc-delete-button"><img src="/images/delete.png"></button>
            </div>
        </div>`;

            $('#bcc-list').append(el);
        });

        $(document).on('click', '.bcc-delete-button', function (e) {
            e.preventDefault();
            var parent = $(this).parent().parent();

            parent.hide(300, function () {
                parent.remove();
                var n = 0;

                $('.bcc-input').each(function () {
                    n++;
                });

                if (n == 0) {
                    $('#bcc-label').fadeOut();
                }
            });
        });

        $(document).on('click', '.block-twilio', function () {
            var vendor_id = $(this).data('id');
            var thiss = $(this);

            $.ajax({
                type: "POST",
                url: "{{ route('vendors.block') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: vendor_id
                },
                beforeSend: function () {
                    $(thiss).text('Blocking...');
                }
            }).done(function (response) {
                if (response.is_blocked == 1) {
                    $(thiss).html('<img src="/images/blocked-twilio.png" />');
                } else {
                    $(thiss).html('<img src="/images/unblocked-twilio.png" />');
                }
            }).fail(function (response) {
                $(thiss).html('<img src="/images/unblocked-twilio.png" />');

                alert('Could not block customer!');

                console.log(response);
            });
        });

         $(document).on('click', '.call-select', function() {
        var id = $(this).data('id');
        $('#show'+id).toggle();
        console.log('#show'+id);
      });

          $(document).ready(function() {
              src = "{{ route('vendors.index') }}";
              $(".search").autocomplete({
                  source: function (request, response) {
                      id = $('#id').val();
                      name = $('#name').val();
                      email = $('#email').val();
                      phone = $('#phone').val();
                      address = $('#address').val();
                      category = $('#category').val();

                      $.ajax({
                          url: src,
                          dataType: "json",
                          data: {
                              id: id,
                              name: name,
                              phone: phone,
                              email: email,
                              address: address,
                              category: category,
                          },
                          beforeSend: function () {
                              $("#loading-image").show();
                          },

                      }).done(function (data) {
                          $("#loading-image").hide();
                          console.log(data);
                          $("#vendor-table tbody").empty().html(data.tbody);
                          if (data.links.length > 10) {
                              $('ul.pagination').replaceWith(data.links);
                          } else {
                              $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                          }
                          $(".select2-quick-reply").select2({});

                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          alert('No response from server');
                      });
                  },
                  minLength: 1,

              });


              $(document).ready(function () {
                  src = "{{ route('vendors.index') }}";
                  $("#search_id").autocomplete({
                      source: function (request, response) {
                          $.ajax({
                              url: src,
                              dataType: "json",
                              data: {
                                  term: request.term
                              },
                              beforeSend: function () {
                                  $("#loading-image").show();
                              },

                          }).done(function (data) {
                              $("#loading-image").hide();
                              $("#vendor-table tbody").empty().html(data.tbody);
                              if (data.links.length > 10) {
                                  $('ul.pagination').replaceWith(data.links);
                              } else {
                                  $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
                              }
                              $(".select2-quick-reply").select2({});

                          }).fail(function (jqXHR, ajaxOptions, thrownError) {
                              alert('No response from server');
                          });
                      },
                      minLength: 1,

                  });
              });

              $(document).on("change", ".quickComment", function (e) {

                  var message = $(this).val();

                  if ($.isNumeric(message) == false) {
                      $.ajax({
                          headers: {
                              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                          },
                          url: BASE_URL+"/vendors/reply/add",
                          dataType: "json",
                          method: "POST",
                          data: {reply: message}
                      }).done(function (data) {

                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          alert('No response from server');
                      });
                  }
                  $(this).closest("td").find(".quick-message-field").val($(this).find("option:selected").text());

              });

              $(".select2-quick-reply").select2({tags: true});

              $(document).on("click", ".delete_quick_comment", function (e) {
                  var deleteAuto = $(this).closest(".d-flex").find(".quickComment").find("option:selected").val();
                  if (typeof deleteAuto != "undefined") {
                      $.ajax({
                          headers: {
                              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                          },
                          url: BASE_URL+"/vendors/reply/delete",
                          dataType: "json",
                          method: "GET",
                          data: {id: deleteAuto}
                      }).done(function (data) {
                          if (data.code == 200) {
                              $(".quickComment").empty();
                              $.each(data.data, function (k, v) {
                                  $(".quickComment").append("<option value='" + k + "'>" + v + "</option>");
                              });
                              $(".quickComment").select2({tags: true});
                          }

                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          alert('No response from server');
                      });
                  }
              });
          });

          function createUserFromVendor(id, email) {
                $('#vendor_id').attr('data-id', id);
                if (email) {
                    $('#createUser').attr('data-email', email);
                }
                $('#createUser').modal('show');
            }

            $('#createUser').on('hidden.bs.modal', function() {
                $('#createUser').removeAttr('data-email');
            })

         $(document).on("click", "#vendor_id", function () {
            $('#createUser').modal('hide');
            id = $(this).attr('data-id');
                $.ajax({
                          headers: {
                              'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                          },
                          url: BASE_URL+"/vendors/create-user",
                          dataType: "json",
                          method: "POST",
                          data: {id: id},
                          beforeSend: function () {
                              $("#loading-image").show();
                          },
                      }).done(function (data) {
                        $("#loading-image").hide();
                          if (data.code == 200) {
                              alert(data.data);
                          }

                      }).fail(function (jqXHR, ajaxOptions, thrownError) {
                          $("#loading-image").hide(); 
                          alert('No response from server');
                      });
          });

          function inviteGithub() {
            $('#createUser').modal('hide');
            const email = $('#createUser').attr('data-email');

            $.ajax({
                type: "POST",
                url: "/vendors/inviteGithub",
                data: {
                    _token: "{{ csrf_token() }}",
                    email
                }
            })
            .done(function(data){
                alert(data.message);
            })
            .fail(function(error) {
                alert(error.responseJSON.message);
            });

            console.log(email);
        }

        function inviteHubstaff() {
            $('#createUser').modal('hide');
            const email = $('#createUser').attr('data-email');
            console.log(email);

            $.ajax({
                type: "POST",
                url: BASE_URL+"/vendors/inviteHubstaff",
                data: {
                    _token: "{{ csrf_token() }}",
                    email
                }
            })
            .done(function(data){
                alert(data.message);
            })
            .fail(function(error) {
                alert(error.responseJSON.message);
            })
        }

        $('#reminder_from').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
        });

        $(document).on("change",".vendor-update-status",function(){
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: BASE_URL +"vendors/change-status",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: $this.data("id"),
                    status : $this.prop('checked')
                }
            }).done(function(data){
                if(data.code == 200) {
                    toastr["success"](data.message);
                }
            }).fail(function(error) {
                
            })
        });
        $(document).on("click",".vendor-update-status-icon",function(){
            var $this = $(this);
            var vendor_id = $(this).attr("data-id");
            var hdn_vendorstatus = $("#hdn_vendorstatus_"+vendor_id).val();
            $.ajax({
                type: "POST",
                url: BASE_URL +"vendors/change-status",
                data: {
                    _token: "{{ csrf_token() }}",
                    vendor_id: $this.data("id"),
                    status : hdn_vendorstatus
                }
            }).done(function(data){
                if(data.code == 200) {
                    //toastr["success"](data.message);
                    if(hdn_vendorstatus == "true")
                    {
                        var img_url = BASE_URL + 'images/do-disturb.png';
                        $("#btn_vendorstatus_"+vendor_id).html('<img src="'+img_url+'" />');
                        $("#btn_vendorstatus_"+vendor_id).attr("title","On");
                        $("#hdn_vendorstatus_"+vendor_id).val('false');    
                    }
                    else
                    {
                        var img_url = BASE_URL + 'images/do-not-disturb.png';
                        $("#btn_vendorstatus_"+vendor_id).html('<img src="'+img_url+'" />');  
                        $("#btn_vendorstatus_"+vendor_id).attr("title","Off");
                        $("#hdn_vendorstatus_"+vendor_id).val('true');    
                    }
                    
                }
            }).fail(function(error) {
                
            })
        });

    $('ul.pagination').hide();
    $('.infinite-scroll').jscroll({
        autoTrigger: true,
        // debug: true,
        loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
        padding: 20,
        nextSelector: '.pagination li.active + li a',
        contentSelector: 'div.infinite-scroll',
        callback: function () {
            $('ul.pagination').first().remove();
            $('ul.pagination').hide();
        }
    });

    $(document).on('click', '.create_broadcast', function () {
        var vendors = [];
        $(".select_vendor").each(function () {
            if ($(this).prop("checked") == true) {
                vendors.push($(this).val());
            }
        });
        if (vendors.length == 0) {
            alert('Please select vendor');
            return false;
        }
        $("#create_broadcast").modal("show");
    });

    $("#send_message").submit(function (e) {
        e.preventDefault();
        var vendors = [];
        $(".select_vendor").each(function () {
            if ($(this).prop("checked") == true) {
                vendors.push($(this).val());
            }
        });
        if (vendors.length == 0) {
            alert('Please select vendor');
            return false;
        }

        if ($("#send_message").find("#message_to_all_field").val() == "") {
            alert('Please type message ');
            return false;
        }

        $.ajax({
            type: "POST",
            url: "{{ route('vendors/send/message') }}",
            data: {
                _token: "{{ csrf_token() }}",
                message: $("#send_message").find("#message_to_all_field").val(),
                vendors: vendors
            }
        }).done(function () {
            window.location.reload();
        }).fail(function (response) {
            $(thiss).text('No');

            alert('Could not say No!');
            console.log(response);
        });
    });
    </script>
@endsection
