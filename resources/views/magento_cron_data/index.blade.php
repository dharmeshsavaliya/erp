@extends('layouts.app')

@section('title','Product pricing')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
<link href="https://unpkg.com/gijgo@1.9.13/css/gijgo.min.css" rel="stylesheet" type="text/css" />
<style>.hidden {
    display:none;
}
.btn-secondary, .btn-secondary:hover, .btn-secondary:focus{
        background: #fff;
        color: #757575;
        border: 1px solid #ddd;
        outline: none;
        box-shadow: none;
    }
  .shortTable{
    cursor: pointer;
  }
</style>
<div class = "row m-0">
    <div class="col-lg-12 margin-tb p-0">
        <h2 class="page-heading">Product pricing</h2>
    </div>
</div>
<div class = "row m-0">
    <div class="pl-3 pr-3 margin-tb">
        <div class="pull-left cls_filter_box">

            <form class="form-inline filter_form" action="" method="GET">
                <div class="form-group mr-3">
                    <select class="form-control globalSelect2" data-placeholder="Select Websites" name="website" id="magentowebsite">
                         <option value="">Select Websites</option>
                    @php
                    $selectcate ='';
                    if(isset($_GET['website'])){
                      $selectcate =$_GET['website'];
                    }
                    @endphp
                        @if ($website)
                            @foreach($website as $id => $web)
                                <option value="{{ $id }}" @if($selectcate == $id) selected @endif  >{{ $web }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group mr-3">
                    <select class="form-control globalSelect2" data-placeholder="Select Status" name="status" id="magentowebsitestatus">
                        <option value="">Select Status</option>
                    @php
                    $selectcate ='';
                    if(isset($_GET['status'])){
                      $selectcate =$_GET['status'];
                    }
                    @endphp
                        @if ($status)
                            @foreach($status as $sat)
                                <option value="{{ $sat }}" @if($selectcate == $sat) selected @endif  >{{ $sat }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group mr-3">
                    <input type="date" name="create_at" class="form-control" >
                </div>

                <div class="form-group mr-3">
                   <button type="submit" class="btn btn-secondary">Search</button>
                </div>
            </form> 
            
        </div>
    </div>
</div>

<div class="row m-0">
    <div class="col-lg-12"> 
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="table-responsive">
                   <table class="table table-bordered table-striped" id="product-price" style="table-layout: fixed">
                       <thead>
                       <tr>
                           <th style="width: 7%">website
                            <!-- <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="category" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer"data-input="category" data-order="desc" aria-hidden="true"></i> -->
                           </th>
                           <th style="width: 7%">Cron ID 
                            <!-- <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="website" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="website" data-order="desc" aria-hidden="true"></i> -->
                           </th>
                           <th style="width: 7%">Job Code
                            <!--  <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="bsegment" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="bsegment" data-order="desc" aria-hidden="true"></i> -->
                           </th>
                           <th style="width: 4%;word-break: break-all">Cron Message</th>
                           <th style="width: 5%">Cron Status
                            <!-- <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="csegment" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="csegment" data-order="desc" aria-hidden="true"></i> -->
                           </th>
                           <th style="width: 5%">Created at</th>
                        
                           <th style="width: 5%">Scheduled at</th>
                           <th style="width: 5%">Executed at</th>
                           <th style="width: 5%">Finished at</th>
                       </tr>
                       </thead>
                       <tbody>
                       @php $i=1; @endphp
                       @foreach ($data as $dat) 
                           <tr  data-id="{{$i}}" class="tr_{{$i++}}">
                               <td class="expand-row" style="word-break: break-all">
                                   <span class="td-mini-container">
                                      {{ strlen( $dat['website']) > 9 ? substr( $dat['website'], 0, 8).'...' :  $dat['website'] }}
                                   </span>

                                   <span class="td-full-container hidden">
                                       {{ $dat['website'] }}
                                   </span>
                                </td>

                                <td class="expand-row" style="word-break: break-all">
                                   <span class="td-mini-container">
                                      {{ strlen( $dat['cron_id']) > 9 ? substr( $dat['cron_id'], 0, 8).'...' :  $dat['cron_id'] }}
                                   </span>

                                   <span class="td-full-container hidden">
                                       {{ $dat['cron_id'] }}
                                   </span>
                                </td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['job_code']) > 15 ? substr( $dat['job_code'], 0, 15).'...' :  $dat['job_code'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['job_code'] }}
                                            </span>
                               </td>
                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['cron_message']) > 15 ? substr( $dat['cron_message'], 0, 15).'...' :  $dat['cron_message'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['cron_message'] }}
                                            </span>
                               </td>
                            
                               <td>{{ $dat['cronstatus'] }}</td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['cron_created_at']) > 15 ? substr( $dat['cron_created_at'], 0, 15).'...' :  $dat['cron_created_at'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['cron_created_at'] }}
                                            </span>
                               </td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['cron_scheduled_at']) > 15 ? substr( $dat['cron_scheduled_at'], 0, 15).'...' :  $dat['cron_scheduled_at'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['cron_scheduled_at'] }}
                                            </span>
                               </td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['cron_executed_at']) > 15 ? substr( $dat['cron_executed_at'], 0, 15).'...' :  $dat['cron_executed_at'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['cron_executed_at'] }}
                                            </span>
                               </td>

                               <td class="expand-row" style="word-break: break-all">
                                    <span class="td-mini-container">
                                                {{ strlen( $dat['cron_finished_at']) > 15 ? substr( $dat['cron_finished_at'], 0, 15).'...' :  $dat['cron_finished_at'] }}
                                     </span>

                                   <span class="td-full-container hidden">
                                               {{ $dat['cron_finished_at'] }}
                                            </span>
                               </td>

                           </tr> 
                       @endforeach
                       </tbody>
                   </table>
                   <img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />
              </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script src="https://unpkg.com/gijgo@1.9.13/js/gijgo.min.js" type="text/javascript"></script>
<script>

    $(document).ready(function () {
        
        

      $(".filter_form").submit(function (event) {

        event.preventDefault();
        let data = $('.filter_form').serialize();

        page = page + 1;
        $.ajax({
        url: "{{url('/show-magento-cron-data')}}?page="+ page + '&count=' + {{$i}} + '&' + data,
        type: 'GET',
        data: $('.filter_form').serialize(),
        success: function (data) {
            console.log(data);
            // $loader.hide();
             $('tbody').html($.trim(data['html']));
            // isLoading = false;
        },
        error: function () {
            // $loader.hide();
            // isLoading = false;
        }
    });

      });
    });


    var isLoading = false;
    var page = 1;
    $(document).ready(function () {
        
        $(window).scroll(function() {
            if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
              loadMore();
            }
        });

        let data = $('.filter_form').serialize();

        function loadMore() {
            if (isLoading)
                return;
            isLoading = true;
            var $loader = $('.infinite-scroll-products-loader');
            page = page + 1;

            var url  = new URL(window.location.href);
            var search_params = url.searchParams;
          // add "topic" parameter
            search_params.set('page', page);
            search_params.set('count',{{$i}});
            url.search = search_params.toString();
            var new_url = url.toString();

            $.ajax({
                url: new_url + '&' + data,
                type: 'GET',
                data: $('.filter_form').serialize(),
                beforeSend: function() {
                    $loader.show();
                },
                success: function (data) {
                    $loader.hide();
                    $('tbody').append($.trim(data['html']));
                    isLoading = false;
                },
                error: function () {
                    $loader.hide();
                    isLoading = false;
                }
            });
        }  

    $(document).on('click', '.shortTable',function(){
      var $loader = $('#loading-image-preview');
      $loader.show();
      var order = $(this).data('order');
      var input = $(this).data('input');

      var url  = new URL(window.location.href);
      var search_params = url.searchParams;

      search_params.set('order', order);
      search_params.set('input', input);

      url.search = search_params.toString();
      var new_url = url.toString();

      window.history.pushState("", "Title", new_url);
      $.ajax({
        url: new_url,
        type: 'GET',
        data: {},
        // beforeSend: function() {
        //   $loader.show();
        // },
        success: function (data) {
            $loader.hide();
            $('tbody').html($.trim(data['html']));
            isLoading = false;
        },
        error: function () {
            $loader.hide();
            isLoading = false;
        } 
      });

    });


    });

$(document).on('click', '.expand-row', function () {
  var selection = window.getSelection();
  if (selection.toString().length === 0) {
    $(this).find('.td-mini-container').toggleClass('hidden');
    $(this).find('.td-full-container').toggleClass('hidden');
  }
});
</script>

@endsection