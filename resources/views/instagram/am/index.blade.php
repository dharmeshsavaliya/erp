@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram Automated Messages</h2>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="accordion" id="accordionExample">
                <div class="card">
                    <div class="card-header">
                        <div style="cursor: pointer;font-size: 20px;font-weight: bolder;" data-toggle="collapse" data-target="#form_am" aria-expanded="true" aria-controls="form_am">
                            Create New Reply / DM
                        </div>
                    </div>
                    <div id="form_am" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                        <div class="card-body">
                            <form method="post" action="{{ action('InstagramAutomatedMessagesController@store') }}">
                                @csrf
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="type">Type</label>
                                            <select class="form-control" name="type" id="type">
                                                <option value="text">Text</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sender_type">Sender Type</label>
                                            <select class="form-control" name="sender_type" id="sender_type">
                                                <option value="normal">Normal</option>
                                                <option disabled value="legit">Paid / Legit Accounts</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="receiver_type">Receiver Type</label>
                                            <select class="form-control" name="receiver_type" id="receiver_type">
                                                <option value="hashtag">Hahstags</option>
                                                <option value="inf_dm">DM Influencers</option>
                                                <option disabled value="product_inquiry">Product Inquiry / Query</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="reusable">Reusable</label>
                                            <select class="form-control" name="reusable" id="reusable">
                                                <option value="0">Only once</option>
                                                <option disabled value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <textarea class="form-control" name="message" id="message" rows="4" placeholder="Type message or reply..."></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <button class="btn btn-info btn-block">Add Message</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <br>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="table" class="table table-striped">
                <thead>
                    <tr>
                        <th>S.N</th>
                        <th>Type</th>
                        <th>Sender Type</th>
                        <th>Receiver Type</th>
                        <th>Message</th>
                        <th>Status</th>
                        <th>Reusable</th>
                        <th>Used for</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($replies as $key=>$reply)
                        <tr>
                            <td>{{$key+1}}</td>
                            <td>{{$reply->type}}</td>
                            <td>{{$reply->sender_type}}</td>
                            <td>{{$reply->receiver_type}}</td>
                            <td>{{$reply->message}}</td>
                            <td class="text-center">{!! $reply->status ? '<img src="/images/active.png" style="width:20px;">' : '<img src="/images/inactive.png" style="width:20px;">'!!}</td>
                            <td class="text-center">{!! $reply->reusable ? '<img src="/images/active.png" style="width:20px;">' : '<img src="/images/inactive.png" style="width:20px;">'!!}</td>
                            <td>{{ $reply->use_count }}</td>
                            <td>
                                <form method="post" action="{{ action('InstagramAutomatedMessagesController@destroy', $reply->id) }}">
                                    @csrf
                                    @method('delete')
                                    <button class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('styles')
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
        });
    </script>
    @if (Session::has('message'))
        <script>
            toastr["success"]("{{ Session::get('message') }}", "Message")
        </script>
    @endif
@endsection