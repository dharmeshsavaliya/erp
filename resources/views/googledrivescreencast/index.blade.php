@extends('layouts.app')
@section('favicon' , '')

@section('title', 'Google Drive Screencasts')

@section('styles')
    <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
            z-index: 60;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Google Drive Screencasts/Files</h2>
            <div class="pull-left">
                <div class="form-group">
                    <div class="row">
                        <form class="form-inline message-search-handler" method="get">
                                    <div class="form-group m-1">
                                        <input name="name" list="name-lists" type="text" class="form-control" placeholder="Search file" value="{{request()->get('name')}}" />
                                        <datalist id="name-lists">
                                            @foreach ($data as $key => $val )
                                                <option value="{{$val->file_name}}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="form-group sm-1">
                                        <input name="docid" list="docid-lists" type="text" class="form-control" placeholder="Search Url" value="{{request()->get('docid')}}" />
                                        <datalist id="docid-lists">
                                            @foreach ($data as $key => $val )
                                                <option value="{{$val->google_drive_file_id	}}">
                                            @endforeach
                                        </datalist>
                                    </div>
                                    <div class="form-group m-1">
                                        <select name="task_id" id="search_task" class="form-control" placeholder="Search Dev Task">
                                        <option value="">Search Dev Task</option>
                                            @foreach ($tasks as $key => $task )
                                                <option value="{{$task->id}}" @if(request()->get('task_id')==$task->id) selected @endif>#DEVTASK-{{$task->id}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @if(Auth::user()->isAdmin())
                                    <div class="form-group m-1">
                                        <select name="user_gmail" id="search_user" class="form-control" placeholder="Search User">
                                        <option value="">Search User</option>
                                            @foreach ($users as $key => $val )
                                                <option value="{{$val->gmail}}" @if(request()->get('user_gmail')==$val->gmail) selected @endif>{{$val->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
				                    @endif
                                    <div class="form-group">
                                        <label for="button">&nbsp;</label>
                                        <button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
                                            <img src="/images/search.png" style="cursor: default;">
                                        </button>
                                        <a href="/google-drive-screencast" class="btn btn-image" id=""><img src="/images/resend2.png" style="cursor: nwse-resize;"></a>
                                    </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="pull-right">
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#uploadeScreencastModal">
                    + Upload Screencast/File
                </button>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>No</th>
                <th style="max-width: 150px">File Name</th>
                <th>Dev Task</th>
                <th>File Creation Date</th>
                <th>URL</th>
                <th style="max-width: 200px">Remarks</th>
                <th>User</th>
                <th>File Uploaded AT</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @include('googledrivescreencast.partials.list')
            @include('googledrivescreencast.partials.update-file-permissions')
            </tbody>
        </table>
    </div>

@include('googledrivescreencast.partials.upload')

<div id="showFullMessageModel" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

            </div>
        </div>

    </div>
</div>
@endsection
@section('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
$("#id_label_multiple_user_read").select2();
$("#id_label_multiple_user_write").select2();
$("#search_user").select2();
$('#id_label_task').select2({
  minimumInputLength: 3 // only start searching when the user has input 3 or more characters
});
$('#search_task').select2({
  minimumInputLength: 3 // only start searching when the user has input 3 or more characters
});

$(document).on('click', '.filepermissionupdate', function (e) {
		
		$("#updateGoogleFilePermissionModal #id_label_file_permission_read").val("").trigger('change');
		$("#updateGoogleFilePermissionModal #id_label_file_permission_write").val("").trigger('change');
		
        let data_read = $(this).data('readpermission');
        let data_write = $(this).data('writepermission');
		var file_id = $(this).data('fileid');
        var id = $(this).data('id');
		var permission_read = data_read.split(',');
		var permission_write = data_write.split(',');
		if(permission_read)
		{
			$("#updateGoogleFilePermissionModal #id_label_file_permission_read").val(permission_read).trigger('change');
		}
		if(permission_write)
		{
			$("#updateGoogleFilePermissionModal #id_label_file_permission_write").val(permission_write).trigger('change');
		}
		
		$('#file_id').val(file_id);
        $('#id').val(id);
	
	});

    $(document).on("click",".showFullMessage", function () {
        let title = $(this).data('title');
        let message = $(this).data('message');

        $("#showFullMessageModel .modal-body").html(message);
        $("#showFullMessageModel .modal-title").html(title);
        $("#showFullMessageModel").modal("show");
    });
    </script>
@endsection

