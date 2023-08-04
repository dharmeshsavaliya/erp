@extends('layouts.app')

@section('link-css')
<style type="text/css">
  .float-right-addbtn{
    float: right !important;
    margin-top: 1%;
    margin-right: 0.095rem;
  }
  .form-group {
    padding: 10px;
  }
</style>
@endsection
@section('content')
<!-- TIMEDOCTOR ACCOUNT MODEL CONTENT START -->
<div id="time_doctor_modal" class="modal fade" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Add Time Doctor Account</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="add_time_doctor_account">
              <div class="modal-body">
                  <div class="form-group field_spacing">
                      <strong>Email:</strong>
                      <input type="text" name="email" class="form-control" id="email">
                      <label class="error"></label>
                  </div>
                  <div class="form-group field_spacing">
                      <strong>Password:</strong>
                      <input type="text" name="password" class="form-control" id="password">
                      <label class="error"></label>
                  </div>
              </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>


@if(Session::has('message'))
<div class="alert alert-success alert-block">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>{{ Session::get('message') }}</strong>
</div>
@endif


<div class="col-lg-12 margin-tb">
    <h2 class="page-heading">List User Accounts {{count($timeDoctorAccounts)}}</h2>
</div>

  @if(!empty($timeDoctorAccounts))
  <div class="row">

    <div class="col-lg-8 col-12">
      {{-- <form id="filter">
        <div class="row">
          <div class="col-lg-4 col-md-6 col-12">
            <input type="text" class="form-control" name="time_doctor_user_id" id="time_doctor_user_id" placeholder="Time Doctor User Id">
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <input type="text" class="form-control" name="time_doctor_email" id="time_doctor_email" placeholder="Time Doctor Email">
          </div>
          <div class="col-lg-4 col-md-6 col-12">
            <select class="form-control" name="time_doctor_account_id" id="time_doctor_account_id">
              @foreach($timeDoctorAccounts as $timeDoctorAccount)
                <option value="{{$timeDoctorAccount->id}}">{{$timeDoctorAccount->time_doctor_email}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-4 col-md-6 col-12 mt-4 mb-4">
            <select class="form-control" name="time_doctor_user" id="time_doctor_user">
              @foreach($users as $user)
                <option value="{{$user->id}}" >{{$user->name}}</option>
              @endforeach
            </select>
          </div>
          <div class="col-lg-4 col-12">
              <button type="button" class="btn btn-image mt-4" onclick="submitSearch()"><img src="/images/filter.png" style="cursor: nwse-resize;"></button>
              <button type="button" class="btn btn-image mt-4" id="resetFilter" onclick="resetSearch()"><img src="/images/resend2.png" style="cursor: nwse-resize;"></button>
          </div>
        </div>
      </form> --}}
    </div>

    <div class="col-lg-4 col-12">
      <button type="button" class="btn btn-secondary float-right-addbtn" id="add_account">+ Add Account</button>
    </div>
    <div class="col-md-12 pr-5 pl-5">    
    <table class="table table-bordered" id="time-doctor-members">
      <thead>
        <tr>
          <th>No</th>
          <th>Password</th>
          <th>TimeDoctor Account</th>
          <th>Create DateTime</th>
          <th>Access Token </th>
          <th width="30%">Remark </th>
        </tr>
      </thead>
      @php  $no=1; @endphp
      <tbody>
      @foreach($timeDoctorAccounts as $key =>$member)
        <tr>
          <td style="vertical-align:middle;">{{ $key+1 }}</td>
          <td style="vertical-align:middle;">{{ ($member->time_doctor_email)!= null  ? $member->time_doctor_email : ""}}</td>
          <td style="vertical-align:middle;">{{ ($member->time_doctor_password)!= null  ? $member->time_doctor_password : ""}}</td>
          <td style="vertical-align:middle;">{{ $member->created_at }}</td>
            @if ($member->auth_token === '')
            <td><button type="button" class="btn btn-secondary get_token" data-id="{{ $member->id }}">Get Token</button></td>
            @else
            <td style="vertical-align:middle;">{{ $member->auth_token }}</td>
            @endif
            <td>
            {{-- <div class="d-flex"> --}}
            <input type="text" name="remark_pop" class="form-control remark_pop{{$member->id}}" placeholder="Please enter remark" style="margin-bottom:5px;width:50%;display:inline;">
            <button type="button" class="btn btn-sm btn-image add_remark pointer" title="Send message" data-member_id="{{$member->id}}">
                <img src="{{asset('images/filled-sent.png')}}">
            </button>
          <button data-member_id="{{ $member->id }}"  class="btn btn-xs btn-image show-remark" title="Remark"><img src="{{asset('images/chat.png')}}" alt=""></button>
        {{-- </div> --}}
        </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <br>
    <hr>
  </div>
  </div>  
  @endif

  <div id="category-listing" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body">

                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th width="10%">No</th>
                                <th width="30%">Old Remark</th>
                                <th width="30%">New Remark</th>
                                <th width="30%">Updated by</th>
                                <th width="20%">Created Date</th>
                            </tr>
                        </thead>
                        <tbody class="category-listing-view">
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif')
  50% 50% no-repeat;display:none;">
</div>
@section("scripts")
<!-- <script src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script> -->
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script type="text/javascript">

  function saveUser(a) {
    var selectedValue = (a.value || a.options[a.selectedIndex].value); //crossbrowser solution =)
    console.log('selectedValue', selectedValue);
    if (selectedValue != 'unassigned') {
      var splitValues = selectedValue.split('|');
      var userId = splitValues[0];
      var timeDoctorUserId = splitValues[1];


      var xhr = new XMLHttpRequest();
      var url = "link_time_doctor_user";
      xhr.open("POST", url, true);
      xhr.setRequestHeader("Content-Type", "application/json");
      xhr.onreadystatechange = function() {        
        if (xhr.readyState === 4 && xhr.status === 200) {
          var json = JSON.parse(xhr.responseText);
          console.log("EMAIL = "+json.email + ", " + json.password);
        }
      };
      var data = JSON.stringify({
        "user_id": userId,
        "time_doctor_user_id": timeDoctorUserId
      });
      xhr.send(data);
    }
  }

  $.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      }
  });

  $(document).on("click", "#add_account" , function(){
    $("#time_doctor_modal").modal('show');
    $("#email").val('');
    $("#password").val('');
    $(".error").html('');
  });

  
  $(document).on("click", ".get_token", function(){
      var getId = $(this).attr('data-id');
      $.ajax({
          type: "POST",
          url: "{{ route('time-doctor.getToken') }}",
          data: {id:getId},
          success: function(response) {
            if(response.code == 200){
              toastr['success'](response.message, 'success');
            } else {
              toastr['error'](response.message, 'error');
            }
            window.location.reload();
          }
      })
  })

    $("#add_time_doctor_account").validate({
        rules: {
        email: "required",
        password: "required",
        },
        messages: {
        email: "Please enter email",
        password: "Please enter password",
        },
        errorPlacement: function(error, element) {
        error.insertAfter(element);
        },  
        submitHandler: function (form) {
            var formdata = $('#add_time_doctor_account').serialize();
            $.ajax({
                type: "POST",
                url: "{{ route('time-doctor.adduser') }}",
                data: formdata,
                success: function(response) {
                $('#add_time_doctor_account').trigger("reset");
                if(response.code == 200){
                    toastr['success'](response.message, 'success');
                } else {
                    toastr['error'](response.message, 'error');
                }
                window.location.reload();
                }
            })
        }
    });


    $("#time_doctor_account_id").select2({
        multiple: true,
        placeholder: "Select account"
        });
    $("#time_doctor_user").select2({
        multiple: true,
        placeholder: "Select user"
    });
    $("#time_doctor_account_id").val(null);
    $("#time_doctor_user").val(null);
    $("#time_doctor_account_id, #time_doctor_user").trigger("change");

    function submitSearch(){
        src = "{{route('time-doctor.members')}}"
        time_doctor_user_id = $('#time_doctor_user_id').val()
        time_doctor_email = $('#time_doctor_email').val()
        time_doctor_account_id = $('#time_doctor_account_id').val()
        time_doctor_user = $('#time_doctor_user').val()
        $.ajax({
            url: src,
            dataType: "json",
            data: {
                time_doctor_user_id,
                time_doctor_email,
                time_doctor_account_id,
                time_doctor_user
            },
            beforeSend: function () {
                $("#loading-image").show();
            },

        }).done(function (data) {
            $("#loading-image").hide();
            $("#time-doctor-members tbody").empty().html(data.tbody);
            // $("#Referral_count").text(data.count);
            // if (data.links.length > 10) {
            //     $('ul.pagination').replaceWith(data.links);
            // } else {
            //     $('ul.pagination').replaceWith('<ul class="pagination"></ul>');
            // }

        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
        
    }

    function resetSearch(){
        $('#time_doctor_user_id').val("")
        $('#time_doctor_email').val("")
        $('#time_doctor_account_id').val(null)
        $('#time_doctor_user').val(null)
        $("#time_doctor_account_id, #time_doctor_user").trigger("change");
        submitSearch();
    }

    $(document).on("click",".add_remark",function(e) {
        e.preventDefault();
        var thiss = $(this);
        var member_id = $(this).data('member_id');
        var remark = $(`.remark_pop`+member_id).val();

        $.ajax({
            type: "POST",
            url: `{{ route('time-doctor.remark.store') }}`,
            headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
            data: {
                member_id : member_id,
                remark : remark,
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function (response) {
            if(response.code == 200) {
                $("#loading-image").hide();
                if (remark == ''){
                }
                $(".task-create-get-list-view").html(response.data);
                $(`.td-password-remark`+password_id).html(response.remark_data);
                $(`.remark_pop`+password_id).val("");
                toastr['success'](response.message);
            }else{
                $("#loading-image").hide();
                if (remark == '') {
                }
                $(".task-create-get-list-view").html("");
                toastr['success'](response.message);
            }

        }).fail(function (response) {
            $("#loading-image").hide();
            $("#preview-task-create-get-modal").modal("show");
            $(".task-create-get-list-view").html("");
            toastr['success'](response.message);
        });
    });

    $(document).on('click', '.show-remark', function() {
        var thiss = $(this);
        var member_id = $(this).data('member_id');
            $.ajax({
                type: "POST",
                url: `{{ route('time-doctor.remark.get') }}`,
                headers: {
                'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
            },
                data: {
                    member_id:member_id,

                },
                success: function(response) {
                    if (response.status) {
                        var html = "";
                        $.each(response.data, function(k, v) {
                            html += `<tr>
                                        <td> ${k + 1} </td>
                                        <td> ${v.old_remark ? v.old_remark : ''} </td>
                                        <td> ${v.new_remark ? v.new_remark : ''} </td>
                                        <td> ${(v.user !== undefined) ? v.user.name : ' - ' } </td>
                                        <td> ${v.created_at} </td>
                                    </tr>`;
                        });
                        $("#category-listing").find(".category-listing-view").html(html);
                        $("#category-listing").modal("show");
                    } else {
                        toastr["error"](response.error, "Message");
                    }
                }
            });
        });

</script>
<style>
  .select2-search--inline {
      display: contents; /*this will make the container disappear, making the child the one who sets the width of the element*/
  }

  .select2-search__field:placeholder-shown {
      width: 100% !important; /*makes the placeholder to be 100% of the width while there are no options selected*/
  }
</style>
@endsection