@extends('layouts.app')

@section('title', 'Larave Log List')

@section("styles")
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
     <style type="text/css">
        #loading-image {
            position: fixed;
            top: 50%;
            left: 50%;
            margin: -50px 0px 0px -50px;
        }
    </style>
@endsection

@section('content')
    <div id="myDiv">
        <img id="loading-image" src="/images/pre-loader.gif" style="display:none;"/>
    </div>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Scrap Api Logs</h2>

        </div>
    </div>

    <div class="mt-3 col-md-12">
        <table class="table table-bordered table-striped" id="log-table">
            <thead>
            <tr>
                <th width="10%">#</th>
                <th width="10%">Server id</th>
                <th width="25%">Logs</th>
            </tr>
            @php
                $i = 1;
            @endphp
            
            @foreach ($logs as $log)
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $log->server_id }}</td>
                    <td>{{ $log->log_messages }}</td>
                </tr>
            @endforeach
            </thead>

        </table>
    </div>
 

@endsection