@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Sitejabber Accounts, Reviews & Q/A</h2>
        </div>
        <div class="col-md-12">
            <div class="row">
                <form method="get" action="{{action('SitejabberQAController@edit', 'routines')}}">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="range">Post this number of reviews in a day</label>
                            <input name="range" id="range" type="number" class="form-control" placeholder="Eg: 6" value="{{ $setting->times_a_day }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="range2">Create this number of SJ account in a day</label>
                            <input name="range2" id="range2" type="number" class="form-control" placeholder="Eg: 6" value="{{ $setting2->times_a_day }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="mt-4 btn btn-primary">Ok</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-12 mb-5">
            <div id="exTab2" class="container">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#one" data-toggle="tab" class="btn btn-image">Accounts & Reviews</a>
                    </li>
                    <li>
                        <a href="#two" data-toggle="tab" class="btn btn-image">Q&A</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active mt-3" id="one">
                    <table id="table" class="table table-striped">
                        <thead>
                        <tr>
                            <th>S.N</th>
                            <th>Name</th>
                            <th>E-Mail Address</th>
                            <th>Password</th>
                            <th>Created On</th>
                            <th>Reviews Posted</th>
                            <th>Approval Status</th>
                            <th>Post Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($accounts as $key=>$sj)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $sj->first_name ?? 'N/A' }} {{ $sj->first_name ?? 'N/A' }}</td>
                                <td>{{ $sj->email }}</td>
                                <td>{{ $sj->password }}</td>
                                <td>{{ $sj->created_at->diffForHumans() }}</td>

                                <td>
                                    @if ($sj->reviews()->count())
                                        @foreach($sj->reviews as $answer)
                                            <div class="alert @if($answer->status=='posted_one') alert-danger @elseif($answer->status=='posted') alert-success @elseif($answer->is_approved) alert-warning @else alert-info @endif">
                                                <strong>{{ $answer->title }}</strong><br>{{ $answer->review }}
                                            </div>
                                            @if($answer->status!= 'posted' && $answer->status!= 'posted_one')
                                                <a href="" class="btn btn-sm btn-info">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="" class="btn btn-sm btn-danger">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            @endif
                                        @endforeach
                                    @else
                                        <div class="accordion" id="accordionExample">
                                            <div class="card mt-0" style="width:400px;">
                                                <div class="card-header">
                                                    <div style="cursor: pointer;font-size: 20px;font-weight: bolder;" data-toggle="collapse" data-target="#form_am" aria-expanded="true" aria-controls="form_am">
                                                        Attach A New Review
                                                    </div>
                                                </div>
                                                <div id="form_am" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                                    <div class="card-body">
                                                        <form action="{{ action('ReviewController@store') }}" method="post">
                                                            @csrf
                                                            <input type="hidden" name="account_id" value="{{ $sj->id }}">
                                                            <div class="form-group">
                                                                <input name="title" type="text" class="form-control" placeholder="Enter Title...">
                                                            </div>
                                                            <div class="form-group">
                                                                <textarea class="form-control" name="review" id="review_{{$key+1}}" rows="3" placeholder="Enter Body..."></textarea>
                                                            </div>
                                                            <div class="text-right">
                                                                <button class="btn btn-success">Attach A Review</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                                <td class="text-center">{!! $sj->is_approved==1 ? '<img src="/images/active.png" style="width:20px;">' : '<img src="/images/inactive.png" style="width:20px;">'!!}</td>
                                <td class="text-center">{!! $sj->status=='posted' ? '<img src="/images/active.png" style="width:20px;">' : '<img src="/images/inactive.png" style="width:20px;">'!!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="tab-pane active mt-3" id="two">
                    <table id="table2" class="table table-striped">
                        <thead>
                            <tr>
                                <th>I.D</th>
                                <th>Question</th>
                                <th>Answers</th>
                                <th>Reply</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($sjs as $kkk=>$sj)
                            <tr>
                                <th>{{$kkk+1}}</th>
                                <th>{{$sj->text}}</th>
                                <td>
                                    <table class="table table-striped">
                                        @foreach($sj->answers as $answer)
                                            <tr>
                                                <td>{{ $answer->author }} <span class="badge badge-success">{{$answer->type}}</span> @if ($answer->type == 'reply') <span class="badge badge-primary">Posted</span> @endif</td>
                                                <td>{{ $answer->text }}</td>
                                            </tr>
                                        @endforeach
                                    </table>
                                </td>
                                <td>
                                    <div class="form-group" style="width: 400px;">
                                        <form action="{{ action('SitejabberQAController@update', $sj->id) }}" method="post">
                                            @csrf
                                            @method('put')
                                            <textarea type="text" name="reply" class="form-control" placeholder="Type reply..."></textarea>
                                            <div class="text-right">
                                                <button class="btn btn-success mt-1">Reply To Thread</button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
    <style>
        thead input {
            width: 100%;
        }
    </style>
@endsection

@section('scripts')

    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#table thead tr').clone(true).appendTo( '#table thead' );
            $('#table thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );

                $( 'input', this ).on( 'keyup change', function () {
                    if ( table.column(i).search() !== this.value ) {
                        table
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
            var table = $('#table').dataTable({
                orderCellsTop: true,
                fixedHeader: true
            });

            $('#table2 thead tr').clone(true).appendTo( '#table2 thead' );
            $('#table2 thead tr:eq(1) th').each( function (i) {
                var title = $(this).text();
                $(this).html( '<input type="text" placeholder="Search '+title+'" />' );

                $( 'input', this ).on( 'keyup change', function () {
                    if ( table2.column(i).search() !== this.value ) {
                        table2
                            .column(i)
                            .search( this.value )
                            .draw();
                    }
                } );
            } );
            var table2 = $('#table2').dataTable({
                orderCellsTop: true,
                fixedHeader: true
            });
        });
    </script>
    @if (Session::has('message'))
        <script>
            toastr["success"]("{{ Session::get('message') }}", "Message")
        </script>
    @endif
@endsection