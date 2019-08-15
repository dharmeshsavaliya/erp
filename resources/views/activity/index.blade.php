@extends('layouts.app')


@section('content')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>


    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">

    <section class="dashboard-counts section-padding">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('activity') }}" method="GET" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>User</strong>
                                    <?php
                                    echo Form::select( 'selected_user', $users, $selected_user, [
                                        'class' => 'form-control',
                                        'multiple' => 'multiple',
                                        'id' => 'userList',
                                        'name' => 'selected_user[]'
                                    ] );?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <strong>Date Range</strong>
                                    <input type="text" value="{{ $range_start }}" name="range_start" hidden/>
                                    <input type="text" value="{{ $range_end  }}" name="range_end" hidden/>
                                    <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc; width: 100%">
                                        <i class="fa fa-calendar"></i>&nbsp;
                                        <span></span> <i class="fa fa-caret-down"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <strong>&nbsp;</strong>
                                <button type="submit" class="btn btn-secondary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="dashboard-counts section-padding">
        <div class="container-fluid">
            <div class="row">
                <strong>Product stats totals</strong>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Uncropped</th>
                        <th>Crop Approval</th>
                        <th>Crop Rejected</th>
                        <th>On Sequencing/Approved</th>
                        <th>Attribute Approved</th>
                        <th>Attribute Rejected</th>
                        <th>Magento Listed</th>
                        <th>Total</th>
                    </tr>
                    <tr>
                        <td>{{ $productStats->uncropped }}</td>
                        <td>{{ $productStats->crop_approval }}</td>
                        <td>{{ $productStats->crop_rejected }}</td>
                        <td>{{ $productStats->left_for_sequencing }}</td>
                        <td>{{ $productStats->total_approvals }}</td>
                        <td>{{ $productStats->total_rejections }}</td>
                        <td>
                            {{ $productStats->total_listed }}
                        </td>
                        <td>
                            {{ $productStats->crop_approval + $productStats->uncropped + $productStats->crop_rejected + $productStats->left_for_sequencing + $productStats->total_approvals + $productStats->total_rejections }}
                        </td>
                    </tr>
                </table>
            </div>
            @if ( isset($_GET['range_start']) )
                <div class="row">
                    <strong>Product stats by date range</strong>
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th>Uncropped</th>
                            <th>Crop Approval</th>
                            <th>Crop Rejected</th>
                            <th>On Sequencing/Approved</th>
                            <th>Attribute Approved</th>
                            <th>Attribute Rejected</th>
                            <th>Magento Listed</th>
                            <th>Total</th>
                        </tr>
                        <tr>
                            <td>{{ $productStatsDateRange->uncropped }}</td>
                            <td>{{ $productStatsDateRange->crop_approval }}</td>
                            <td>{{ $productStatsDateRange->crop_rejected }}</td>
                            <td>{{ $productStatsDateRange->left_for_sequencing }}</td>
                            <td>{{ $productStatsDateRange->total_approvals }}</td>
                            <td>{{ $productStatsDateRange->total_rejections }}</td>
                            <td>
                                {{ $productStatsDateRange->total_listed }}
                            </td>
                            <td>
                                {{ $productStatsDateRange->crop_approval + $productStatsDateRange->uncropped + $productStatsDateRange->crop_rejected + $productStatsDateRange->left_for_sequencing + $productStatsDateRange->total_approvals + $productStatsDateRange->total_rejections }}
                            </td>
                        </tr>
                    </table>
                </div>
            @endif

            <div class="row">

                <table class="table table-striped table-bordered">
                    <tr>
                        <th colspan="2"><strong>AI Activity (Totals)</strong></th>
                        @if ( isset($_GET['range_start']) )
                            <th colspan="2"><strong>AI Activity (By Date Range)</strong></th>
                        @endif
                    </tr>
                    <tr>
                        <th>Total Products</th>
                        <th>Run by AI</th>
                        @if ( isset($_GET['range_start']) )
                            <th>New Products</th>
                            <th>Run by AI</th>
                        @endif
                    </tr>
                    <tr>
                        <td>{{ $aiActivity['total'] }}</td>
                        <td>{{ $aiActivity['ai'] }}</td>
                        @if ( isset($_GET['range_start']) )
                            <td>{{ $aiActivity['total_range'] }}</td>
                            <td>{{ $aiActivity['ai_range'] }}</td>
                        @endif
                    </tr>
                </table>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <strong>Crop Rate: </strong> {{ $cropCountPerMinute }} Per Minute (average of the last 24 hours)
                </div>
            </div>
            <div class="row">
                <table class="table table-bordered">
                    <tr>
                        <th>New Scrapping</th>
                        <th>Inventory</th>
                        <th>Crop Approval</th>
                        <th>Crop Rejection</th>
                        <th>Crop Confirmation</th>
                        <th>Crop Sequencing</th>
                        <th>Attribute Approval</th>
                        <th>Attribute Rejected</th>
                    </tr>
                    <tr>
                        <td>{{ $scrapCount }}</td>
                        <td>{{ $inventoryCount }}</td>
                        @if ( $allActivity->crop_approval_denied > 0 )
                            <td>{{ $allActivity->crop_approved }} - {{ $allActivity->crop_approval_denied }} = {{ $allActivity->crop_approved - $allActivity->crop_approval_denied }}</td>
                        @else
                            <td>{{ $allActivity->crop_approved }}</td>
                        @endif
                        <td>{{ $allActivity->crop_rejected }}</td>
                        <td>{{ $allActivity->crop_approval_confirmation }}</td>
                        <td>{{ $allActivity->crop_ordered }}</td>
                        <td>{{ $allActivity->attribute_approved }}</td>
                        <td>{{ $rejectedListingsCount - $allActivity->attribute_rejected }}</td>
                    </tr>
                </table>
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>Crop Approval</th>
                        <th>Crop Rejection</th>
                        <th>Crop Confirmation</th>
                        <th>Crop Sequencing</th>
                        <th>Attribute Approval</th>
                        <th>Attribute Rejection</th>
                        <th>Magento Listed</th>
                    </tr>
                    @foreach ($userActions as $key => $user)
                        <tr>
                            <td>{{ $users[$user->user_id] }}</td>
                            @if ( $user->crop_approval_denied > 0 )
                                <td>{{ $user->crop_approved }} - {{ $user->crop_approval_denied }} = {{ $user->crop_approved - $user->crop_approval_denied }}</td>
                            @else
                                <td>{{ $user->crop_approved }}</td>
                            @endif
                            <td>{{ $user->crop_rejected }}</td>
                            <td>{{ $user->crop_approval_confirmation }}</td>
                            <td>{{ $user->crop_ordered }}</td>
                            <td>{{ $user->attribute_approved }}</td>
                            <td>{{ $user->attribute_rejected }}</td>
                            <td>{{ $user->magento_listed }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </section>

    {{-- <section class="dashboard-counts section-padding">
         <div class="container-fluid">
             <div class="row">
                 <div class="col-lg-12">
                     --}}{{--<div class="col-xl-6 col-md-6">--}}{{--
                     <div class="row">
                         <div class="card activity-chart">
                             <div class="card-header d-flex align-items-center">
                                 <h4>Activity Chart</h4>
                             </div>
                             <div class="card-body">
                                 <canvas id="ActivityChart"></canvas>
                             </div>
                         </div>
                     </div>
                     --}}{{--</div>--}}{{--
                 </div>
             </div>
         </div>
     </section>--}}


    <script>

        jQuery('#userList').select2(
            {
                placeholder: 'Select a User'
            }
        );
    </script>


    <script type="text/javascript">
        $(function () {

            let r_s = jQuery('input[name="range_start"]').val();
            let r_e = jQuery('input[name="range_end"]').val()

            let start = r_s ? moment(r_s, 'YYYY-MM-DD') : moment().subtract(6, 'days');
            let end = r_e ? moment(r_e, 'YYYY-MM-DD') : moment();

            // jQuery('input[name="range_start"]').val(start.format('YYYY-MM-DD'));
            // jQuery('input[name="range_end"]').val(end.format('YYYY-MM-DD'));

            function cb(start, end) {
                $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            }

            $('#reportrange').daterangepicker({
                startDate: start,
                maxYear: 1,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            }, cb);

            cb(start, end);

        });

        $('#reportrange').on('apply.daterangepicker', function (ev, picker) {

            jQuery('input[name="range_start"]').val(picker.startDate.format('YYYY-MM-DD'));
            jQuery('input[name="range_end"]').val(picker.endDate.format('YYYY-MM-DD'));

        });

    </script>

    {{--<script>

        /*global $, document, LINECHARTEXMPLE*/
        $(document).ready(function () {

            'use strict';

            let brandPrimary = 'rgba(51, 179, 90, 1)';

            let ActivityChart = $('#ActivityChart');

            var barChartExample = new Chart(ActivityChart, {
                type: 'bar',
                data: {
                    // labels: ["SELECTIONS", "SEARCHES", "ATTRIBUTE", "IMAGECROPPING", "APPROVALS", "LISTINGS", "SALES"],
                    labels: ["Mon", "Tue", "Wed", "Thus", "Fri", "Sat", "Sun"],
                    datasets: [
                        {
                            label: "Work Done",

                            backgroundColor: '#5EBA31' ,
                            // data: [65, 59, 80, 81, 56, 55, 140],
                            data: [
                                @foreach($total_data as $key => $value)
                                {{ $value.',' }}
                                @endforeach
                            ],
                        },
                        {
                            label: "Benchmark",
                            // backgroundColor: ['rgba(203, 203, 203, 0.6)',],
                            backgroundColor: '#5738CA' ,
                            // backgroundColor: '#FF0000',

                            // data: [35, 40, 60, 47, 88, 27, 30],
                            data: [
                                @foreach($benchmark as $key => $value)
                                {{ $value.',' }}
                                @endforeach
                            ],
                        }
                    ],
                },
                options: {
                    scaleShowValues: true,
                    scales: {
                        yAxes: [{
                            // stacked: true,
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            // stacked: true,
                            ticks: {
                                autoSkip: false
                            }
                        }]
                    }
                }
            });

        });
    </script>--}}

@endsection

{{--
/*backgroundColor: [
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)',
'rgba(51, 179, 90, 0.6)'
],
*/
/*borderColor: [
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)',
'rgba(51, 179, 90, 1)'
],*/

borderColor: [
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)',
'rgba(203, 203, 203, 1)'
],--}}
