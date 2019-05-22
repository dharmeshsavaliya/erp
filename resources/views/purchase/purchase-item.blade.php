<div class="table-responsive">
    <table class="table table-bordered">
    <tr>
      <th width="5%"></th>
      <th width="5%"><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=id{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}" class="ajax-sort-link">ID</a></th>
      <th width="5%"><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=date{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}" class="ajax-sort-link">Date</a></th>
      <th width="5%"><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=purchase_handler{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}" class="ajax-sort-link">Purchase Handler</a></th>
      <th width="10%"><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=supplier{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}" class="ajax-sort-link">Supplier Name</a></th>
      <th width="10%"><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}" class="ajax-sort-link">Supplier Purchase Status</a></th>
      <th width="20%">Customer Names</th>
      <th width="5%">Products</th>
      {{-- <th>Qty</th> --}}
      <th width="5%">Retail Price</th>
      {{-- <th>Sold Price</th> --}}
      <th width="5%">Buying Price</th>
      <th width="5%">Gross Profit</th>
      {{-- <th>Message Status</th>
      <th><a href="/purchases{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=communication{{ ($orderby == 'DESC') ? '&orderby=ASC' : '' }}" class="ajax-sort-link">Communication</a></th> --}}
      <th width="10%">Action</th>
    </tr>
    @foreach ($purchases_array as $key => $purchase)
      @php
        $products_count = 1;
        if (count($purchase['products']) > 0) {
          // foreach ($purchase['products'] as $product) {
          //   $products_count += count($product['orderproducts']);
          // }

          $products_count = count($purchase['products']) + 1;
        }
      @endphp
        <tr>
          <td rowspan="{{ $products_count }}">
            <input type="checkbox" name="select" class="export-checkbox" data-id="{{ $purchase['id'] }}">
          </td>
          <td rowspan="{{ $products_count }}">{{ $purchase['id'] }}</td>
          <td rowspan="{{ $products_count }}">{{ Carbon\Carbon::parse($purchase['created_at'])->format('d-m-Y') }}</td>
          <td rowspan="{{ $products_count }}">{{ $purchase['purchase_handler'] ? $users[$purchase['purchase_handler']] : 'nil' }}</td>
          <td rowspan="{{ $products_count }}">{{ $purchase['purchase_supplier']['supplier'] }}</td>
          <td rowspan="{{ $products_count }}">{{ $purchase['status']}}</td>
        </tr>

        @if ($purchase['products'])
          @php
            $qty = 0;
            $sold_price = 0;
          @endphp
          @foreach ($purchase['products'] as $product)
            <tr>
              <td>
                @if ($product['orderproducts'])
                  {{-- <ul> --}}
                    @foreach ($product['orderproducts'] as $order_product)
                      <li>
                        @if ($order_product['order'])
                          @if ($order_product['order']['customer'])
                            {{ $order_product['order']['customer']['name'] }}
                          @else
                            No Customer
                          @endif
                        @else
                          No Order
                        @endif

                         - Qty. <strong>{{ $qty = $order_product['qty'] }}</strong>
                         - Sold Price: <strong>{{ $order_product['product_price'] }}</strong>

                        @php
                          $sold_price += $order_product['product_price'];
                        @endphp
                      </li>
                      @php $qty = 0; @endphp
                    @endforeach
                  {{-- </ul> --}}
                @else
                  <li>No Order Product</li>
                @endif
              </td>
              <td>
                <img src="{{ $product['imageurl'] }}" class="img-responsive" width="50px">
              </td>
              {{-- <td>
                @if (count($product['orderproducts']) > 0)
                  <ul>
                    @foreach ($product['orderproducts'] as $order_product)
                      <li>{{ $qty = $order_product['qty'] }}</li>
                      @php

                        $qty = 0;
                      @endphp
                    @endforeach
                  </ul>
                @endif
              </td> --}}
              <td>{{ $product['price'] }}</td>
              {{-- <td>
                @php $sold_price = 0; @endphp
                <ul>
                  @foreach ($product['orderproducts'] as $order_product)
                    <li>{{ $order_product['product_price'] }}</li>

                    @php
                      $sold_price += $order_product['product_price'];
                    @endphp
                  @endforeach
                </ul>
              </td> --}}
              <td>
                @php $actual_price = 0; @endphp
                @php $actual_price += $product['price'] @endphp

                {{ $product['price'] * 78 }}
              </td>
              <td>
                {{ $sold_price - ($actual_price * 78) }}
              </td>

            </tr>
          @endforeach
        @endif
        <tr>
          <td colspan="12">
            <div class="pull-right">
              <a class="btn btn-image" href="{{ route('purchase.show',$purchase['id']) }}"><img src="/images/view.png" /></a>

              {!! Form::open(['method' => 'DELETE','route' => ['purchase.destroy', $purchase['id']],'style'=>'display:inline']) !!}
              <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
              {!! Form::close() !!}

              {!! Form::open(['method' => 'DELETE','route' => ['purchase.permanentDelete', $purchase['id']],'style'=>'display:inline']) !!}
              <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
              {!! Form::close() !!}
            </div>
          </td>
        </tr>
        {{-- <td>
          <ul>
            @foreach ($purchase['products'] as $product)
              <li>
                {{ $product['orderproducts'] ? ($product['orderproducts'][0]['order'] ? ($product['orderproducts'][0]['order']['customer'] ? $product['orderproducts'][0]['order']['customer']['name'] : 'No Customer') : 'No Order') : 'No Order Product' }}
              </li>
            @endforeach
          </ul>
        </td> --}}
            {{-- <td>
              @foreach ($purchase['products'] as $product)
                <img src="{{ $product['imageurl'] }}" class="img-responsive" width="50px">
              @endforeach
            </td> --}}


            {{-- <td>
              @php
                $qty = 0;
              @endphp
              <ul>
                @foreach ($purchase['products'] as $product)
                  @if (count($product['orderproducts']) > 0)
                    @foreach ($product['orderproducts'] as $order_product)
                      @php
                        $qty += $order_product['qty'];
                      @endphp
                    @endforeach
                  @endif

                  <li>
                    {{ $qty }}
                  </li>

                  @php
                    $qty = 0;
                  @endphp
                @endforeach
              </ul>
            </td> --}}
            {{-- <td>
              {{-- @php $retail_price = 0; @endphp
              @foreach ($purchase['products'] as $product)
                @php $retail_price += $product['price'] @endphp
              @endforeach

              {{ $retail_price }}

              <ul>
                @foreach ($purchase['products'] as $product)
                  <li>
                    {{ $product['price'] }}
                  </li>
                @endforeach
              </ul>
            </td> --}}
            {{-- <td>
              <ul>
                @php $sold_price = 0; @endphp
                @foreach ($purchase['products'] as $product)
                  @foreach ($product['orderproducts'] as $order_product)
                    <li>{{ $order_product['product_price'] }}</li>

                    @php
                      $sold_price += $order_product['product_price'];
                    @endphp
                  @endforeach
                @endforeach
              </ul>
            </td> --}}
            {{-- <td>
              <ul>
                @php $actual_price = 0; @endphp
                @foreach ($purchase['products'] as $product)
                  @php $actual_price += $product['price'] @endphp

                  <li>{{ $product['price'] * 78 }}</li>
                @endforeach
              </ul>
            </td> --}}
            {{-- <td>
              {{ $sold_price - ($actual_price * 78) }}
            </td> --}}
            {{-- <td>
              @if ($purchase['communication']['status'] != null && $purchase['communication']['status'] == 0)
                Unread
              @elseif ($purchase['communication']['status'] == 5)
                Read
              @elseif ($purchase['communication']['status'] == 6)
                Replied
              @elseif ($purchase['communication']['status'] == 1)
                Awaiting Approval
              @elseif ($purchase['communication']['status'] == 2)
                Approved
              @elseif ($purchase['communication']['status'] == 4)
                Internal Message
              @endif
            </td>
            <td>
              @if (strpos($purchase['communication']['body'], '<br>') !== false)
                {{ substr($purchase['communication']['body'], 0, strpos($purchase['communication']['body'], '<br>')) }}
              @else
                {{ $purchase['communication']['body'] }}
              @endif
            </td> --}}

        </tr>
    @endforeach
</table>
</div>

{!! $purchases_array->appends(Request::except('page'))->links() !!}
