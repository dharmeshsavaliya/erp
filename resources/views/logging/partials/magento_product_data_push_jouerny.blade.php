@extends('layouts.app')

@section('title', 'Product Push Journey')

@section('content')
  <div id="myDiv">
    <img id="loading-image" src="/images/pre-loader.gif" style="display:none;" />
  </div>
    
    <div class="row m-0">
      <div class="col-lg-12 margin-tb p-0">
        <h2 class="page-heading">Product Push Journey ({{ $total_count }})</h2>
      </div>
      <div class="" style="overflow: scroll;">
        <table id="magento_list_tbl_895" class="table table-bordered table-hover" >
          <thead>
            <th >ID</th>
            <th >SKU</th>
            <th >Brand</th>
            <th >Category</th>
            <th >Price</th>
            <td>entered_in_product_push</td>
            @foreach($conditions as $condition)
                <td>{{$condition->condition}}</td>
            @endforeach

          </thead>
          <tbody class="infinite-scroll-pending-inner">

          @foreach($logListMagentos as $item)
			    <tr>
                  <td>
                    <a class="show-product-information text-dark" data-id="{{ $item->product_id }}" href="/products/{{ $item->product_id }}" target="__blank">{{ $item->product_id }}</a>
                  </td>
                  <td class="expand-row-msg" data-name="sku" data-id="{{$item->id}}">
                    <span class="show-short-sku-{{$item->id}}">{{ str_limit($item->sku, 3 ,'..')}}</span>
                    <span style="word-break:break-all;" class="show-full-sku-{{$item->id}} hidden"><a class="text-dark" href="{{ $item->website_url }}/default/catalogsearch/result/?q={{ $item->sku }}" target="__blank">{{$item->sku}}</a></span>
                  </td>
                  <td class="expand-row-msg" data-name="brand_name" data-id="{{$item->id}}">
                    <span class="show-short-brand_name-{{$item->id}}">{{ str_limit($item->brand_name, 3, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-brand_name-{{$item->id}} hidden">{{$item->brand_name}}</span>
                  </td>
                  <td class="expand-row-msg" data-name="category_title" data-id="{{$item->id}}">
                    <span class="show-short-category_title-{{$item->id}}">{{ str_limit($item->category_home, 6, '..')}}</span>
                    <span style="word-break:break-all;" class="show-full-category_title-{{$item->id}} hidden">{{$item->category_home}}</span>
                  </td>
                  <td> {{$item->price}} </td>
                  <?php $pushJourney = \App\ProductPushJourney::where('log_list_magento_id', $item->log_list_magento_id)->pluck( 'condition')->toArray(); 
                        $category = \App\Category::find($item->category);
                        if($category->parent_id !=0) {
                            $useStatus = 'status';
                        } else {
                            $useStatus = "upteam_status";
                        }
                  ?>
                  <td> @if(in_array('entered_in_product_push', $pushJourney)) <i class="fa fa-check-circle-o text-success fa-lg" aria-hidden="true"></i> @else <i class="fa fa-times-circle text-danger fa-lg" aria-hidden="true"></i> @endif</td>
                  @foreach($conditions as $condition)
                      <td>
                          @if($condition->$useStatus == '1')
                              <i class="fa fa-check-circle-o text-success fa-lg" aria-hidden="true"></i>
                          @else
                              <i class="fa fa-times-circle text-danger fa-lg" aria-hidden="true"></i>
                          @endif
                      </td>
                  @endforeach()
                </tr>
              @endforeach()
            </tbody>
          </table>


        </div>
        
     </div>
     @endsection

     @section('scripts')
     <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
     <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
     <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
         <script>
              $(document).on('click', '.expand-row-msg', function () {
                var name = $(this).data('name');
                var id = $(this).data('id');
                var full = '.expand-row-msg .show-short-'+name+'-'+id;
                var mini ='.expand-row-msg .show-full-'+name+'-'+id;
                $(full).toggleClass('hidden');
                $(mini).toggleClass('hidden');
              });

              /** infinite loader **/
	var isLoading = false;
	var page = 1;
	$(document).ready(function () {
		$(window).scroll(function() {
			if ( ( $(window).scrollTop() + $(window).outerHeight() ) >= ( $(document).height() - 2500 ) ) {
				loadMore();
			}
		});

		function loadMore() {
			if (isLoading)
				return;
			isLoading = true;
			var $loader = $('.infinite-scroll-products-loader');
			page = page + 1;
			$.ajax({
				url: "/logging/log-magento-product-push-journey?type=product_log_list&page="+page,
				type: 'GET',
				data: $('.handle-search').serialize(),
				beforeSend: function() {
					$loader.show();
				},
				success: function (data) {
					//console.log(data);
					$loader.hide();				
					$('.infinite-scroll-pending-inner').append(data.tbody);
					isLoading = false;
					if(data.tbody == "") {
						isLoading = true;
					}
				},
				error: function () {
					$loader.hide();
					isLoading = false;
				}
			});
		}
	});
	//End load more functionality
        </script>

    @endsection