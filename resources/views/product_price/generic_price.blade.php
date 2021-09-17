@extends('layouts.app')

@section('title','Product pricing')

@section('content')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">
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
                    <select class="form-control globalSelect2" data-placeholder="Select Category" name="category_id" id="categoryForGenericPrices">
                    <option value="">Select Websites</option>
                    @php
                    $selectcate ='';
                    if(isset($_GET['id'])){
                      $selectcate =$_GET['id'];
                    }
                    @endphp
                        @if ($categories)
                            @foreach($categories as $category)
                                <option value="{{ $category['id'] }}" @if($selectcate == $category['id']) selected @endif  >{{ $category['title'] }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="form-group mr-3">
                    <a onClick="showgenerice()" class="btn btn-secondary">Show Generic Prices</a>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="loading-image" style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 9999; background: url(&quot;/images/pre-loader.gif&quot;) 50% 50% no-repeat; display: none;">
</div>
<div class="row m-0">
    <div class="col-lg-12"> 
        <div class="panel-group" style="margin-bottom: 5px;">
            <div class="table-responsive">
                   <table class="table table-bordered table-striped" id="product-price" style="table-layout: fixed">
                       <thead>
                       <tr>
                           <th style="width: 7%">Category
                            <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="category" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer"data-input="category" data-order="desc" aria-hidden="true"></i>
                           </th>
                           <th style="width: 7%">website 
                            <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="website" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="website" data-order="desc" aria-hidden="true"></i>
                           </th>
                           <th style="width: 7%">Brand segment
                             <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="bsegment" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="bsegment" data-order="desc" aria-hidden="true"></i>
                           </th>
                           <th style="width: 4%;word-break: break-all">Product</th>
                           <th style="width: 5%">Country segment
                            <i class="fa fa-arrow-up shortTable cursor-pointer" data-input="csegment" data-order="asc" aria-hidden="true"></i>
                            <i class="fa fa-arrow-down shortTable cursor-pointer" data-input="csegment" data-order="desc" aria-hidden="true"></i>
                           </th>
                           <th style="width: 2%">Price</th>
                           @foreach($category_segments as $category_segment)
                              <th width="3%"> Category Segment {{ $category_segment->name }}</th>
                           @endforeach
                           <th style="width: 5%">Add Duty </th>
                           <th style="width: 5%">Add Profit </th>
                           <th style="width: 3%">less_IVA </th>
                           <th style="width: 3%">Cost A</th>
                           <th style="width: 3%">Cost B </th>
                           <th style="width: 3%">Final Price A</th>
                           <th style="width: 3%">Final Price B</th>
                           <th style="width: 3%">Update</th>
                       </tr>
                       </thead>
                       <tbody>
                       @php $i=1; @endphp
                        @include('product_price.generic_price_ajax')
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
<script>
function showgenerice() {
    var catId = $('#categoryForGenericPrices').val();
    if(catId==''){
      alert('Select Category First');
    }else{
        var url = "{{url('/')}}/product-generic-pricing?id="+catId;
         window.location.replace(url);
    }
}

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

    /*function checkFinalPriceBeforeUpdate2($that){ console.log('called');
        var less_iva = $($that).data('less_iva').replace('%', "");;
        var product_price = $($that).data('product_price');
        var final_price =product_price;

        var cate_segment_discount = $($that).data('cate_segment_discount');
        var cate_segment_discount_type = $($that).data('cate_segment_discount_type');
        var default_duty = $($that).data('default_duty');
        var name = $($that).data('name');

        cate_segment_discount = $($that).val();
        var row = $($that).data('row');

        // if(cate_segment_discount !='' && cate_segment_discount != null){
        //     if(cate_segment_discount_type == 'percentage'){
        //         var catDisc = (product_price * cate_segment_discount)/100;
        //         final_price = final_price - catDisc;

        //     }else{
        //         final_price = final_price - cate_segment_discount;
        //     }
        // }

        // if(less_iva!=0){
        //     var lessIva = (final_price * less_iva )/100;
        //     final_price = final_price - lessIva;
        // }
        if(default_duty !=''){
            var dutyDisc = (final_price * default_duty)/100;
            final_price = final_price + dutyDisc;
        } console.log(final_price.toFixed(2));
        $('#final_price_row'+row).text(final_price.toFixed(2));
    }*/

    function checkFinalPriceBeforeUpdate($that){
		var product_price = 100;
        var final_price1 = final_price2 = product_price;
		var tr = $($that).closest('tr'); 
        var less_iva = $(tr).find('td .less_iva').val(); 
        var segment1 = $(tr).find('td .segment1').val();
        var segment1DiscType = $(tr).find('td .segment1').data('cate_segment_discount_type');
		
        var segment2= $(tr).find('td .segment2').val();
		var segment2DiscType = $(tr).find('td .segment1').data('cate_segment_discount_type');
		
        var default_duty = $(tr).find('td .add_duty').val();
        var add_profit = $(tr).find('td .add_profit').val();
		
		var cate_segment_discount1 = segment1;
		var cate_segment_discount_type1 = segment1DiscType;
		var cate_segment_discount2 = segment2;
		var cate_segment_discount_type2 = segment2DiscType;

        var row = $($that).data('row');

        if(cate_segment_discount1 !='' && cate_segment_discount1 != null){
            if(cate_segment_discount_type1 == 'percentage'){
                var catDisc = (final_price1 * cate_segment_discount1)/100;
                final_price1 = Number(final_price1) - Number(catDisc);

            }else{
                final_price1 = Number(final_price1) - Number(cate_segment_discount1);
            }
        }

		if(cate_segment_discount2 !='' && cate_segment_discount2 != null){
            if(cate_segment_discount_type2 == 'percentage'){
                var catDisc = (final_price2 * cate_segment_discount2)/100; 
                final_price2 = Number(final_price2) - Number(catDisc);

            }else{
                final_price2 = Number(final_price2) - Number(cate_segment_discount2);
            }
        }
		
        if(less_iva!=0){
            var lessIva = (final_price1 * less_iva )/100;
            final_price1 = Number(final_price1) - Number(lessIva);
			
			var lessIva = (final_price2 * less_iva )/100;
            final_price2 = Number(final_price2) - Number(lessIva);
        }
		
        if(default_duty !=''){
            var dutyDisc = (final_price1 * default_duty)/100;
            final_price1 = Number(final_price1) + Number(dutyDisc);
			
			var dutyDisc = (final_price2 * default_duty)/100;
            final_price2 = Number(final_price2) + Number(dutyDisc);
        }
		var cost1 = final_price1;
		var cost2 = final_price2; console.log(add_profit);
		if(add_profit !=''){
            var profit = (final_price1 * add_profit)/100;
            final_price1 = Number(final_price1) + Number(profit);
			
			var profit = (final_price2 * add_profit)/100;
            final_price2 = Number(final_price2) + Number(profit);
        }
		$(tr).find('.row_final_price_a').text(final_price1.toFixed(2));
		$(tr).find('.row_final_price_b').text(final_price2.toFixed(2));
		$(tr).find('.row_cost_a').text(cost1.toFixed(2));
		$(tr).find('.row_cost_b').text(cost2.toFixed(2));
    }


function updateDutyPrice(countryId, dutyPrice) {     
     $.ajax({
        url: "{{route('updateDutyPrice')}}",
        type: 'post',
        data: {
            _token: '{{csrf_token()}}',
            countryId: countryId,
            dutyPrice: dutyPrice.value
        },
        beforeSend: function () {
            $("#loading-image").show();
        }
    }).done(function(response) {
        $("#loading-image").hide();
        if(response.status == false){
             toastr["error"]("Something went wrong, Please try again.");
        }else{
            toastr["success"]("Product updated successfully!", "Message");
        }
    });
}

function updateSegmentPrice(segmentId, brandId, price) {
        var data = {
                _token: '{{csrf_token()}}',
                segmentId: segmentId,
                brandId: brandId,
                price: price.value
            };  
         $.ajax({
            //updateSegmentPrice  real route
            url: "{{route('updateSegmentPrice')}}",
            type: 'post',
            data: data,
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            if(response.status == false){
                toastr["error"]("Something went wrong, Please try again.");
            }else{
                toastr["success"]("Product updated successfully!", "Message");
            }
        });
    }

$(document).on('click', '.expand-row', function () {
  var selection = window.getSelection();
  if (selection.toString().length === 0) {
    $(this).find('.td-mini-container').toggleClass('hidden');
    $(this).find('.td-full-container').toggleClass('hidden');
  }
});

$(document).on('keyup', '.add_profit', function () {
        if (event.keyCode != 13) {
            return;
        }
        let add_profit = $(this).val().replace('%', '');
        let ref_name = $(this).data('ref');
        let rows = $('.'+ref_name).closest('tr');
        let product_array = [];
        for(let i=0; i< rows.length; i++){
            product_array[i] = {
                'row_id' : $(rows[i]).attr('data-id'),
                'storewebsitesid' : $(rows[i]).attr('data-storewebsitesid'),
                'product_id' : $(rows[i]).closest('tr').find('.product_id').text(),
                'add_duty' : $(rows[i]).closest('tr').find('.add_duty').val().replace('%', ''),
                'product_id' : $(rows[i]).find('.product_id').text(),
                'add_profit' : $(rows[i]).closest('tr').find('.add_profit').val().replace('%', ''),
                'country_code' : $(rows[i]).attr('data-country_code'), 
            };
        }

        $.ajax({
            url: "{{route('product.pricing.update.add_profit')}}",
            type: 'post',
            data: {
                _token: '{{csrf_token()}}',
                product_array: JSON.stringify(product_array),
                add_profit: add_profit,
                row_id: $(this).closest('tr').attr('data-id'),
            },
            beforeSend: function () {
                $("#loading-image").show();
            }
        }).done(function(response) {
            $("#loading-image").hide();
            if(response.status == false){
                toastr["error"](response.message + " is not exist!", "Message");
            }else{
                response.data.forEach(function(item, index) {
                    if(item.status){
                        let row = $(`.tr_${item.row_id}`); 
                        $(row).find('td:nth-child(12) span').html(item.add_profit);
                        $(row).find('.add_profit').val(add_profit);
                        $(row).find('td:nth-child(13)').html(item.price);
                    }
                }); 
                toastr["success"]("profit updated successfully!", "Message");
            }
        });

    }); 

$(document).on('click', '.UpdateProduct', function () {
	var tr = $(this).closest('tr'); 
    var data ={
        _token: '{{csrf_token()}}',
        default_duty: $(tr).find('td .add_duty').val(),
        segmentprice1 :  $(tr).find('td .segment1').val(),
        segmentprice2:   $(tr).find('td .segment2').val(),
        segmentId1:   $(tr).find('td .segment1').data('ref'),
        segmentId2:   $(tr).find('td .segment2').data('ref'),
        brandId:      $(this).attr('data-brandId'),
        countryId:    $(this).attr('data-countryId'),
        websiteId:    $(this).attr('data-websiteid'),
        catId:    $(this).attr('data-catid'),
		add_profit : $(tr).find('td .add_profit').val(),
		country_code : $(tr).find('td .country_code').val(),
    }; 

    $.ajax({
        //updateSegmentPrice  real route
        url: "{{route('product_update')}}",
        type: 'post',
        data: data,
        beforeSend: function () {
			$("#loading-image").show();
        }
    }).done(function(response) {
        $("#loading-image").hide();
        if(response.status == false){
            toastr["error"]("Something went wrong, Please try again.");
        }else{
            toastr["success"](response.count + " products updated" );
        }
    });
});

</script>

@endsection