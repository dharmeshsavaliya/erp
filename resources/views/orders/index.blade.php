@extends('layouts.app')

@section('title', 'Orders List')

@section('styles')
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
@endsection

@section('content')

    <div class="row">
        <div class="col-12">
            <h2 class="page-heading">Orders List</h2>
          </div>

          <div class="col-12 mb-3">
            <div class="pull-left">

                <form class="form-inline" action="{{ route('order.index') }}" method="GET">
                  <div class="form-group">
                    <input name="term" type="text" class="form-control"
                           value="{{ isset($term) ? $term : '' }}"
                           placeholder="Search">
                  </div>

                  <div class="form-group ml-3">
                    <select class="form-control select-multiple" name="status[]" multiple>
                      <option value="">Select a Status</option>

                      @foreach ($order_status_list as $order_st)
                        <option value="{{ $order_st }}" {{ isset($order_status) && in_array($order_st, $order_status) ? 'selected' : '' }}>{{ $order_st }}</option>
                      @endforeach
                    </select>
                  </div>

                  <div class="form-group ml-3">
                    <?php echo Form::select("brand_id[]",["" => "-- Select Brands --"]+$brandList,request('brand_id',[]),["class" => "form-control select2"]); ?>
                  </div>

                  <div class="form-group ml-3">
                    <div class='input-group date' id='order-datetime'>
                      <input type='text' class="form-control" name="date" value="{{ isset($date) ? $date : '' }}" />

                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                    </div>
                  </div>

                  <button type="submit" class="btn btn-image ml-3"><img src="/images/filter.png" /></button>
                </form>
            </div>
            <div class="pull-right">
                <a class="btn btn-secondary" href="{{ route('order.create') }}">+</a>
                <a href="{{ action('OrderController@downloadOrderInPdf', Request::all()) }}" class="btn btn-success btn-xs">Download</a>
            </div>
        </div>
    </div>

    @include('partials.flash_messages')
    <?php if(!empty($statusFilterList)) { ?>
      <div class="row col-md-12">
          <?php foreach($statusFilterList as $listFilter) { ?>
            <div class="card">
                <div class="card-header">
                  <?php echo $listFilter["order_status"]; ?>
                </div>
                <div class="card-body">
                    <?php echo $listFilter["total"]; ?>
                </div>
            </div>
        <?php } ?>
      </div>
    <?php } ?>  
    </br> 
    <div class="table-responsive">
      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="10%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">ID</a></th>
            <th width="10%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=date{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Date</a></th>
            <th width="15%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=client_name{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Client</a></th>
            <th width="10%">Products</th>
            <th>Brands</th>
            <th width="15%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Order Status</a></th>
            <th width="10%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=advance{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Advance</a></th>
            <th width="10%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}{{ isset($order_status) ? implode('&', array_map(function($item) {return 'status[]='. $item;}, $order_status)) . '&' : '&' }}sortby=balance{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}">Balance</a></th>
            {{-- <th style="width: 5%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=action{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Action Status</a></th>
            <th style="width: 8%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=due{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Due</a></th> --}}
            {{-- <th style="width: 8%">Message Status</th> --}}
            {{-- <th style="width: 20%"><a href="/order{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=communication{{ ($orderby == 'asc') ? '&orderby=desc' : '' }}">Communication</a></th> --}}
            <th width="10%">Action</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($orders_array as $key => $order)
            <tr class="{{ \App\Helpers::statusClass($order->assign_status ) }}">
              <td class="table-hover-cell">
                <div class="form-inline">
                  @if ($order->is_priority == 1)
                    <strong class="text-danger mr-1">!!!</strong>
                  @endif
                  <span class="td-mini-container">
                    {{ $order->order_id }}
                  </span>
                </div>
              </td>
              <td>{{ Carbon\Carbon::parse($order->order_date)->format('d-m') }}</td>
              <td class="expand-row table-hover-cell">
                @if ($order->customer)
                  <span class="td-mini-container">
                    <a href="{{ route('customer.show', $order->customer->id) }}">{{ strlen($order->customer->name) > 15 ? substr($order->customer->name, 0, 13) . '...' : $order->customer->name }}</a>
                  </span>

                  <span class="td-full-container hidden">
                    <a href="{{ route('customer.show', $order->customer->id) }}">{{ $order->customer->name }}</a>
                  </span>
                @endif
              </td>
              <td class="expand-row table-hover-cell">
                @php $count = 0; @endphp
                <div class="d-flex">
                  <div class="">
                    @foreach ($order->order_product as $order_product)
                      @if ($order_product->product)
                        @if ($order_product->product->hasMedia(config('constants.media_tags')))
                          <span class="td-mini-container">
                            @if ($count == 0)
                              <a href="{{ route('products.show', $order_product->product->id) }}" target="_blank"><img src="{{ $order_product->product->getMedia(config('constants.media_tags'))->first()->getUrl() }}" class="img-responsive thumbnail-200 mb-1"></a>

                              @php ++$count; @endphp
                            @endif
                          </span>

                          <span class="td-full-container hidden">
                            @if ($count >= 1)
                              <a href="{{ route('products.show', $order_product->product->id) }}" target="_blank"><img src="{{ $order_product->product->getMedia(config('constants.media_tags'))->first()->getUrl() }}" class="img-responsive thumbnail-200 mb-1"></a>

                              @php $count++; @endphp
                            @endif
                          </span>
                        @endif
                      @endif
                    @endforeach
                  </div>

                  @if (($count - 1) > 1)
                    <span class="ml-1">
                      ({{ ($count - 1) }})
                    </span>
                  @endif
                </div>

              </td>
              <td>
                <?php 
                    $totalBrands = explode(",",$order->brand_name_list);
                    echo (count($totalBrands) > 1) ? "Multi" : $order->brand_name_list; 
                ?>
              </td>
              <td class="expand-row table-hover-cell">
                <div class="form-group">
                  <?php echo Form::select("order_status",["" => "N/A"] + $order_status_list, $order->order_status,["class"=> "form-control order-status-select select2","data-id"=> $order->id]); ?>
                </div>
              </td>
              <td>{{ $order->advance_detail }}</td>
              <td>{{ $order->balance_amount }}</td>
              {{-- <td></td>
              <td></td> --}}
              {{-- <td>{{ $order->action->status }}</td>
              <td>{{ $order->action->completion_date ? Carbon\Carbon::parse($order->action->completion_date)->format('d-m') : '' }}</td> --}}


              <td>
                <div class="d-flex">
                  <a class="btn btn-image" href="{{route('purchase.grid')}}?order_id={{$order->id}}">
                    <img style="display: inline; width: 15px;" src="{{ asset('images/customer-order.png') }}" alt="">
                  </a>
                  <a class="btn btn-image" href="{{ route('order.show',$order->id) }}"><img src="/images/view.png" /></a>
                  {{-- @can('order-edit')
                  <a class="btn btn-image" href="{{ route('order.edit',$order['id']) }}"><img src="/images/edit.png" /></a>
                  @endcan --}}

                  {!! Form::open(['method' => 'DELETE','route' => ['order.destroy', $order->id],'style'=>'display:inline']) !!}
                  <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
                  {!! Form::close() !!}

                  @if(auth()->user()->checkPermission('order-delete'))
                    {!! Form::open(['method' => 'DELETE','route' => ['order.permanentDelete', $order->id],'style'=>'display:inline']) !!}
                    <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
                    {!! Form::close() !!}
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {!! $orders_array->appends(Request::except('page'))->links() !!}
    {{--{!! $orders->links() !!}--}}


@endsection

@section('scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('#order-datetime').datetimepicker({
        format: 'YYYY-MM-DD'
      });

      $(document).on("change",".order-status-select",function() {
        $.ajax({
          url: "/order/change-status",
          type: "GET",
          data : {
            id : $(this).data("id"),
            status : $(this).val()
          }
        }).done( function(response) {
         
        }).fail(function(errObj) {
          alert("Could not change status");
        });
      });

      $(".select2").select2({tags:true});

      $(".select-multiple").multiselect({
        // buttonWidth: '100%',
        // includeSelectAllOption: true
      });
    });

    $(document).on('click', '.change_message_status', function(e) {
      e.preventDefault();
      var url = $(this).data('url');
      var thiss = $(this);
      var type = 'GET';

      if ($(this).hasClass('approve-whatsapp')) {
        type = 'POST';
      }

        $.ajax({
          url: url,
          type: type,
          beforeSend: function() {
            $(thiss).text('Loading');
          }
        }).done( function(response) {
          $(thiss).closest('tr').removeClass('row-highlight');
          $(thiss).prev('span').text('Approved');
          $(thiss).remove();
        }).fail(function(errObj) {
          alert("Could not change status");
        });
    });

    $(document).on('click', '.expand-row', function() {
      var selection = window.getSelection();
      if (selection.toString().length === 0) {
        $(this).find('.td-mini-container').toggleClass('hidden');
        $(this).find('.td-full-container').toggleClass('hidden');
      }
    });
  </script>
@endsection
