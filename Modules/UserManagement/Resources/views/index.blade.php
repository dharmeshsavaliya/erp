@extends('layouts.app')
@section('favicon' , 'user-management.png')


@section('large_content')
<style type="text/css">
    .preview-category input.form-control {
      width: auto;
    }
</style>

<style>
    #payment-table_filter {
        text-align: right;
    }

    .activity-container {
        margin-top: 3px;
    }

    .elastic {
        transition: height 0.5s;
    }

    .activity-table-wrapper {
        position: absolute;
        width: calc(100% - 50px);
        max-height: 500px;
        overflow-y: auto;
    }

    .dropdown-wrapper {
        position: relative;
    }

    .dropdown-wrapper.hidden {
        display: none;
    }

    .dropdown-wrapper>ul {
        margin: 0px;
        padding: 5px;
        list-style: none;
        position: absolute;
        width: 100%;
        box-shadow: 3px 3px 10px 0px;
        background: white;
    }

    .dropdown input {
        width: calc(100% - 120px);
        line-height: 2;
        outline: none;
        border: none;
    }

    .payment-method-option:hover {
        background: #d4d4d4;
    }

    .payment-method-option.selected {
        font-weight: bold;
    }

    .payment-dropdown-header {
        padding: 2px;
        border: 1px solid #e0e0e0;
        border-radius: 3px;
    }

    .payment-overlay {
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0px;
    }

    .error {
        color: red;
        font-size: 10pt;
    }
    .pd-5 {
        padding:5px;
    }
  
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
@include('partials.flash_messages')
<div class="row" id="common-page-layout">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
    <input type="hidden" name="page_no" class="page_no" />
    <div class="col-lg-12 margin-tb">
        <div class="row">
            <div class="col">
                <div class="h" style="margin-bottom:10px;">
                    <div class="row">
                        <form class="form-inline message-search-handler" method="post">
                            <div class="col">
                                <div class="form-group">
                                    <label for="keyword">Keyword:</label>
                                    <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control data-keyword","placeholder" => "Enter keyword"]) ?>
                                </div>
                                <div class="form-group">
                                    <label for="keyword">Active:</label>
                                    <select name="is_active"  class="form-control">
                                        <option value="0" {{request("is_active") == 0 ? 'selected' : ''}}>All</option>
                                        <option value="1" {{request("is_active") == 1 ? 'selected' : ''}}>Active</option>
                                        <option value="2" {{request("is_active") == 2 ? 'selected' : ''}}>In active</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="button">&nbsp;</label>
                                    <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
                                        <img src="/images/search.png" style="cursor: default;">
                                    </button>
                                </div>      
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>  
        <div class="col-md-12 margin-tb" id="page-view-result">

        </div>
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
    <div class="modal-dialog modal-lg" role="document" id="modalDialog">
    </div>  
</div>


<div id="time_history_modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title">Estimated Time History</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form action="" id="approve-time-btn" method="POST">
                @csrf
                <input type="hidden" name="hidden_task_type" id="hidden_task_type">
                <div class="modal-body">
                <div class="row">
                <input type="hidden" name="developer_task_id" id="developer_task_id">

                    <div class="col-md-12" id="time_history_div">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Old Value</th>
                                    <th>New Value</th>
                                    <th>Updated by</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    @if(auth()->user()->isReviwerLikeAdmin())
                        <button type="submit" class="btn btn-secondary">Confirm</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>

<div id="chat-list-history" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Communication</h4>
                    <input type="hidden" id="chat_obj_type" name="chat_obj_type">
                    <input type="hidden" id="chat_obj_id" name="chat_obj_id">
                    <button type="submit" class="btn btn-default downloadChatMessages">Download</button>
                </div>
                <div class="modal-body" style="background-color: #999999;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
</div>
@include('common.commonEmailModal')
@include("usermanagement::templates.list-template")
@include("usermanagement::templates.create-solution-template")
@include("usermanagement::templates.load-communication-history")
@include("usermanagement::templates.add-role")
@include("usermanagement::templates.add-permission")
@include("usermanagement::templates.load-task-history")
@include("usermanagement::templates.add-team")
@include("usermanagement::templates.edit-team")
@include("usermanagement::templates.add-time")
@include("usermanagement::templates.user-avaibility")
@include("usermanagement::templates.show-task-hours")
@include("usermanagement::templates.show-user-details")


<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript" src="/js/user-management-list.js"></script>


<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"> </script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"> </script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
<script src="{{asset('js/common-email-send.js')}}">//js for common mail</script> 
<script type="text/javascript">
 $('#due-datetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
});
    $('.due-datetime').datetimepicker({
        format: 'YYYY-MM-DD HH:mm'
    });
    page.init({
        bodyView : $("#common-page-layout"),
        baseUrl : "<?php echo url("/"); ?>"
    });


    function editUser(id) {
       $.ajax({
          url: "/user-management/edit/"+id,
          type: "get"
        }).done(function(response) {
          $('.common-modal').modal('show');
          console.log($(".modal-dialog"));
           $(".modal-dialog").html(response); 
        }).fail(function(errObj) {
            $('.common-modal').modal('hide');
        });
    }

    function payuser(id) {
       $.ajax({
          url: "/user-management/paymentInfo/"+id,
          type: "get"
        }).done(function(response) {
            if(response.code == 500) {
                toastr['error'](response.message, 'error');
            }
            else {
                $('.common-modal').modal('show');
                console.log($(".modal-dialog"));
                $(".modal-dialog").html(response); 
            }
        }).fail(function(errObj) {
            $('.common-modal').modal('hide');
        });
    }

    $(".common-modal").on("click",".open-payment-method",function() {
        if ($('.common-modal #permission-from').hasClass('hidden')) {
            $('.common-modal #permission-from').removeClass('hidden');
        } else {
            $('.common-modal #permission-from').addClass('hidden');
        }
        });

        $(".common-modal").on("click",".add-payment-method",function() {
            var name = $('.common-modal #payment-method-input').val();
            console.log(name);
            if(!name) {
                return;
            }

            $.ajax({
            url: "/user-management/add-new-method",
            type: "post",
            data: {
                name : name,
                "_token": "{{ csrf_token() }}"
            }
            }).done(function(response) {
            $(".common-modal #payment_method").html(response); 
            $('.common-modal #permission-from').addClass('hidden');
            $('.common-modal #payment-method-input').val('');
            }).fail(function(errObj) {
            });
        });

        


    let paymentMethods;

    function makePayment(userId, defaultMethod = null) {
        $('input[name="user_id"]').val(userId);

        if (defaultMethod) {
            $('#payment_method').val(defaultMethod);
        }
        filterMethods('');
        $('.dropdown input').val('');

        $("#paymentModal").modal();
    }

    function setPaymentMethods() {
        paymentMethods = $('.payment-method-option');
        console.log(paymentMethods);
    }

    $(document).ready(function() {

        adjustHeight();

        $('#payment-table').DataTable({
            "ordering": true,
            "info": false
        });

        setPaymentMethods();

        $('#payment-dropdown-wrapper').click(function() {
            event.stopPropagation();
        })

        $("#paymentModal").click(function() {
            closeDropdown();
        })
    });

    function adjustHeight() {
        $('.activity-container').each(function(index, element) {
            const childElement = $($(element).children()[0]);
            $(element).attr('data-expanded-height', childElement.height());
            $(element).height(0);
            childElement.height(0);

            setTimeout(
                function() {
                    $(element).addClass('elastic');
                    childElement.addClass('elastic');
                    $('#payment-table').css('visibility', 'visible');
                },
                1
            )
        })
    }

    function toggle(id) {
        const expandableElement = $('#elastic-' + id);

        const isExpanded = expandableElement.attr('data-expanded') === 'true';


        if (isExpanded) {
            console.log('true1');
            expandableElement.height(0);
            $($(expandableElement).children()[0]).height(0);
            expandableElement.attr('data-expanded', 'false');
        } else {
            console.log('false1');
            const expandedHeight = expandableElement.attr('data-expanded-height');
            expandableElement.height(expandedHeight);
            $($(expandableElement).children()[0]).height(expandedHeight);
            expandableElement.attr('data-expanded', 'true');
        }



    }

  

    function filterMethods(needle) {
        console.log(needle);
        $('#payment-method-dropdown .payment-method-option').remove();

        let filteredElements = paymentMethods.filter(
            function(index, element) {
                const optionValue = $(element).text();
                return optionValue.toLowerCase().includes(needle.toLowerCase());
            }
        )

        filteredElements.each(function(index, element) {
            const value = $(element).text();
            if (value == $('#payment_method').val()) {
                $(element).addClass('selected');
            } else {
                $(element).removeClass('selected');
            }
        });

        $('#payment-method-dropdown').append(filteredElements);
    }

    function selectOption(element) {
        selectOptionWithText($(element).text());
    }

    function selectOptionWithText(text) {
        $('#payment_method').val(text);
        closeDropdown();
    }


    function toggleDropdown() {
        if ($('#payment-dropdown-wrapper').hasClass('hidden')) {
            filterMethods('');
            $('.dropdown input').val('');
            $('#payment-dropdown-wrapper').css('display','block !important');
            $('#payment-dropdown-wrapper').removeClass('hidden');
        } else {
            $('#payment-dropdown-wrapper').addClass('hidden');
        }
        event.stopPropagation();
    }

    function closeDropdown() {
        $('#payment-dropdown-wrapper').addClass('hidden');
    }

    function addPaymentMethod() {

        console.log('here');

        const newPaymentMethod = $('#payment-method-input').val();

        let paymentExists = false;
        $('#payment-method-dropdown .payment-method-option')
            .each(function(index, element) {
                if ($(element).text() == newPaymentMethod) {
                    paymentExists = true;
                }
            });

        if (paymentExists) {
            alert('Payment method exits');
            return;
        } else if (!newPaymentMethod || newPaymentMethod.trim() == '') {
            alert('Payment method required');
            return;
        }

        filterMethods('');

        $('#payment-method-dropdown').append(
            '<li onclick="selectOption(this)" class="payment-method-option">' + newPaymentMethod + '</li>'
        );

        $('#payment_method').append(
            '<option value="' + newPaymentMethod + '">' + newPaymentMethod + '</option>'
        );

        setPaymentMethods();



        selectOptionWithText(newPaymentMethod);
        event.stopPropagation();
        event.preventDefault();

        return true;
    }




    $(document).on("change", ".quickComment", function (e) {

var message = $(this).val();

if ($.isNumeric(message) == false) {
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: "/user-management/reply/add",
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
        url: "/user-management/reply/delete",
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



$(document).on('click', '.send-message', function () {
            var thiss = $(this);
            var data = new FormData();
            var user_id = $(this).data('userid');
            var message = $(this).siblings('input').val();

            data.append("user_id", user_id);
            data.append("message", message);
            data.append("status", 1);

            if (message.length > 0) {
                if (!$(thiss).is(':disabled')) {
                    $.ajax({
                        url: '/whatsapp/sendMessage/user',
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
                        // thiss.closest('tr').find('.chat_messages').html(thiss.siblings('input').val());
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
</script>

@endsection
