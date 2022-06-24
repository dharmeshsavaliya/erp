@extends('layouts.app')

@section('title', 'Post man Request')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">Postman Folder</h2>
          </div>

          <div class="col-12 mb-3">
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <!-- <a title="add new domain" class="btn btn-secondary add-new-btn">+</a> -->
            </div>
        </div>
    </div>
    <div class="pull-left">
      <form class="form-inline" action="/postman/folder/search" method="GET">
        <div class="col">
          <div class="form-group">
            <div class="input-group">
              <input type="text" placeholder="folder name" class="form-control" name="folder_name" value="">
            </div>
          </div>
        </div>
        <div class="col">
          <button type="submit" class="btn btn-image"><img src="/images/filter.png"></button>
          <a href="/postman/folder" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
        </div>
      </form>
    </div>
    
    <button type="button" class="btn custom-button float-right mr-3 openmodeladdpostmanfolder" data-toggle="modal" data-target="#addPostmanFolder">Add Folder</button>
  </br> 
    <div class="infinite-scroll">
	<div class="table-responsive mt-2">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>ID</th>
            <th>Folder Name</th>
            <th>Date</th>
            <th>Action</th>
          </tr>
        </thead>

        <tbody>
			  @foreach ($folders as $key => $folder)
            <tr>
              <td>{{$folder->id}}</td>
              <td>{{$folder->name}}</td>
              <td>{{$folder->created_at}}</td>
              <td>
                <a class="btn btn-image edit-postman-folder-btn" data-id="{{ $folder->id }}"><img data-id="{{ $folder->id }}" src="/images/edit.png" style="cursor: nwse-resize; width: 16px;"></a>
                <a class="btn delete-postman-folder-btn"  data-id="{{ $folder->id }}" href="#"><img  data-id="{{ $folder->id }}" src="/images/delete.png" style="cursor: nwse-resize; width: 16px;"></a>
              </td>
            </tr>
            @endforeach
        </tbody>
      </table>
      <div class="text-center">
        {!! $folders->appends(Request::except('page'))->links() !!}
    </div>
	</div>
    </div>
    <div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 50% 50% no-repeat;display:none;">
   </div>
@endsection

<div id="addPostmanFolder" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">
    <!-- Modal content-->
    <div class="modal-content ">
      <div id="add-mail-content">
        
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><span id="titleUpdate">Add</span>  Postman Folder</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="postmanFolderform" method="post">
                @csrf
                <input type="hidden" id="id" name="id" value=""/>
                <div class="form-row">
                  <div class="form-group col-md-12">
                    <label for="folder_name">Folder Name</label>
                    <input type="text" name="folder_name" value="" class="form-control" id="folder_name" placeholder="Enter folder name">
                  </div>
                </div>
              </form> 
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
              <button type="button" class="btn btn-secondary submit-folder-form">Save</button>
            </div>
            
          </div>
         
      </div>
    </div>
  </div>
</div>


<div id="view-domain" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content ">
        <div id="view-domain-content">
        
        </div>
      </div>
    </div>
</div>

@section('scripts')
  <script type="text/javascript">

    // $('ul.pagination').hide();
    //   $('.infinite-scroll').jscroll({
    //     autoTrigger: true,
    //     // debug: true,
    //     loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
    //     padding: 0,
    //     nextSelector: '.pagination li.active + li a',
    //     contentSelector: 'div.infinite-scroll',
    //     callback: function () {
    //       $('ul.pagination').first().remove();
    //       $('ul.pagination').hide();
    //     }
		// });
    
    $(document).on("click",".openmodeladdpostmanfolder",function(e){
      $('#titleUpdate').html("Add");
        $('#postmanform').find("input[type=text], textarea").val("");
    });
    $(document).on("click",".delete-postman-folder-btn",function(e){
        e.preventDefault();
        if (confirm("Are you sure?")) {
          var $this = $(this);
          var id = $this.data('id');
          $.ajax({
            url: "/postman/folder/delete",
            type: "delete",
            headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
            data:{
              id:id
            }
          }).done(function(response) {
            if(response.code = '200') {
              toastr['success']('Postman deleted successfully!!!', 'success'); 
              location.reload();
            } else {
              toastr['error'](response.message, 'error'); 
            }
          }).fail(function(errObj) {
            $('#loading-image').hide();
            $("#addPostmanFolder").hide();
            toastr['error'](errObj.message, 'error');
          });
          }
      });
      $(document).on("click",".submit-folder-form",function(e){
        e.preventDefault();
        var $this = $(this);
        if($('#titleUpdate').text() == 'Add')
          $("#id").val("");
        $.ajax({
          url: "/postman/folder/create/",
          type: "post",
          data:$('#postmanFolderform').serialize()
        }).done(function(response) {
          if(response.code = '200') {
            $('#loading-image').hide();
            $('#addPostmanFolder').modal('hide');
            toastr['success']('Folder added successfully!!!', 'success'); 
            location.reload();
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           //$("#addMail").hide();
           toastr['error'](errObj.message, 'error');
        });
    });

    $(document).on("click",".edit-postman-folder-btn",function(e){
        e.preventDefault();
        $('#titleUpdate').html("Update");
        var $this = $(this);
        var id = $this.data('id');
        $("#id").val(id);
        $.ajax({
          url: "/postman/folder/edit/",
          type: "post",
          headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
          data:{
            id:id
          }
        }).done(function(response) {
          if(response.code = '200') {
            form = $('#postmanFolderform');
            $.each(response.data, function(key, v) {
              if(key == 'name'){
                  form.find('[name="folder_name"]').val(v);
              } else if(form.find('[name="'+key+'"]').length){
                  form.find('[name="'+key+'"]').val(v);
              }      
            });
            $('#addPostmanFolder').modal('show');
            toastr['success']('Postman edited successfully!!!', 'success'); 
            
          } else {
            toastr['error'](response.message, 'error'); 
          }
        }).fail(function(errObj) {
          $('#loading-image').hide();
           $("#addPostmanFolder").hide();
           toastr['error'](errObj.message, 'error');
        });
    });


  </script>
@endsection
