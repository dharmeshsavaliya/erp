@extends('layouts.app')

@section('content')

    <div class="row">
        <div class="col-lg-12 margin-tb">
          <h2>Orders Product List</h2>

          <form action="/order/products/list/" method="GET" class="form-inline align-items-start" id="searchForm">
            <div class="form-group mr-3">
                        <input name="term" type="text" class="form-control"
                               value="{{ isset($term) ? $term : '' }}"
                               placeholder="Search">
            </div>

            <div class="form-group mr-3">
              @php $brands = \App\Brand::getAll(); @endphp
              {!! Form::select('brand[]',$brands, (isset($brand) ? $brand : ''), ['placeholder' => 'Select a Brand','class' => 'form-control', 'multiple' => true]) !!}
            </div>

            <div class="form-group">
              @php $suppliers = new \App\ReadOnly\SupplierList(); @endphp
              {!! Form::select('supplier[]',$suppliers->all(), (isset($supplier) ? $supplier : ''), ['placeholder' => 'Select a Supplier','class' => 'form-control', 'multiple' => true]) !!}
            </div>
            <button type="submit" class="btn btn-image"><img src="/images/search.png" /></button>

          </form>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered mt-3">
        <tr>
          <th>Image</th>
          <th><a href="/order/products/list{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=supplier{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Supplier Name</a></th>
          <th>Supplier Price</th>
          <th><a href="/order/products/list{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=customer{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Customer</a></th>
          <th><a href="/order/products/list{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=customer_price{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Customer Price</a></th>
          <th><a href="/order/products/list{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=date{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Order Date</a></th>
          <th><a href="/order/products/list{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=delivery_date{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Delivery Date</a></th>
          <th><a href="/order/products/list{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=updated_date{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Updated Delivery Date</a></th>
          <th><a href="/order/products/list{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=status{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Order Status</a></th>
          <th><a href="/order/products/list{{ isset($term) ? '?term='.$term.'&' : '?' }}sortby=communication{{ ($orderby == 'desc') ? '&orderby=asc' : '' }}">Communication</a></th>
        </tr>
        @foreach ($products as $key => $product)
          {{-- {{dd(count($product->product->purchases))}} --}}
          <tr style="{{ $product['order'] ? '' : 'color:red' }}">
            <td>
              <a href="{{ route('order.show', $product['order_id']) }}">
                <img src="{{ $product['product'] ? $product['product']['imageurl'] : '' }}" class="img-responsive" alt="">
              </a>
            </td>
            <td>{{ $product['product']['supplier'] }}</td>
            <td>{{ (isset($product['product']['percentage']) || isset($product['product']['factor'])) && $product['product']['price'] ? ($product['product']['price'] - ($product['product']['price'] * $product['product']['percentage'] / 100) - $product['product']['factor']) : ($product['product']['price']) }}</td>
            <td>{{ $product['order'] ? $product['order']['client_name'] : '' }}</td>
            <td>{{ $product['product_price'] }}</td>
            <td>{{ $product['order'] ? Carbon\Carbon::parse($product['order']['created_at'])->format('d-m-Y') : '' }}</td>
            <td>{{ $product['order'] ? Carbon\Carbon::parse($product['order']['date_of_delivery'])->format('d-m-Y') : '' }}</td>
            <td>{{ $product['order'] ? Carbon\Carbon::parse($product['order']['estimated_delivery_date'])->format('d-m-Y') : '' }}</td>
            <td>
              <a href="{{ $product['order'] ? route('order.show', $product['order_id']) : '#' }}">{{ $product['order'] ? $product['order']['order_status'] : '' }}</a>
            </td>
            <td>
              @if (isset($product['communication']['body']))
                @if (strpos($product['communication']['body'], '<br>') !== false)
                  {{ substr($product['communication']['body'], 0, strpos($product['communication']['body'], '<br>')) }}
                @else
                  {{ $product['communication']['body'] }}
                @endif
              @else
                {{ $product['communication']['message'] }}
              @endif
            </td>
          </tr>
        @endforeach
    </table>

    {!! $products->appends(Request::except('page'))->links() !!}
@endsection
