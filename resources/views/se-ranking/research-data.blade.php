@extends('layouts.app')
@section('title', 'SE Ranking Data')
@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <h2 class="page-heading">SE Ranking - Domain Overview (SEO/PPC Research Data)</h2>
        </div>
        {{-- <form action="{{route('filteredAnalyticsResults')}}" method="get" class="form-inline float-right">
            <div class="form-group">
                <div class="col-md-4 col-lg-6 col-xl-6">
                    <input name="location" type="text" placeholder="City/Country" class="form-control" value="{{!empty(request()->location) ? request()->location : ''}}">
                </div>
            </div>
            <div class="form-group">
                <div class="col-md-4 col-lg-6 col-xl-6">
                    <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
                </div>
            </div>
        </form> --}}
    </div>
    <div class="container">
        <div class="row">
            @include('se-ranking.buttons-area')
            <div class="col-md-12">
                <table class="table" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Source</th>
                            <th>Year Month</th>
                            <th>Organic Keyword Count</th>
                            <th>Organic Price Sum</th>
                            <th>Organic Traffic Sum</th>
                            <th>Paid Keyword Count</th>
                            <th>Paid Price Sum</th>
                            <th>Paid Traffic Sum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $new_items = App\Helpers::customPaginator(request(), $r_data, 25);
                        @endphp
                        @foreach ($new_items as $key => $data)
                            <tr>
                                <td>{{$data->source}}</td>
                                <td>{{$data->month}}, {{$data->year}}</td>
                                <td>{{$data->organic->keywords_count}}</td>
                                <td>{{$data->organic->price_sum}}</td>
                                <td>{{$data->organic->traffic_sum}}</td>
                                <td>{{$data->adv->traffic_sum}}</td>
                                <td>{{$data->adv->price_sum}}</td>
                                <td>{{$data->adv->keywords_count}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="text-center">
                    {!! $new_items->links() !!}
                </div>
                {{-- <nav>
                    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link" id="nav-home-tab" data-toggle="tab" href="#nav-home" role="tab" aria-controls="nav-home" aria-selected="true">Project Tab 1</a>
                        <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#nav-profile" role="tab" aria-controls="nav-profile" aria-selected="false">Project Tab 2</a>
                        <a class="nav-item nav-link" id="nav-contact-tab" data-toggle="tab" href="#nav-contact" role="tab" aria-controls="nav-contact" aria-selected="false">Project Tab 3</a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
                        
                    </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                        <table class="table" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Project Name</th>
                                    <th>Employer</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="#">Work 1</a></td>
                                    <td>Doe</td>
                                    <td>john@example.com</td>
                                </tr>
                                <tr>
                                    <td><a href="#">Work 2</a></td>
                                    <td>Moe</td>
                                    <td>mary@example.com</td>
                                </tr>
                                <tr>
                                    <td><a href="#">Work 3</a></td>
                                    <td>Dooley</td>
                                    <td>july@example.com</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab">
                        <table class="table" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Contest Name</th>
                                    <th>Date</th>
                                    <th>Award Position</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><a href="#">Work 1</a></td>
                                    <td>Doe</td>
                                    <td>john@example.com</td>
                                </tr>
                                <tr>
                                    <td><a href="#">Work 2</a></td>
                                    <td>Moe</td>
                                    <td>mary@example.com</td>
                                </tr>
                                <tr>
                                    <td><a href="#">Work 3</a></td>
                                    <td>Dooley</td>
                                    <td>july@example.com</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection