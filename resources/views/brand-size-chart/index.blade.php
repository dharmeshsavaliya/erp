@extends('layouts.app')

@section('content')
    <style type="text/css">
        .nav-pills>li.active>a, .nav-pills>li.active>a:focus, .nav-pills>li.active>a:hover {
            color: #fff;
            background-color: #5A6267;
        }

        a {
            color: #5A6267;
            text-decoration: none;
        }

        a:focus, a:hover{
            color: #5A6267;
        }

    </style>
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">Brand Size Chart List </h2>
            <div class="pull-left">
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('brand/create/size/chart') }}">+</a>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div id="exTab1" class="container">	
                <ul  class="nav nav-pills">
                    @forelse ($storeWebsite as $key => $item)
                        <li class="@if($loop->first) active @endif">
                            <a  href="#tab_div_{{ $key + 1 }}" data-toggle="tab">{{ $item->title }}</a>
                        </li>
                    @empty
                    @endforelse
                </ul>
        
                <div class="tab-content clearfix">
                    @forelse ($storeWebsite as $key => $item)
                        <div class="tab-pane @if($loop->first) active @endif" id="tab_div_{{ $key + 1 }}">
                            <div class="table-responsive mt-3">
                                <table class="table table-bordered">
                                    <tr>
                                        <th></th>
                                        @forelse ($item->categories as $catkey => $catitem)
                                            <th>{{ $catitem->title }}</th>
                                        @empty
                                            <th></th>
                                        @endforelse
                                    </tr>
                                    @forelse ($item->brands as $brandkey => $branditem)
                                        <tr>
                                            <th>{{ $branditem->name }}</th>
                                            @forelse ($item->categories as $catkey => $catitem)
                                                <td>
                                                @forelse ($sizeChart as $chartitem)
                                                    @if($chartitem->category_id == $catitem->id && $chartitem->brand_id == $branditem->id)
                                                        @if ($chartitem->hasMedia(config('constants.size_chart_media_tag')))
                                                            <span class="td-mini-container">
                                                                <img src="{{ $chartitem->getMedia(config('constants.size_chart_media_tag'))->first()->getUrl() }}" class="img-responsive thumbnail-200 mb-1">
                                                            </span>
                                                            <span class="td-full-container hidden">
                                                                <img src="{{ $chartitem->getMedia(config('constants.size_chart_media_tag'))->first()->getUrl() }}" class="img-responsive thumbnail-200 mb-1">
                                                            </span>
                                                        @endif
                                                    @endif
                                                @empty
                                                @endforelse
                                                </td>
                                            @empty
                                                <td></td>
                                            @endforelse
                                        </tr>
                                    @empty
                                        <tr>
                                            <th></th>
                                        </tr>
                                    @endforelse
                                </table>
                            </div>
                        </div>
                    @empty
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">

</script>
@endsection
