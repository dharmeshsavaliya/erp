@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <h2 class="page-heading">Instagram HashTags</h2>
        </div>
        <div class="col-md-12">
            @if(Session::has('message'))
                <script>
                    alert("{{Session::get('message')}}")
                </script>
            @endif
            <form method="post" action="{{ action('HashtagController@store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Hashtag (without # symbol)</label>
                            <input type="text" name="name" id="name" placeholder="sololuxuryindia (without hash)" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Add?</label>
                            <button class="btn-block btn btn-default">Add Hashtag</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <table class="table-striped table-bordered table table-sm">
                <tr>
                    <th>S.N</th>
                    <th>Tag Name</th>
                    <th>Count</th>
                    <th>Rating</th>
                    <th>Actions</th>
                </tr>
                @foreach($hashtags as $key=>$hashtag)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            <a href="{{ action('HashtagController@showGrid',$hashtag->id) }}">
                                {{ $hashtag->hashtag }}
                            </a>
                        </td>
                        <td>{{$hashtag->post_count}}</td>
                        <td>{{ $hashtag->rating }}</td>
                        <td>
                            <form method="post" action="{{ action('HashtagController@destroy', $hashtag->id) }}">
                                <a class="btn btn-default btn-image" href="{{ action('HashtagController@showGrid', $hashtag->id) }}">
                                    <img src="{{ asset('images/view.png') }}" alt="">
                                </a>
                                <a class="btn btn-default btn-image" href="{{ action('HashtagController@edit', $hashtag->hashtag) }}">
                                    <i class="fa fa-info"></i> Relavent Hashtags
                                </a>
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-default btn-image btn-sm">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
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
        var cid = null;
        $(function(){
            $('.show-details').on('click',function() {
                let id = $(this).attr('data-pid');
                $('.reveal-'+id).slideToggle('slow');
            });

            $('.card-reveal .close').on('click',function(){
                $(this).parent().slideToggle('slow');
            });

        });
    </script>
@endsection