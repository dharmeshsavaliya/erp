@extends('layouts.app')

@section('favicon' , 'scrapergmail.png')

@section('title', 'Gmail Scrapper Info')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Gmail Data</h2>
        </div>
        <div class="col-md-12">
            <table class="table table-striped">
                <tr>
                    <th>SN</th>
                    <th>Page Url</th>
                    <th>Sender</th>
                    <th>Date Sent</th>
                    <th>Images</th>
                    <th>tags</th>
                </tr>
                @foreach($data as $key=>$datum)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td><a href="{{ $datum->page_url }}">Visit</a></td>
                        <td>{{ $datum->sender }}</td>
                        <td>{{ $datum->received_at }}</td>
                        <td>
                            @foreach($datum->images as $image)
                                <a href="{{ $image }}">
                                    <img src="{{ $image }}" alt="" style="width: 150px;height: 150px;">
                                </a>
                            @endforeach
                        </td>
                        <td>
                            @foreach($datum->tags as $tag)
                                <li>{{ $tag }}</li>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/media-card.css') }}">
@endsection

@section('scripts')
    <script>

    </script>
@endsection
