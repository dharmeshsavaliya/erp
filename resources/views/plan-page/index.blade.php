@extends('layouts.app')


@section('title', 'Plans')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
@section('content')
<div class="row mb-5">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Plans page</h2>

        <div class="pull-right">
            <button type="button" class="btn btn-secondary new-plan" data-toggle="modal" data-target="#myModal">New plan</button>
        </div>

        <form action="{{ url()->current() }}" method="GET" id="searchForm" class="form-inline align-items-start">
            <div class="form-group col-md-2 mr-3 mb-3 no-pd">
                <input name="term" type="text" class="form-control" value="{{ request('term') }}" placeholder="Search.." style="width:100%;">
            </div>
            <div class="form-group col-md-2 mr-3 mb-3 no-pd">
                <input name="date" type="date" class="form-control" value="{{ request('date') }}" placeholder="Search.." style="width:100%;">
            </div>
            <div class="form-group col-md-3 mr-3 no-pd">
                <select class="form-control" name="priority">
                    <option value="">Select priority</option>
                    <option value="high">High</option>
                    <option value="medium">Medium</option>
                    <option value="low">Low</option>
                </select>
            </div>
            <div class="form-group col-md-3 mr-3 no-pd">
                <select class="form-control" name="status">
                    <option value="">Select status</option>
                    <option value="complete">complete</option>
                    <option value="pending">pending</option>
                </select>
            </div>
            <div class="col-md-1 no-pd">
            &nbsp;
            <button type="submit" class="btn btn-image image-filter-btn"><img src="/images/filter.png"/></button>
            </div>
        </form>
    </div>
</div>

@include('partials.flash_messages')

<div class="table-responsive">
    <table class="table table-bordered" id="store_website-analytics-table">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Subject</th>
                <th>Sub subject</th>
                <th>Description</th>
                <th>Priority</th>
                <th>status</th>
                <th>Date</th>
                <th width="280px">Action</th>
            </tr>
        </thead>
        <tbody class="searchable">
            @foreach($planList as $key => $record)
            <tr>
                <td>{{$record->id}}</td>
                <td>{{$record->subject}}</td>
                <td>{{$record->sub_subject}}</td>
                <td width="15%">
                    <span class="toggle-title-box has-small" data-small-title="<?php echo substr($record->description, 0, 10).'..' ?>" data-full-title="<?php echo ($record->description) ? $record->description : '' ?>">
                        <?php
                            if($record->description) {
                                echo (strlen($record->description) > 12) ? substr($record->description, 0, 10).".." : $record->description;
                            }
                         ?>
                     </span>
                </td>
                <td>{{$record->priority}}</td>
                <td>{{$record->status}}</td>
                <td>{{$record->date}}</td>
                <td>
                    <button type="button" class="btn btn-secondary edit-plan" data-id="{{$record->id}}"><i class="fa fa-edit"></i></button>
                    <a href="{{route('plan.delete',$record->id)}}" class="btn btn-image" title="Delete Record"><img src="/images/delete.png"></a>
                    <button title="Add step" type="button" class="btn btn-secondary btn-sm add-sub-plan" data-id="{{$record->id}}" data-toggle="modal" data-target="#myModal">+</button>
                    <button title="Open step" type="button" class="btn preview-attached-img-btn btn-image no-pd" data-id="{{$record->id}}">
                        <img src="/images/forward.png" style="cursor: default;">
                    </button>
                </td>
            </tr>
            <tr class="expand-{{$record->id}} hidden">
                <th colspan="2"></th>
                <th>Remark</th>
                <th>description</th>
                <th>priority</th>
                <th>status</th>
                <th>date</th>
                <th>Action</th>
                @foreach( $record->subList( $record->id ) as $sublist)
                    <tr class="expand-{{$record->id}} hidden" >
                        <td colspan="2"></td>
                        <td width="10%">
                            <span class="toggle-title-box has-small" data-small-title="<?php echo substr($sublist->remark, 0, 10).'..' ?>" data-full-title="<?php echo ($sublist->remark) ? $sublist->remark : '' ?>">
                                <?php
                                    if($sublist->remark) {
                                        echo (strlen($sublist->remark) > 12) ? substr($sublist->remark, 0, 10).".." : $sublist->remark;
                                    }
                                 ?>
                             </span>
                        </td>
                        <td width="15%">
                            <span class="toggle-title-box has-small" data-small-title="<?php echo substr($sublist->description, 0, 10).'..' ?>" data-full-title="<?php echo ($sublist->description) ? $sublist->description : '' ?>">
                                <?php
                                    if($sublist->description) {
                                        echo (strlen($sublist->description) > 12) ? substr($sublist->description, 0, 10).".." : $sublist->description;
                                    }
                                 ?>
                             </span>
                        </td>
                        <td>{{$sublist->priority}}</td>
                        <td>{{$sublist->status}}</td>
                        <td>{{$sublist->date}}</td>
                        <td>
                            <button type="button" class="btn btn-secondary edit-plan" data-id="{{$sublist->id}}"><i class="fa fa-edit"></i></button>
                            <a href="{{route('plan.delete',$sublist->id)}}" class="btn btn-image" title="Delete Record"><img src="/images/delete.png"></a>
                        </td>
                    </tr>
                @endforeach
            </tr>
            @endforeach
            <tr>
                <td colspan="8">{{$planList->appends(request()->except("page"))->links()}}</td>
            </tr>
        </tbody>
    </table>
</div>

<!-- The Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModal" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="myModal">Plans</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
        <form method="post" id="planadd" action="{{ route('plan.store') }}">
          <div class="modal-body">
            <div class="container-fluid">
                  @csrf
                  <div class="row subject-field">
                      <div class="col-md-6">
                          <div class="form-group">
                            <label  class="col-form-label">Subject:</label>
                            <input type="text" name="subject" class="form-control">
                          </div>
                      </div>
                      <div class="col-md-6">
                          <div class="form-group">
                            <label  class="col-form-label">Sub subject:</label>
                            <input type="text" name="sub_subject" class="form-control" >
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-6">
                          <div class="form-group">
                            <label  class="col-form-label">Priority:</label>
                            <select class="form-control" name="priority" required>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                          </div>
                      </div>
                      <input type="hidden" id="edit_id" name="id">
                      <input type="hidden" id="parent_id" name="parent_id">
                      <div class="col-md-6">
                         <div class="form-group">
                            <label  class="col-form-label">Status:</label>
                            <select class="form-control" name="status" required>
                                <option value="complete">complete</option>
                                <option value="pending">pending</option>
                            </select>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-md-6">
                         <div class="form-group">
                            <label  class="col-form-label">Date:</label>
                            <input type="date" name="date" class="form-control" required>
                          </div>
                      </div>
                      <div class="col-md-6">
                         <div class="form-group">
                            <label class="col-form-label">Description:</label>
                            <textarea class="form-control" name="description"></textarea>
                          </div>
                      </div>
                  </div>
                  <div class="row remark-field hidden" >
                      <div class="col-md-12">
                         <div class="form-group">
                            <label  class="col-form-label">Remark:</label>
                            <textarea class="form-control" name="remark"></textarea>
                          </div>
                      </div>
                  </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-secondary">Save</button>
          </div>
      </form>
    </div>
  </div>
</div>

@endsection

<script>

$(document).on('click','.new-plan', function (event) {
    $('#parent_id').val('');
    $('#edit_id').val('');
    $('.remark-field').addClass('hidden');
    $('.subject-field').removeClass('hidden')
    $('#planadd')[0].reset();
});

$('#myModal').on('hidden.bs.modal', function () {
  
})

$(document).on('click', '.preview-attached-img-btn', function (e) {     
    e.preventDefault();
    var planId = $(this).data('id');
    var expand = $('.expand-'+planId);
    $(expand).toggleClass('hidden');

});
$(document).on('click','.add-sub-plan', function (event) {
    var id = $(this).data('id');
    $('#edit_id').val('');
    $('#parent_id').val(id);
    $('#planadd')[0].reset();
    $('.subject-field').addClass('hidden')
    $('.remark-field').removeClass('hidden');
});

    $(document).on('click','.edit-plan', function (event) {
        $('.remark-field').addClass('hidden');
        $('.subject-field').removeClass('hidden')
        $('#planadd')[0].reset();
        var id = $(this).data('id');
        $('#parent_id').val('');
        $('#edit_id').val('');

        $('#edit_id').val(id)

        $.ajax({
            url: "{{ route('plan.edit') }}",
            data: { id : id },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function (data) {
            console.log(data);
            if(data.code == 200){
                $('input[name="subject"]').val(data.object.subject);
                $('input[name="sub_subject"]').val(data.object.sub_subject);
                $('select[name="priority"]').val(data.object.priority).change();
                $('select[name="status"]').val(data.object.status).change();
                $('input[name="date"]').val(data.object.date);
                $('textarea[name="description"]').val(data.object.description);
                $('textarea[name="remark"]').val(data.object.remark);
                $('#parent_id').val(data.object.parent_id);
                if( data.object.parent_id != null ){
                    $('.remark-field').removeClass('hidden');
                    $('.subject-field').addClass('hidden')
                }
                $('#myModal').modal('toggle');
            }else{
                alert('error');
            }
        }).fail(function (jqXHR, ajaxOptions, thrownError) {
            alert('No response from server');
        });
      
    });

$(document).ready(function () {
    (function ($) {
        $('#filter').keyup(function () {
            var rex = new RegExp($(this).val(), 'i');
            $('.searchable tr').hide();
            $('.searchable tr').filter(function () {
                return rex.test($(this).text());
            }).show();
        })

        $(document).on("click",".find-records",function(e){
            e.preventDefault();
            var id = $(this).data("id");
            $.ajax({
                url: "/store-website-analytics/report/"+id,
                beforeSend: function () {
                    $("#loading-image").show();
                }
            }).done(function (data) {
                $("#loading-image").hide();
                $(".bd-report-modal-lg .modal-body").empty().html(data);
                $(".bd-report-modal-lg").modal("show");
            }).fail(function (jqXHR, ajaxOptions, thrownError) {
                alert('No response from server');
            });
        });

    }(jQuery));
});

$(document).on("click",".toggle-title-box",function(ele) {
    var $this = $(this);
    if($this.hasClass("has-small")){
        $this.html($this.data("full-title"));
        $this.removeClass("has-small")
    }else{
        $this.addClass("has-small")
        $this.html($this.data("small-title"));
    }
});
</script>
