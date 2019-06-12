@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram Accounts ({{$total}})</h2>
        </div>
    </div>
    <div class="row">
        <div class="p-5" style="background: #dddddd">
            <form action="{{ action('InstagramController@store') }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="first_name">Full name</label>
                            <input class="form-control" type="text" id="first_name" name="first_name" placeholder="Full name">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="first_name">Instagram Username</label>
                            <input class="form-control" type="text" id="last_name" name="last_name" placeholder="Username">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="text" name="password" id="password" class="form-control" placeholder="Password">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="checkbox" id="broadcast" name="broadcast">
                            <label for="broadcast">Direct Messaging</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="checkbox" id="manual_comments" name="manual_comments">
                            <label for="manual_comments">Manual Comments</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <input type="checkbox" id="bulk_comments" name="bulk_comments">
                            <label for="bulk_comments">Bulk Comments</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label for="email">Phone/Email</label>
                        <input class="form-control" type="text" name="email" id="email" placeholder="Email/phone">
                    </div>
                    <div class="col-md-4">
                        <label for="country">Country</label>
                        <select class="form-control" name="country" id="country">
                            <option value="">All</option>
                            @foreach($countries as $country)
                                <option value="{{$country->region}}">{{$country->region}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="gender">Gender</label>
                        <select name="gender" id="gender" class="form-control">
                            <option value="all">Any</option>
                            <option value="female">Female</option>
                            <option value="male">Male</option>
                        </select>
                    </div>
                    <div class="col-md-12 text-right">
                        <div class="form-group">
                            <button class="btn btn-default">Add Account</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12 mt-2">
            <form action="{{ action('InstagramController@accounts') }}" method="get">
                <div class="row">
                    <div class="col-md-3">
                        <input value="{{$request->get('query')}}" class="form-control" type="text" name="query" id="query" placeholder="Username or email..">
                    </div>
                    <div class="col-md-2">
                        <select class="form-control" name="filter" id="filter">
                            <option value="all">All</option>
                            <option value="broadcast">DM</option>
                            <option value="manual_comment">Manual Comments</option>
                            <option value="bulk_comment">Bulk Comment</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <input {{ $request->get('blocked')=='on' ? 'checked' : '' }} type="checkbox" id="blocked" name="blocked"> <label for="blocked">Blocked</label>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-default">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12 mt-5">
            <div>
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>I.D</th>
                        <th>Name</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th style="width: 100px;">Email/Phone</th>
                        <th>Roles</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($accounts as $key=>$account)
                        @if(Auth::user()->email == 'facebooktest@test.com')
                            @if(substr($account->created_at, 0, 10) == date('Y-m-d'))
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $account->first_name }}</td>
                                    <td>{{ $account->last_name }}</td>
                                    <td>{{ $account->password }}</td>
                                    <td>
                                        <div style="width: 150px;">
                                            {{ $account->email ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        {!! $account->broadcast ? '<span>DM</span>' : '' !!}
                                        {!! $account->manual_comment ? '<span>Manual Cmt</span>' : '' !!}
                                        {!! $account->bulk_comment ? '<span>Bulk Cmt</span>' : '' !!}
                                    </td>
                                    <td>{{ $account->blocked ? 'Blocked' : 'Active' }}</td>
                                    <td>
                                        <div style="width: 100px;">
                                            {{ substr($account->created_at, 0, 10) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="width: 150px !important;">
                                            <a class="btn btn-image" href="{{ action('AccountController@test', $account->id) }}">
                                                <i class="fa fa-check"></i>
                                            </a>
                                            <a href="{{ action('InstagramController@edit', $account->id) }}" class="btn btn-image">
                                                <img src="{{ asset('images/edit.png') }}" alt="Edit User" title="Edit Product">
                                            </a>
                                            <a href="{{ action('InstagramController@deleteAccount', $account->id) }}" class="btn btn-image">
                                                <img src="{{ asset('images/delete.png') }}" alt="Delete User" title="Delete Product">
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @else
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>{{ $account->first_name }}</td>
                                <td>{{ $account->last_name }}</td>
                                <td>{{ $account->password }}</td>
                                <td>{{ $account->email ?? 'N/A' }}</td>
                                <td>
                                    {!! $account->broadcast ? '<span class="label label-default">DM</span>' : '' !!}
                                    {!! $account->manual_comment ? '<span class="label label-default">Manual Cmt</span>' : '' !!}
                                    {!! $account->bulk_comment ? '<span class="label label-default">Bulk Cmt</span>' : '' !!}
                                </td>
                                <td>{{ $account->blocked ? 'Blocked' : 'Active' }}</td>
                                <td>
                                    <div style="width: 100px;">
                                        {{ substr($account->created_at, 0, 10) }}
                                    </div>
                                </td>
                                <td>
                                    <div style="width:150px !important;">
                                        <a class="btn btn-image" href="{{ action('AccountController@test', $account->id) }}">
                                            <i class="fa fa-check"></i>
                                        </a>
                                        <a href="{{ action('InstagramController@edit', $account->id) }}" class="btn btn-image">
                                            <img src="{{ asset('images/edit.png') }}" alt="Edit User" title="Edit Product">
                                        </a>
                                        <a href="{{ action('InstagramController@deleteAccount', $account->id) }}" class="btn btn-image">
                                            <img src="{{ asset('images/delete.png') }}" alt="Delete User" title="Delete Product">
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
            </div>
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
            // var table = $('#table').dataTable({
            //     // orderCellsTop: true,
            //     fixedHeader: true
            // });
            // $('#table thead tr').clone(true).appendTo( '#table thead' );
            // $('#table thead tr:eq(1) th').each( function (i) {
            //     var title = $(this).text();
            //     $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //
            //     $( 'input', this ).on( 'keyup change', function () {
            //         if ( table.column(i).search() !== this.value ) {
            //             table
            //                 .column(i)
            //                 .search( this.value )
            //                 .draw();
            //         }
            //     } );
            // } );

            // $('#table thead tr').clone(true).appendTo( '#table thead' );
            // $('#table thead tr:eq(1) th').each( function (i) {
            //     var title = $(this).text();
            //     $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
            //
            //     $( 'input', this ).on( 'keyup change', function () {
            //         if ( table.column(i).search() !== this.value ) {
            //             table
            //                 .column(i)
            //                 .search( this.value )
            //                 .draw();
            //         }
            //     } );
            // } );
            //
            // var table = $('#table').DataTable({
            //     orderCellsTop: true,
            //     fixedHeader: true
            // });
            //
            //
            // $("#table").addClass('table-bordered');
        });
    </script>
    @if (Session::has('message'))
        <script>
            toastr["success"]("{{ Session::get('message') }}", "Message")
        </script>
    @endif
@endsection