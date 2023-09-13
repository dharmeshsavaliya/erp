@extends('layouts.app')
@section('title', 'Meetings')
@section('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">

    {{-- <link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" type="text/css" /> --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">

    <style>
        #message-wrapper {
            height: 450px;
            overflow-y: scroll;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-lg-12 text-center">
            <h2 class="page-heading"> Meetings</h2>
        </div>
        <div class="container">
        </div>
    </div>
    <div class="clearboth"></div>
    <div class="row" style="margin:10px;">
        <!-- <h4>List Of Upcoming Meetings</h4> -->
        <div class="col-lg-12">
            <div class=" pull-right">
                <a href="{{ route('meeting.list.error-logs') }}" target="_blank" class="btn btn-secondary"> View Api Logs</a>
                <button type="button" class="btn btn-secondary" id="sync_meetings"> Sync Meetings </button>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#personal-meeting-update"> Update Your Personal Meeting </button>
            </div>
        </div>
        <div class="col-lg-12 margin-tb">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="3%">ID</th>
                        <th width="10%">Meeting Id</th>
                        <th width="10%">Meeting Type</th>
                        <th width="15%">Meeting Topic</th>
                        <th width="15%">Meeting Agenda</th>
                        <th width="5%">Join Meeting URL</th>
                        <th width="10%">Start Date Time</th>
                        <th width="5%">Meeting Duration</th>
                        <th width="3%">Timezone</th>
                        <th width="8%">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($meetingData)
                        @foreach($meetingData as $meetings)
                            <tr>
                                <td class="p-2">{{ $meetings->id }}</td>
                                <td class="p-2">{{ $meetings->meeting_id }}</td>
                                <td class="p-2">{{ $meetings->meeting_type }}</td>
                                <td class="p-2">{{ $meetings->meeting_topic }}</td>
                                <td class="p-2">{{ $meetings->meeting_agenda }}</td>
                                <td class="p-2"><a href="{{ $meetings->join_meeting_url }}" target="_blank">Link</a></td>
                                <td class="p-2">{{ Carbon\Carbon::parse($meetings->start_date_time)->format('M, d-Y H:i') }}</td>
                                <td class="p-2">{{ $meetings->meeting_duration }} mins</td>
                                <td class="p-2" width="20%">{{ $meetings->timezone }}</td>
                                <td>
                                    <button type="button" title="Fetch Recordings" class="btn" style="padding: 0px 1px;">
										<i class="fa fas fa-refresh fetch-zoom-meeting-recordings" data-meeting_id="{{ $meetings->meeting_id }}"></i>
									</button>
                                    <a href="{{ route('meeting.list.recordings', ['id' => $meetings->meeting_id]) }}" target="_blank">
                                        <i class="fa fa-video-camera" style="color: #808080;"></i>
                                    </a>   
                                    <button type="button" title="Fetch Participants" class="btn" style="padding: 0px 1px;">
										<i class="fa fas fa-refresh fetch-zoom-meeting-participants" data-meeting_id="{{ $meetings->meeting_id }}"></i>
									</button>
                                    <button type="button" class="btn btn-xs Participants"
                                        data-meeting_id="{{ $meetings->meeting_id }}" title="view Participants" onclick="viewParticipants()">
                                            <i class="fa fa-users" style="color: #808080;"></i>
                                    </button>
                                    </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            {!! $meetingData->appends(request()->except('page'))->links() !!}
        </div>
    </div>
@include('zoom-meetings.personal-meeting-update-modal')
@endsection

<div id="participants-list-modal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h4 class="modal-title"><b>Participants Lists</b></h4>
                </div>
                <button type="button" class="close" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="row">
                            <div class="col-12" id="participants-list-modal-html">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.31.0/js/jquery.tablesorter.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.3.7/jquery.jscroll.min.js"></script> --}}

    <script type="text/javascript">
        $(document).on('click', '#sync_meetings', function(e){
            $("#loading-image-preview").show();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                type: "POST",
                url: "{{ route('vendor.meetings.recordings.sync') }}",          
                success: function(response) {
                    $("#loading-image-preview").hide();
                    toastr['success'](response.message, 'success');
                    window.location.reload();
                }
            });
        });

        $('.fetch-zoom-meeting-recordings').click(function() {
            var $this = $(this);
            var meetingId = $this.data('meeting_id');

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('meeting.fetch.recordings')}}",
                type: 'POST',
                data: {
                    meetingId : meetingId,
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                $("#loading-image-preview").hide();
                toastr["success"](response.message);
            }).fail(function(errObj) {
                $("#loading-image-preview").hide();
                toastr["error"](errObj.responseJSON.message);
            });
        });

        $('.fetch-zoom-meeting-participants').click(function() {
            var $this = $(this);
            var meetingId = $this.data('meeting_id');

            jQuery.ajax({
                headers: {
                    'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('meeting.fetch.participants')}}",
                type: 'POST',
                data: {
                    meetingId : meetingId,
                },
                beforeSend: function() {
                    $("#loading-image-preview").show();
                }
            }).done(function(response) {
                $("#loading-image-preview").hide();
                toastr["success"](response.message);
            }).fail(function(errObj) {
                $("#loading-image-preview").hide();
                toastr["error"](errObj.responseJSON.message);
            });
        });

        function viewParticipants(pageNumber = 1) {
            var button = document.querySelector('.btn.btn-xs.Participants'); 
            var meetingId = button.getAttribute('data-meeting_id');

                $.ajax({
                    url: "{{route('meeting.list.participants')}}",
                    type: 'GET',
                    dataType: "json",
                    data: {
                        meetingId: meetingId,
                        page:pageNumber,
                    },
                    beforeSend: function() {
                    $("#loading-image-preview").show();
                }
                }).done(function(response) {
                    $('#participants-list-modal-html').empty().html(response.html);
                    $('#participants-list-modal').modal('show');
                    renderdomainPagination(response.data);
                    $("#loading-image-preview").hide();
                }).fail(function(response) {
                    $('.loading-image-preview').show();
                    console.log(response);
                });
        }

        function renderdomainPagination(response) {
            var paginationContainer = $(".pagination-container-participation");
            var currentPage = response.current_page;
            var totalPages = response.last_page;
            var html = "";
            var maxVisiblePages = 10;

            if (totalPages > 1) {
                html += "<ul class='pagination'>";
                if (currentPage > 1) {
                html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(" + (currentPage - 1) + ")'>Previous</a></li>";
                }
                var startPage = 1;
                var endPage = totalPages;

                if (totalPages > maxVisiblePages) {
                if (currentPage <= Math.ceil(maxVisiblePages / 2)) {
                    endPage = maxVisiblePages;
                } else if (currentPage >= totalPages - Math.floor(maxVisiblePages / 2)) {
                    startPage = totalPages - maxVisiblePages + 1;
                } else {
                    startPage = currentPage - Math.floor(maxVisiblePages / 2);
                    endPage = currentPage + Math.ceil(maxVisiblePages / 2) - 1;
                }

                if (startPage > 1) {
                    html += "<li class='page-item'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(1)'>1</a></li>";
                    if (startPage > 2) {
                    html += "<li class='page-item disabled'><span class='page-link'>...</span></li>";
                    }
                }
                }

                for (var i = startPage; i <= endPage; i++) {
                html += "<li class='page-item " + (currentPage == i ? "active" : "") + "'><a class='page-link' href='javascript:void(0);' onclick='changeParticipantsPage(" + i + ")'>" + i + "</a></li>";
                }
                html += "</ul>";
            }
            paginationContainer.html(html);
         }

        function changeParticipantsPage(pageNumber) {
            viewParticipants(pageNumber);
        }
    </script>
@endsection