
    @foreach ($leadOrder_array as $key => $leadOrder) 
        <tr class="">
            <td>{{ isset($leadOrder['id'])? $leadOrder['id'] : '' }}</td>
            <td class="expand-row-msg" data-name="customerName" data-id="{{$key.'-'.$leadOrder['id']}}">
                <span class="show-short-customerName-{{$key.'-'.$leadOrder['id']}}">{{ isset($leadOrder['customer_name'])? str_limit($leadOrder['customer_name'], 25, '..') : '' }}</span>
                <span style="word-break:break-all;" class="show-full-customerName-{{$key.'-'.$leadOrder['id']}} hidden">{{ isset($leadOrder['customer_name'])? $leadOrder['customer_name'] : '' }}</span>
            </td>
            <td>{{ isset($leadOrder['order_date'])? Carbon\Carbon::parse($leadOrder['order_date'])->format('d-m-Y') : '' }}</td>
            <td>{{ isset($leadOrder['product_id'])? $leadOrder['product_id'] : ''}}</td>
            <td class="expand-row-msg" data-name="leadOrder" data-id="{{$key.'-'.$leadOrder['id']}}">
                <span class="show-short-leadOrder-{{$key.'-'.$leadOrder['id']}}">{{ isset($leadOrder['name'])? str_limit($leadOrder['name'], 25, '..') : '' }}</span>
                <span style="word-break:break-all;" class="show-full-leadOrder-{{$key.'-'.$leadOrder['id']}} hidden">{{ isset($leadOrder['name'])? $leadOrder['name'] : ''}}</span>
            </td>
            <td>{{ isset($leadOrder['brand_name'])? $leadOrder['brand_name'] : ''}}</td>
            <td>{{ isset($leadOrder['price_inr'])? $leadOrder['price_inr'] : ''}}</td>
            <td>{{ isset($leadOrder['price_inr_discounted'])? $leadOrder['price_inr_discounted'] : ''}}</td>
            <td>
            @php 
             
             if($leadOrder['price_inr'])  {
              if($leadOrder['price_inr_discounted']) {
                $discount = $leadOrder['price_inr']*($leadOrder['price_inr_discounted']/100);
                $final_price = $leadOrder['price_inr'] - $discount;
              } 
              else {
               $final_price = $leadOrder['price_inr'];
              } 
            }
            else {
              $final_price = 0;
            }
            
            $gmu = 0;
            if($final_price) {
              $gmu = $final_price/1.22;
            }            
            @endphp
            {{ isset($final_price)? $final_price : ''}}</td>
            <td>{{number_format($gmu,2)}}</td>
        </tr>
    @endforeach
	