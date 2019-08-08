<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield ('title', 'ERP') - {{ config('app.name') }}</title>

    <!-- CSRF Token -->

    <meta name="csrf-token" content="{{ csrf_token() }}">



    {{-- <title>{{ config('app.name', 'ERP for Sololuxury') }}</title> --}}



    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/css/bootstrap-select.min.css">
    <script>
        let Laravel = {};
        Laravel.csrfToken = "{{csrf_token()}}";
        window.Laravel = Laravel;
    </script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.5/js/bootstrap-select.min.js"></script>
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script> --}}

    {{-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script> --}}

    {{-- When jQuery UI is included tooltip doesn't work --}}
    {{-- <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script> --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.js"></script>

    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script type="text/javascript" src="//media.twiliocdn.com/sdk/js/client/v1.6/twilio.min.js"></script>

    <script type="text/javascript" src="https://unpkg.com/tabulator-tables@4.0.5/dist/js/tabulator.min.js"></script>

    <script src="{{ asset('js/bootstrap-notify.js') }}"></script>

    @if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 23 || Auth::id() == 56)
      <script src="{{ asset('js/calls.js') }}"></script>
    @endif

    <script src="{{ asset('js/custom.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/bootstrap-slider.min.js"></script>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jquery.lazy/1.7.9/jquery.lazy.min.js"></script>



    {{-- @if( str_contains(Route::current()->getName(),['sales','activity','leads','task','home', 'customer'] ) ) --}}

        <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css" rel="stylesheet"/>

        <script type="text/javascript"

                src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.min.js"></script>

    {{-- @endif --}}



    @if(Auth::user())

        {{--<link href="{{ url('/css/chat.css') }}" rel="stylesheet">--}}

        <script>

            window.userid = {{Auth::user()->id}};

            window.username = "{{Auth::user()->name}}";

            loggedinuser = {{Auth::user()->id}};

        </script>

    @endif



<!-- Fonts -->

    <link rel="dns-prefetch" href="https://fonts.gstatic.com">

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">



    <!-- Styles -->

    <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">



    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">

    {{-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">

    <link rel="stylesheet"

          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-slider/10.3.3/css/bootstrap-slider.min.css">

    <link href="https://unpkg.com/tabulator-tables@4.0.5/dist/css/tabulator.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.0/fullcalendar.min.css">

    @yield("styles")

    <script>

        window.Laravel = {!! json_encode([

            'csrfToken'=> csrf_token(),

            'user'=> [

                'authenticated' => auth()->check(),

                'id' => auth()->check() ? auth()->user()->id : null,

                'name' => auth()->check() ? auth()->user()->name : null,

                ]

            ])

        !!};

    </script>



    {{-- <script src="https://js.pusher.com/4.3/pusher.min.js"></script>

    <script>
      // Enable pusher logging - don't include this in production
      Pusher.logToConsole = true;

      var pusher = new Pusher('df4fad9e0f54a365c85c', {
          cluster: 'ap2',
          forceTLS: true
      });
    </script> --}}

    @if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 23 || Auth::id() == 56)

        <script>

            initializeTwilio();

        </script>

    @endif

    {{-- <script src="{{ asset('js/pusher.chat.js') }}"></script>

    <script src="{{ asset('js/chat.js') }}"></script> --}}



</head>

<body>

  <div class="modal fade" id="instructionAlertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Instruction Reminder</h3>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <a href="" id="instructionAlertUrl" class="btn btn-secondary mx-auto">OK</a>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="developerAlertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Developer Task Reminder</h3>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <a href="" id="developerAlertUrl" class="btn btn-secondary mx-auto">OK</a>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="masterControlAlertModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h3 class="modal-title">Master Control Alert</h3>
        </div>
        <div class="modal-body">
          ...
        </div>
        <div class="modal-footer">
          <a href="" id="masterControlAlertUrl" class="btn btn-secondary mx-auto">OK</a>
        </div>
      </div>
    </div>
  </div>

{{-- <div id="fb-root"></div> --}}



<div class="notifications-container">

    <div class="stack-container stacked" id="leads-notification"></div>

    <div class="stack-container stacked" id="orders-notification"></div>

    {{-- <div class="stack-container stacked" id="messages-notification"></div> --}}

    <div class="stack-container stacked" id="tasks-notification"></div>

</div>



<div id="app">

    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">

        <!--<div class="container container-wide">-->

        <div class="container-fluid">

            <a class="navbar-brand" href="{{ url('/task') }}">

                {{ config('app.name', 'Laravel') }}

            </a>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"

                    aria-controls="navbarSupportedContent" aria-expanded="false"

                    aria-label="{{ __('Toggle navigation') }}">

                <span class="navbar-toggler-icon"></span>

            </button>



            <div class="collapse navbar-collapse" id="navbarSupportedContent">

                <!-- Left Side Of Navbar -->

                <ul class="navbar-nav mr-auto">



                </ul>



                <!-- Right Side Of Navbar -->

                <ul class="navbar-nav ml-auto " style="text-align: center;">

                    <!-- Authentication Links -->

                    @guest

                    <li class="nav-item">

                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>

                    </li>

                    {{--<li class="nav-item">

                        <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>

                    </li>--}}

                    @else





                        @include('partials.notifications')

                        {{-- <li class="nav-item">
                            <a class="nav-link" href="{{ route('pushNotification.index') }}">New Notifications</a>
                        </li> --}}




                        {{-- <li class="nav-item dropdown" data-count="

                             {{ \App\Http\Controllers\NotificaitonContoller::salesCount() }}">

                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                Sale<span class="caret"></span>

                            </a>

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                <a class="dropdown-item" href="{{ route('sales.index') }}">Sale List</a>

                                <a class="dropdown-item" href="{{ route('sales.create') }}">Add new</a>

                            </div>

                        </li> --}}
                        @can('crm')
                        <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="{{ route('task.index') }}">Task</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('mastercontrol.index') }}">Master Control</a>
                                    <a class="dropdown-item" href="{{ route('dailyplanner.index') }}">Daily Planner</a>
                                    <a class="dropdown-item" href="{{ route('task.list') }}">Tasks List</a>
                                </li>
                            </ul>
                        </li>
                        @endcan
                        @can('admin')
                           {{-- Product Menu Started --}}
                            <li class="nav-item dropdown">
                                @if(Auth::user()->email != 'facebooktest@test.com')
                                    <a class="nav-link dropdown-toggle"  data-toggle="dropdown">Product
                                    <span class="caret"></span></a>
                                @endif
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu">
                                        <a class="test" tabindex="-1" href="#">Listing <span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <li class="dropdown-submenu">
                                                <a class="test" href="#">Selection <span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="{{ route('productselection.index') }}">Selections
                                                    Grid</a></li>
                                                    @can('selection-create')
                                                        <li><a href="{{ route('productselection.create') }}">Add New</a></li>
                                                    @endcan
                                                    @can('crm')
                                                    <li><a href="{{ action('ScrapController@excel_import') }}">Import Excel Document Type 1</a></li>
                                                    @endcan
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a class="test" href="#">Supervisor <span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="{{ route('productsupervisor.index') }}">Supervisor Grid</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a class="test" href="#">Image Cropper <span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="{{ route('productimagecropper.index') }}">Image Cropper Grid</a></li>
                                                    <li><a href="{{ action('ProductCropperController@getApprovedImages') }}">Approved Crop grid</a></li>
                                                    <li><a href="{{ action('ProductCropperController@getListOfImagesToBeVerified') }}">Crop Approval Grid</a></li>
                                                    <li><a href="{{ action('ProductCropperController@cropIssuesPage') }}">Crop Issue Summary</a></li>
                                                    <li><a href="{{ action('ProductCropperController@showRejectedCrops') }}">Crop-Rejected Grid</a></li>
                                                    <li><a href="{{ action('ProductCropperController@showCropVerifiedForOrdering') }}">Crop-Sequencer</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a class="test" href="#">Attribute <span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    @can ('product-lister')
                                                        <li><a href="{{ route('products.listing') }}?cropped=on">Attribute Edit Page</a></li>
                                                    @endcan
                                                    @can ('approved-listing')
                                                        <li><a href="{{ action('ProductController@approvedListing') }}?cropped=on">Approved Listing</a></li>
                                                    @endcan
                                                    @can ('rejected-listing')
                                                        <li><a href="{{ action('ProductController@showRejectedListedProducts') }}">Rejected Listings</a></li>
                                                    @endcan
                                                    @can('admin')
                                                        <li><a href="{{ action('ProductController@approvedListing') }}">Approved Listing</a></li>
                                                        <li><a href="{{ action('AttributeReplacementController@index') }}">Attribute Replacement</a></li>
                                                    @endcan    
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a class="test" href="#">Stats <span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    @can('admin')
                                                        <li><a href="{{ action('ProductController@productStats') }}">Product Statics</a></li>
                                                            <li><a href="{{ action('ProductController@showAutoRejectedProducts') }}">Auto Rejected Statistics</a></li>
                                                                <li><a href="{{ action('ListingPaymentsController@index') }}">Product Listing Payment</a></li>
                                                    @endcan
                                                    @can ('crm')
                                                        <li><a href="{{ action('ScrapStatisticsController@index') }}">Scrap Statistics</a></li>
                                                        <li><a href="{{ route('scrap.activity') }}">Scrap Activity</a></li>
                                                        <li><a href="{{ action('ScrapController@showProductStat') }}">Products Scraped</a></li>
                                                    @endcan
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a class="test" href="#">Approver <span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="{{ route('productapprover.index') }}">Approver Grid</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a class="test" href="#">In Stock <span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    <li><a href="{{ route('productinventory.instock') }}">In stock</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a class="test" href="#">Inventory <span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('productinventory.index') }}">Inventory Grid</a></li>
                                                    <li><a class="dropdown-item" href="{{ route('productinventory.list') }}">Inventory List</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a class="test" href="#">Quick Sell <span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('quicksell.index') }}">Quick Sell</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>
                                    @can('purchase')
                                        <li class="dropdown-submenu">
                                            <a class="test" tabindex="-1" href="#">Purchase <span class="caret"></span></a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ route('purchase.index') }}">Purchases</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('purchase.calendar') }}">Purchase Calendar</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('purchase.grid') }}">Purchase Grid</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('purchase.grid', 'canceled-refunded') }}">Canc\Ref Grid</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('purchase.grid', 'ordered') }}">Ordered Grid</a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('purchase.grid', 'delivered') }}">Delivered Grid</a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endcan
                                    @can ('crm')
                                        <li>
                                            <a href="{{ route('supplier.index') }}">Suppliers List</a>
                                        </li>
                                    @endcan
                                    @can ('crm')
                                        <li class="dropdown-submenu">
                                            <a class="test" tabindex="-1" href="#">Scrap <span class="caret"></span></a>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a href="{{ action('SalesItemController@index') }}">Sales Item</a>
                                                </li>
                                                <li>
                                                    <a href="{{ action('DesignerController@index') }}">Designer List</a>
                                                </li>
                                                <li>
                                                    <a href="{{ action('GmailDataController@index') }}">Gmail Inbox</a>
                                                </li>
                                                <li>
                                                    <a href="{{ action('ScrapController@index') }}">Google Images</a>
                                                </li>
                                                <li>
                                                    <a href="{{ action('SocialTagsController@index') }}">Social Tags</a>
                                                </li>
                                                <li>
                                                    <a href="{{ action('DubbizleController@index') }}">Dubbizle</a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
                            {{-- Product menu Ended --}}
                        @else

                            <li class="nav-item dropdown">

                                @if(Auth::user()->email != 'facebooktest@test.com')
                                  {{-- @can('admin') --}}
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Product<span class="caret"></span>

                                    </a>
                                  {{-- @endcan --}}

                                @endif

                                @can ('admin')
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                    <a class="dropdown-item" href="{{ route('quicksell.index') }}">Quick Sell</a>

                                </div>
                              @endcan

                                @can ('product-lister')
                                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('products.listing') }}">Attribute Edit Page</a>
                                    @can ('approved-listing')
                                      <a class="dropdown-item" href="{{ action('ProductController@approvedListing') }}?cropped=on">Approved Listing</a>
                                    @endcan
                                  </div>
                                @endcan

                                @can('admin')
                                        <a class="dropdown-item" href="{{ action('ProductController@approvedListing') }}">Approved Listing</a>
                                    @endcan

                            </li>

                            @can('inventory-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Inventory<span class="caret"></span>

                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productinventory.index') }}">Inventory

                                            Grid</a>

                                        <a class="dropdown-item" href="{{ route('productinventory.instock') }}">In

                                            stock</a>

                                    </div>

                                </li>

                            @endcan



                            @can('approver-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Approver<span class="caret"></span>

                                    </a>

                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productapprover.index') }}">Approver

                                            Grid</a>

                                    </div>

                                </li>

                            @endcan



                            @can('lister-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Lister<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productlister.index') }}">Lister

                                            Grid</a>

                                    </div>

                                </li>

                            @endcan

                            @can('crop-approval')
                              <li class="nav-item dropdown">

                                  <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                     data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                      Image Cropper Grid<span class="caret"></span>

                                  </a>



                                  <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                    {{-- <a class="dropdown-item" href="{{ route('productimagecropper.index') }}">ImageCropper

                                        Grid</a> --}}
                                      @can('crop-approval')
                                        <a class="dropdown-item" href="{{ action('ProductCropperController@getListOfImagesToBeVerified') }}">Crop Approval
                                        Grid</a>
                                      @endcan
                                    {{-- <a class="dropdown-item" href="{{ action('ProductCropperController@showRejectedCrops') }}">Crop-Rejected

                                        Grid</a> --}}

                                  </div>

                              </li>


                          @endcan

                            @can('crop-sequence')
                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Image Cropper Grid<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        {{-- <a class="dropdown-item" href="{{ route('productimagecropper.index') }}">ImageCropper

                                            Grid</a> --}}
                                        @can('crop-sequence')
                                            <a class="dropdown-item" href="{{ action('ProductCropperController@showCropVerifiedForOrdering') }}">Crop Sequencer</a>
                                        @endcan
                                            {{-- <a class="dropdown-item" href="{{ action('ProductCropperController@showRejectedCrops') }}">Crop-Rejected

                                                Grid</a> --}}

                                    </div>

                                </li>


                            @endcan



                            @can('imagecropper-list')

                                <li class="nav-item dropdown"

                                    data-count="{{ \App\Http\Controllers\ProductCropperController::rejectedProductCountByUser() }}">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        ImageCropper<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

{{--                                        <a class="dropdown-item" href="{{ route('productimagecropper.index') }}">ImageCropper--}}

{{--                                            Grid</a>--}}
                                        <a class="dropdown-item" href="{{ action('ProductCropperController@getApprovedImages') }}">Approved Crop

                                            Grid</a>
                                        <a class="dropdown-item" href="{{ action('ProductCropperController@showRejectedCrops') }}">Crop-Rejected

                                            Grid</a>


                                    </div>

                                </li>

                            @endcan



                            @can('supervisor-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Supervisor<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productsupervisor.index') }}">Supervisor Grid</a>

                                        {{--<a class="dropdown-item" href="{{ route('productattribute.list') }}">Searcher List</a>--}}

                                    </div>

                                </li>

                            @endcan



                            {{-- @can('attribute-list')

                                <li class="nav-item dropdown" data-count="{{

                                          \App\Http\Controllers\ProductAttributeController::rejectedProductCountByUser()

                                   }}">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Attribute<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productattribute.index') }}">Attribute

                                            Grid</a>


                                    </div>

                                </li>

                            @endcan --}}



                            @can('searcher-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Searcher<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productsearcher.index') }}">Searcher

                                            Grid</a>

                                        {{--<a class="dropdown-item" href="{{ route('productattribute.list') }}">Searcher List</a>--}}

                                    </div>

                                </li>

                            @endcan



                            @can('selection-list')

                                <li class="nav-item dropdown">

                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                       data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                        Selection<span class="caret"></span>

                                    </a>



                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                        <a class="dropdown-item" href="{{ route('productselection.index') }}">Selections

                                            Grid</a>

                                        {{--                                        <a class="dropdown-item" href="{{route('productselection.list')}}">Selections List</a>--}}

                                        @can('selection-create')

                                            <a class="dropdown-item" href="{{ route('productselection.create') }}">Add

                                                New</a>

                                        @endcan

                                    </div>

                                </li>

                            @endcan

                        @endcan

                        {{-- CRM menu started --}}
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle"  data-toggle="dropdown">CRM
                            <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-submenu">
                                    <a class="test" tabindex="-1" href="#">Customers <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        @can('customer')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('customer.index') }}?type=unread">Customers</a>
                                        </li>
                                        @endcan
                                        @can('crm')
                                        <li class="dropdown-submenu">
                                            <a id="coldLeadsMenu" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">
                                                Cold Leads<span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="coldLeadsMenu">
                                                <a class="dropdown-item" href="{{ action('ColdLeadsController@index') }}?via=hashtags">Via Hashtags</a>
                                                <a class="dropdown-item" href="{{ action('ColdLeadsController@showImportedColdLeads') }}">Imported Cold Leads</a>
                                            </ul>
                                        </li>
                                        <li class="dropdown-submenu">
                                            <a id="coldLeadsMenu" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">
                                                Instructions<span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="coldLeadsMenu">
                                                <a class="dropdown-item" href="{{ route('instruction.index') }}">Instructions</a>
                                                <a class="dropdown-item" href="{{ route('instruction.list') }}">Instructions List</a>
                                            </ul>
                                        </li>
                                        @endcan
                                        <li class="dropdown-submenu">
                                            <a id="coldLeadsMenu" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">
                                                Leeds<span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="coldLeadsMenu">
                                                @can('crm')
                                                    <a class="dropdown-item" href="{{ route('leads.index') }}">Leads</a>
                                                @endcan
                                                @can('lead-create')
                                                    <a class="dropdown-item" href="{{ route('leads.create') }}">Add New</a>
                                                @endcan
                                                @can('crm')
                                                    <a class="dropdown-item" href="{{ route('leads.image.grid') }}">Leads Image Grid</a>
                                                @endcan
                                            </ul>
                                        </li>
                                        @can('crm')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('refund.index') }}">Refunds</a>
                                        </li>
                                        @endcan
                                        @can('order-view')
                                            <li class="dropdown-submenu">
                                                <a class="dropdown-item" href="#">Orders</a>
                                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="coldLeadsMenu">
                                                    <a class="dropdown-item" href="{{ route('order.index') }}">Orders</a>
                                                    @can('order-create')
                                                        <a class="dropdown-item" href="{{ route('order.create') }}">Add Order</a>
                                                    @endcan
                                                    <a class="dropdown-item" href="{{ route('order.products') }}">Order Product
                                                    List</a>
                                                </ul>
                                            </li>
                                        @endcan
                                        @can('review-view')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('complaint.index') }}">Customer Complaints</a>
                                        </li>
                                        @endcan
                                        @can('crm')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('order.missed-calls') }}">Missed calls
                                            List</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('order.calls-history') }}">Calls History</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('stock.index') }}">Inward Stock</a>
                                        </li>
                                        @endcan
                                        @can ('private-viewing')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('stock.private.viewing') }}">Private Viewing</a> 
                                        </li>
                                        @endcan
                                        @can ('delivery-approval')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('deliveryapproval.index') }}">Delivery Approvals</a>
                                        </li> 
                                        @endcan
                                    </ul>
                                    @can('crm')
                                    <li class="dropdown-submenu">
                                        <a class="dropdown-item" href="#">Broadcast</a>
                                        <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="coldLeadsMenu">
                                            <a class="dropdown-item" href="{{ route('broadcast.index') }}">Broadcast Messages</a>
                                            <a class="dropdown-item" href="{{ route('broadcast.images') }}">Broadcast Images</a>                                                
                                            <a class="dropdown-item" href="{{ route('broadcast.calendar') }}">Broadcast Calendar</a>
                                        </ul>
                                    </li>
                                    @endcan
                                <li>
                            </ul>
                        </li>
                        {{-- CRM menu ended --}}
                     
                        @can ('vendor-all')
                          <li class="nav-item dropdown">

                              <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                 data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                  Vendor<span class="caret"></span>

                              </a>



                              <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                  <a class="dropdown-item" href="{{ route('vendor.index') }}">Vendor Info</a>
                                  <a class="dropdown-item" href="{{ route('vendor.product.index') }}">Product Info</a>
                              </div>

                          </li>
                        @endcan



                    <!--<li class="nav-item dropdown">

                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                Images<span class="caret"></span>

                            </a>



                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                <a class="dropdown-item" href="{{ route('image.grid') }}">Image Grid</a>

                                {{-- <a class="dropdown-item" href="{{ route('purchase.grid') }}">Purchase Grid</a> --}}

                            </div>

                        </li>-->



                        {{-- @can('product-list')

                             <li class="nav-item dropdown">

                                 <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                     Product<span class="caret"></span>

                                 </a>



                                 <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                                     <a class="dropdown-item" href="{{ route('products.index') }}">List Products</a>

                                     @can('product-create')

                                         <a class="dropdown-item" href="{{ route('products.create') }}">Add New</a>

                                     @endcan

                                 </div>

                             </li>

                         @endcan--}}



                        @can('user-list')

                            <li class="nav-item dropdown">

                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                    Users<span class="caret"></span>

                                </a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu">
                                        <a class="test" tabindex="-1" href="#">User Management <span class="caret"></span></a>
                                        <ul class="dropdown-menu multi-level">
                                            <li><a class="dropdown-item" href="{{ route('users.index') }}">List Users</a></li>
                                            @can('user-create')
                                              {{-- <li class="nav-item dropdown dropdown-submenu"> --}}
                                                <li><a class="dropdown-item" href="{{ route('users.create') }}">Add New</a></li>
                                              {{-- </li> --}}
                                            @endcan
                                            <li><a class="dropdown-item" href="{{ route('users.login.index') }}">User Logins</a></li>
                                            @can('role-list')
                                              <li class="nav-item dropdown dropdown-submenu">
                                                <a id="navbarDropdown" class="" href="#" role="button" data-toggle="dropdown"
        
                                                    aria-haspopup="true" aria-expanded="false" v-pre>
        
                                                    Roles<span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
        
                                                    <a class="dropdown-item" href="{{ route('roles.index') }}">List Roles</a>
        
                                                    @can('role-create')
        
                                                        <a class="dropdown-item" href="{{ route('roles.create') }}">Add New</a>
        
                                                    @endcan
        
                                                </ul>
                                              </li>
                                            @endcan
                                        </ul>
                                    </li>
                                    @can('view-activity')

                                    <li class="dropdown-submenu">
                                        <a class="test" tabindex="-1" href="#">Activity <span class="caret"></span></a>

                                        <ul class="dropdown-menu multi-level">

                                            <li><a class="dropdown-item" href="{{ route('activity') }}">View</a></li>

                                            <li><a class="dropdown-item" href="{{ route('graph') }}">View Graph</a></li>

                                            <li><a class="dropdown-item" href="{{ route('graph_user') }}">User Graph</a></li>

                                            <li><a class="dropdown-item" href="{{ route('benchmark.create') }}">Add benchmark</a></li>
                                            <li><a class="dropdown-item" href="{{ action('ProductController@showListigByUsers') }}">User-Product Assigmnent</a></li>

                                        </ul>

                                    </li>
                                    @endcan
                                </ul>
                            </li>

                        @endcan

                        @can('social-create')
                        <li class="nav-item dropdown">
                            <a id="instagramMenu" class="nav-link dropdown-toggle" href="#" role="button"
                             data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                              Platforms <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu">
                                @if(Auth::check() && Auth::user()->email == 'facebooktest@test.com')
                                    <li class="dropdown-submenu">
                                        <a href="{{ action('PreAccountController@index') }}" tabindex="-1">E-Mail Accounts<span class="caret"></span></a>
                                    </li>
                                @else    
                                    @can('social-email')
                                    <li >
                                        <a href="{{ action('PreAccountController@index') }}" class="dropdown-item">E-Mail Accounts</a>
                                    </li>
                                    @endcan
                                    @can('instagram')
                                    <li class="dropdown-submenu">
                                        <a id="instagramMenu" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">
                                            INSTAGRAM<span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu multi-level">                                          
                                            @can('admin')
                                                <li><a class="dropdown-item" href="{{ action('InstagramController@index') }}">Dashboard</a></li>
                                                <li><a href="{{ action('InstagramPostsController@index') }}">Manual Instagram Post</a></li>
                                                <li><a class="dropdown-item" href="{{ action('InstagramController@accounts') }}">Accounts</a></li>
                                                {{-- <li><a class="dropdown-item" href="{{ action('HashtagController@index') }}">Targeted Hashtags</a></li>--}}
                                                <li><a class="dropdown-item" href="{{ action('HashtagController@showGrid', 'sololuxury') }}">Hashtag Monitoring &<br> Manual Commenting</a></li>
                                                <li><a class="dropdown-item" href="{{ action('HashtagController@showNotification') }}">Recent Comments <br>(Notifications)</a></li>
                                                <li><a class="dropdown-item" href="{{ action('InstagramController@showPosts') }}">All Posts</a></li>
                                                <li><a class="dropdown-item" href="{{ action('TargetLocationController@index') }}">Target Locations</a></li>
                                                <li><a class="dropdown-item" href="{{ action('KeywordsController@index') }}">Keywords for Comments</a></li>
                                                <li><a class="dropdown-item" href="{{ action('HashtagController@showProcessedComments') }}">Processed Comments</a></li>
                                                <li><a class="dropdown-item" href="{{ action('CompetitorPageController@index') }}?via=instagram">Competitors On Instaram</a></li>
                                                <li><a class="dropdown-item" href="{{ action('InstagramAutoCommentsController@index') }}">Quick Reply</a></li>
                                                <li><a class="dropdown-item" href="{{ action('AutoCommentHistoryController@index') }}">Auto Comment Statistics</a></li>
                                                <li><a class="dropdown-item" href="{{ action('InstagramProfileController@index') }}">Customer's Followers</a></li>
                                                <li><a class="dropdown-item" href="{{ action('InstagramProfileController@edit', 1) }}">#tags used by top customers</a></li>
                                            @endcan
                                            @can('instagram-manual-comment')
                                                <a class="dropdown-item" href="{{ action('UsersAutoCommentHistoriesController@index') }}">Bulk Commenting</a>
                                                <a class="dropdown-item" href="{{ action('InstagramController@accounts') }}">Accounts</a>
                                            @endcan  
                                        </ul>
                                    </li>
                                    @endcan
                                    @can('facebook')
                                        <li class="nav-item dropdown dropdown-submenu">
                                          <a id="facebookMenu" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">
                                              Facebook<span class="caret"></span>
                                          </a>
                                         <ul class="dropdown-menu multi-level" aria-labelledby="facebookMenu">
                                            <li><a class="dropdown-item" href="{{ action('InstagramController@showImagesToBePosted') }}">Create A Post</a></li>
                                            <li><a class="dropdown-item" href="{{ action('InstagramController@showSchedules') }}">Scheduled Posts</a></li>
                                            <li class="dropdown-submenu">
                                                <a href="#" role="button" data-toggle="dropdown" >
                                                    Facebook<span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li><a class="dropdown-item" href="{{ action('FacebookController@index') }}">Facebook Posts</a></li>
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a href="#" role="button" data-toggle="dropdown" >
                                                    Facebook Groups<span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                        <li><a class="dropdown-item" href="{{ action('FacebookController@show', 'group') }}">Facebook Groups </a></li> 
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a href="#" role="button" data-toggle="dropdown" >
                                                        Facebook Brands Fan Page<span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li><a class="dropdown-item" href="{{ action('FacebookController@show', 'brand') }}">Facebook Brands Fan Page</a></li> 
                                                </ul>
                                            </li>
                                            <li class="dropdown-submenu">
                                                <a href="#" role="button" data-toggle="dropdown" >
                                                    Adds<span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li><a class="dropdown-item" href="{{route('social.get-post.page')}}">See Posts</a></li> 
                                                    <li><a class="dropdown-item" href="{{route('social.post.page')}}">Post on Page</a></li> 
                                                    <li><a class="dropdown-item" href="{{route('social.report')}}">Ad Reports</a></li> 
                                                    <li><a class="dropdown-item" href="{{route('social.adCreative.report')}}">Ad Creative Reports</a></li> 
                                                    <li><a class="dropdown-item" href="{{route('social.ad.campaign.create')}}">Create New Campaign</a></li>
                                                    <li><a class="dropdown-item" href="{{route('social.ad.adset.create')}}">Create New Adset</a></li>
                                                    <li><a class="dropdown-item" href="{{route('social.ad.create')}}">Create New Ad</a></li>
                                                    <li><a class="dropdown-item" href="{{route('social.ads.schedules')}}">Ad Schedules</a></li>
                                                </ul>
                                            </li>
                                          </ul>
                                        </li>
                                    @endcan
                                    @can('sitejabber')
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="compAnaMenu" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">
                                                Sitejabber<span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu multi-level" aria-labelledby="compAnaMenu">
                                                <li><a class="dropdown-item" href="{{ action('SitejabberQAController@accounts') }}">Account</a></li>
                                                <li><a class="dropdown-item" href="{{ action('QuickReplyController@index') }}">Quick Reply</a></li>
                                            </ul>
                                        </li>
                                    @endcan
                                    @can('pinterest')
                                        <li class="nav-item dropdown dropdown-submenu">
                                            <a id="pinterestMenu" class="" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">
                                              Pinterest<span class="caret"></span>
                                            </a>
                                            <ul class="dropdown-menu multi-level" aria-labelledby="pinterestMenu">
                                                <li>
                                                  <a class="dropdown-item" href="{{ action('PinterestAccountAcontroller@index') }}">Accounts</a>
                                                </li>
                                            </ul>
                                        </li>
                                    @endcan
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre="">
                                            Images<span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu multi-level">
                                            @can('social-create')
                                            <li>
                                                <a class="dropdown-item" href="{{ route('image.grid') }}">Image Grid</a>
                                            </li>
                                            @endcan
                                            <li><a class="dropdown-item" href="{{ route('image.grid.approved') }}">Final Images</a></li>
                                            <li><a class="dropdown-item" href="{{ route('image.grid.final.approval') }}">Final Approval</a></li>
                                        </ul>
                                    </li>
                                    @can('review-view')
                                    <li >
                                        <a class="dropdown-item" href="{{ route('review.index') }}">Reviews</a>
                                    </li>
                                    @endcan
                                    <li class="nav-item dropdown dropdown-submenu">
                                        <a class="dropdown-item" href="#">Bloggers</a>
                                        <ul class="dropdown-menu multi-level">
                                            <li>
                                                <a class="dropdown-item" href="">Blogger</a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="">Email</a>
                                            </li>
                                        </ul>    
                                    </li>
                                    @can('seo')
                                    <li>
                                        <a class="dropdown-item" href="{{ action('SEOAnalyticsController@show') }}">SEO</a> 
                                    </li>
                                    @endcan
                                @endif
                            </ul>
                        </li>
                        @endcan
                      {{-- @can ('crm')
                            <li class="nav-item dropdown">
                                <div class="dropdown-menu dropdown-menu-left" aria-labelledby="scrapMenu">
                                    <a class="dropdown-item" href="{{ action('FacebookController@index') }}">Facebook Posts</a>
                                    <a class="dropdown-item" href="{{ action('FacebookController@show', 'group') }}">Facebook Groups </a>
                                    <a class="dropdown-item" href="{{ action('FacebookController@show', 'brand') }}">Facebook Brands Fan </a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@excel_import') }}">Import Excel Document Type 1</a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@showProducts', 'G&B') }}">G&B Product</a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@showProducts', 'Wiseboutique') }}">Wiseboutique Product</a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@showProducts', 'DoubleF') }}">TheDoubleF Product</a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@showProducts', 'Tory') }}">Tory Burch Product</a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@showProducts', 'lidiashopping') }}">Lidia Shopping</a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@showProducts', 'cuccuini') }}">Cuccuini</a>
                                    <a class="dropdown-item" href="{{ action('ScrapController@showProducts', 'Divo') }}">Divo</a>
                                </div>
                             </li>
                          @endcan --}}
                            @if(Auth::user()->email != 'facebooktest@test.com')
                                <li class="nav-item dropdown">

                              <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                 data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                  Development<span class="caret"></span>
                              </a>


                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    @can('developer-tasks')
                                        <a class="dropdown-item" href="{{ route('development.index') }}">Tasks</a>
                                        <a class="dropdown-item" href="{{ route('development.issue.index') }}">Issue List</a>
                                    @endcan
                                    <a class="dropdown-item" href="{{ route('development.issue.create') }}">Submit Issue</a>
                                </div>
                        </li>
                            @endif

                        @can('admin')
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    Admin<span class="caret"></span>
                                </a>
                                <ul class="dropdown-menu">
                                    <li class="dropdown-submenu">
                                        <a class="test" tabindex="-1" href="#">Cash Flow <span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('cashflow.index') }}">Cash Flow</a>
                                            </li>
                                            @can('voucher')
                                            <li><a class="dropdown-item" href="{{ route('voucher.index') }}">Convenience Vouchers</a></li>
                                            @endcan
                                            <li><a class="dropdown-item" href="{{ route('cashflow.mastercashflow') }}">Master Cash Flow</a></li>
                                            <li><a class="dropdown-item" href="{{ route('dailycashflow.index') }}">Daily Cash Flow</a></li>
                                            <li><a class="dropdown-item" href="{{ route('budget.index') }}">Budget</a></li>
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" href="#" >
                                            Legal <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            @can('lawyer-all')
                                                <li><a class="dropdown-item" href="{{route('lawyer.index')}}">Lawyers</a></li>
                                            @endcan
                                            @can('case-all')
                                                <li><a class="dropdown-item" href="{{route('case.index')}}">Cases</a><li>
                                            @endcan
                                        </ul>
                                    </li>
                                    <li class="dropdown-submenu">
                                        <a tabindex="-1" href="#" >
                                            Old Issues <span class="caret"></span>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Old Outgoing</a></li>
                                            <li><a class="dropdown-item" href="#">Old Incoming</a><li>
                                        </ul>
                                    </li>
                                </ul>
                            </li>
                        @endcan

                        <li class="nav-item dropdown">

                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"

                               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>

                                {{ Auth::user()->name }} <span class="caret"></span>

                            </a>



                            <ul class="dropdown-menu">

                                @can('setting-list')

                                    <li><a class="dropdown-item" href="{{route('settings.index')}}">Settings</a></li>

                                @endcan

                                @if (Auth::id() == 3 || Auth::id() == 6 || Auth::id() == 56 || Auth::id() == 65 || Auth::id() == 90)
                                  <li><a class="dropdown-item" href="{{route('password.index')}}">Passwords Manager</a></li>
                                  <li><a class="dropdown-item" href="{{route('document.index')}}">Documents Manager</a></li>
                                @endif

                                @can('admin')
                                    <li><a class="dropdown-item" href="{{ route('resourceimg.index') }}" >Resource Center</a></li>
                                @endcan
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item" href="#" >Product</a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        @can('product-delete')
                                            <li><a class="dropdown-item" href="{{route('products.index')}}">Product</a></li>
                                        @endcan
                                        @can('category-edit')
                                        <li><a class="dropdown-item" href="{{route('category')}}">Category</a></li>
                                        <li><a class="dropdown-item" href="{{action('CategoryController@mapCategory')}}">Category References</a></li>
                                        @endcan
                                        @can('brand-edit')
                                        <li><a class="dropdown-item" href="{{route('brand.index')}}">Brands</a></li>
                                        @endcan
                                        @can('category-edit')
                                        <li><a class="dropdown-item" href="{{route('color-reference.index')}}">Color Reference</a></li>
                                        @endcan
                                    </ul>
                                </li>
                                <li class="dropdown-submenu">
                                    <a class="dropdown-item" href="#" >Customer</a>
                                    <ul class="dropdown-menu dropdown-menu-right">
                                        @can('admin')
                                            <li><a class="dropdown-item" href="{{route('task_category.index')}}">Task Category</a></li>
                                        @endcan
                                        @can('reply-edit')
                                            <li><a class="dropdown-item" href="{{route('reply.index')}}">Quick Replies</a></li>
                                        @endcan
                                        @can ('crm')
                                          <li><a class="dropdown-item" href="{{route('autoreply.index')}}">Auto Replies</a></li>
                                        @endcan
                                    </ul>
                                </li>
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                    document.getElementById('logout-form').submit();">
                                    {{ __('Logout') }}
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"

                                      style="display: none;">

                                    @csrf

                                </form>
                            </ul>
                        </li>
                        @endguest

                </ul>

            </div>

        </div>

    </nav>

    @if (Auth::check())

        @can('admin')
            <div class="float-container developer-float">
                @php
                    $lukas_pending_devtasks_count = \App\DeveloperTask::where('user_id', 3)->where('status', '!=', 'Done')->count();
                    $lukas_completed_devtasks_count = \App\DeveloperTask::where('user_id', 3)->where('status', 'Done')->count();
                    $rishab_pending_devtasks_count = \App\DeveloperTask::where('user_id', 65)->where('status', '!=', 'Done')->count();
                    $rishab_completed_devtasks_count = \App\DeveloperTask::where('user_id', 65)->where('status', 'Done')->count();
                @endphp

                <a href="{{ route('development.index') }}">
                    <span class="badge badge-task-pending">L-{{ $lukas_pending_devtasks_count }}</span>
                </a>

                <a href="{{ route('development.index') }}">
                    <span class="badge badge-task-completed">L-{{ $lukas_completed_devtasks_count }}</span>
                </a>

                <a href="{{ route('development.index') }}">
                    <span class="badge badge-task-other">R-{{ $rishab_pending_devtasks_count }}</span>
                </a>

                <a href="{{ route('development.index') }}">
                    <span class="badge badge-task-other right completed">R-{{ $rishab_completed_devtasks_count }}</span>
                </a>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#quickDevelopmentModal">+ DEVELOPMENT</button>
            </div>

            <div class="float-container instruction-float">
                @php
                    $pending_instructions_count = \App\Instruction::where('assigned_to', Auth::id())->whereNull('completed_at')->count();
                    $completed_instructions_count = \App\Instruction::where('assigned_to', Auth::id())->whereNotNull('completed_at')->count();
                    $sushil_pending_instructions_count = \App\Instruction::where('assigned_from', Auth::id())->where('assigned_to', 7)->whereNull('completed_at')->count();
                    $andy_pending_instructions_count = \App\Instruction::where('assigned_from', Auth::id())->where('assigned_to', 56)->whereNull('completed_at')->count();
                @endphp

                <a href="{{ route('instruction.index') }}">
                    <span class="badge badge-task-pending">{{ $pending_instructions_count }}</span>
                </a>

                <a href="{{ route('instruction.index') }}#verify-instructions">
                    <span class="badge badge-task-completed">{{ $completed_instructions_count }}</span>
                </a>

                <a href="{{ route('instruction.list') }}">
                    <span class="badge badge-task-other">S-{{ $sushil_pending_instructions_count }}</span>
                </a>

                <a href="{{ route('instruction.list') }}">
                    <span class="badge badge-task-other right">A-{{ $andy_pending_instructions_count }}</span>
                </a>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#quickInstructionModal">+ INSTRUCTION</button>
            </div>

            <div class="float-container">
                @php
                    $pending_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to', Auth::id())->whereNull('is_completed')->count();
                    $completed_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to', Auth::id())->whereNotNull('is_completed')->count();
                    $sushil_pending_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to', 7)->whereNull('is_completed')->count();
                    $andy_pending_tasks_count = \App\Task::where('is_statutory', 0)->where('assign_to', 56)->whereNull('is_completed')->count();
                @endphp

                <a href="/#1">
                    <span class="badge badge-task-pending">{{ $pending_tasks_count }}</span>
                </a>

                <a href="/#3">
                    <span class="badge badge-task-completed">{{ $completed_tasks_count }}</span>
                </a>

                <a href="{{ route('task.list') }}">
                    <span class="badge badge-task-other">S-{{ $sushil_pending_tasks_count }}</span>
                </a>

                <a href="{{ route('task.list') }}">
                    <span class="badge badge-task-other right">A-{{ $andy_pending_tasks_count }}</span>
                </a>
                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#quickTaskModal">+ TASK</button>
            </div>
        @endcan

      @include('partials.modals.quick-task')
      @include('partials.modals.quick-instruction')
      @include('partials.modals.quick-development-task')
    @endif

    <main class="container">

        <!-- Showing fb like page div to all pages  -->

    {{-- @if(Auth::check())

     <div class="fb-page" data-href="https://www.facebook.com/devsofts/" data-small-header="true" data-adapt-container-width="false" data-hide-cover="true" data-show-facepile="false"><blockquote cite="https://www.facebook.com/devsofts/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/devsofts/">Development</a></blockquote></div>

     @endif --}}



    <!-- End of fb page like  -->



        @yield('content')

    </main>

    <div class="col-md-12">
        @yield('large_content')
    </div>

</div>

<!-- Scripts -->

{{-- @include('partials.chat') --}}



<!-- Like page plugin script  -->



{{-- <script>(function(d, s, id) {

  var js, fjs = d.getElementsByTagName(s)[0];

  if (d.getElementById(id)) return;

  js = d.createElement(s); js.id = id;

  js.src = 'https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v3.2&appId=2045896142387545&autoLogAppEvents=1';

  fjs.parentNode.insertBefore(js, fjs);

}(document, 'script', 'facebook-jssdk'));</script> --}}

@yield('scripts')

  <script>
      window.token = "{{ csrf_token() }}";

      var url = window.location;
      window.collectedData = [
          {
              type: 'key',
              data: ''
          },
          {
              type: 'mouse',
              data: []
          }
      ];

      $(document).keypress(function(event) {
          var x = event.charCode || event.keyCode;  // Get the Unicode value
          var y = String.fromCharCode(x);
          collectedData[0].data += y;
      });
      

      // $(document).click(function() {
      //     if (collectedData[0].data.length > 10) {
      //         let data_ = collectedData[0].data;
      //         let type_ = collectedData[0].type;
      //
      //         $.ajax({
      //             url: "/track",
      //             type: 'post',
      //             csrf: token,
      //             data: {
      //                 url: url,
      //                 item: type_,
      //                 data: data_
      //             }
      //         });
      //     }
      // });
  </script>
{{--  <script src="{{ asset('js/tracker.js') }}"></script>--}}
</body>

</html>
