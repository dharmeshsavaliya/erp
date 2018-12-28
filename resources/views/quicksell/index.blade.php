@extends('layouts.app')


@section('content')
<div class="row">
  <div class="col-lg-6 margin-tb">
    <h2>Quick Sell</h2>
  </div>
  <div class="col-lg-6 margin-tb text-right">
    <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#imageModal">Upload</button>

    <div id="imageModal" class="modal fade" role="dialog">
      <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title">Upload Images</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>

          <form action="{{ route('quicksell.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="modal-body text-left">
              <div class="form-group">
                <input type="file" name="images[]" multiple required />
                @if ($errors->has('images'))
                <div class="alert alert-danger">{{$errors->first('images')}}</div>
                @endif
              </div>

              <div class="form-group">
                <strong>SKU:</strong>
                <input type="text" name="sku" class="form-control" />
                @if ($errors->has('sku'))
                <div class="alert alert-danger">{{$errors->first('sku')}}</div>
                @endif
              </div>

            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-secondary">Upload</button>
            </div>
          </form>
        </div>

      </div>
    </div>
  </div>

  <form action="{{ route('quicksell.index') }}" method="GET" class="form-inline align-items-start mb-5">
    {{-- <div class="form-group mr-3 mb-3">
      <input name="term" type="text" class="form-control" id="product-search" value="{{ isset($term) ? $term : '' }}" placeholder="sku,brand,category,status,stage">
    </div> --}}

    <div class="form-group mr-3 mb-3">
      {!! $filter_categories_selection !!}
    </div>

    <div class="form-group mr-3">
      @php $brands_select = \App\Brand::getAll();
      @endphp
      {!! Form::select('brand[]',$brands_select, (isset($brand) ? $brand : ''), ['placeholder' => 'Select a Brand','class' => 'form-control', 'multiple' => true]) !!}
    </div>

    <button type="submit" class="btn btn-image"><img src="/images/filter.png" /></button>
  </form>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
  {{ $message }}
</div>
@endif

<div class="row mt-6">
  @foreach ($products as $index => $product)
  <div class="col-md-3 col-xs-6 text-center">
    {{-- <a href="{{ route('leads.show', $lead['id']) }}"> --}}
    <img src="{{ $product->getMedia(config('constants.media_tags'))->first()
              ? $product->getMedia(config('constants.media_tags'))->first()->getUrl()
              : '' }}" class="img-responsive grid-image" alt="" />
    <p>Supplier : {{ $product->supplier }}</p>
    <p>Price : {{ $product->price }}</p>
    <p>Size : {{ $product->size }}</p>
    <p>Brand : {{ $product->brand ? $brands[$product->brand] : '' }}</p>
    <p>Category : {{ $product->category ? $categories[$product->category] : '' }}</p>

    <a href class="btn btn-image edit-modal-button" data-toggle="modal" data-target="#editModal" data-product="{{ $product }}"><img src="/images/edit.png" /></a>
    {!! Form::open(['method' => 'POST','route' => ['products.archive', $product->id],'style'=>'display:inline']) !!}
    <button type="submit" class="btn btn-image"><img src="/images/archive.png" /></button>
    {!! Form::close() !!}

    @can('admin')
    {!! Form::open(['method' => 'DELETE','route' => ['products.destroy', $product->id],'style'=>'display:inline']) !!}
    <button type="submit" class="btn btn-image"><img src="/images/delete.png" /></button>
    {!! Form::close() !!}
    @endcan
    {{-- </a> --}}
  </div>
  @endforeach
</div>

{!! $products->links() !!}

<div id="editModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">Edit Quick Product</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <form action="" method="POST" enctype="multipart/form-data" id="updateForm">
        @csrf

        <div class="modal-body text-left">
          <div class="form-group">
            <input type="file" name="images[]" multiple />
            @if ($errors->has('images'))
            <div class="alert alert-danger">{{$errors->first('images')}}</div>
            @endif
          </div>

          <div class="form-group">
            @php $supplier_list = (new \App\ReadOnly\SupplierList)->all();
            @endphp
            <select class="form-control" name="supplier" id="supplier_select">
              <option value="">Select Supplier</option>
              @foreach ($supplier_list as $index => $value)
              <option value="{{ $index }}" {{ $index == old('supplier') ? 'selected' : '' }}>{{ $value }}</option>
              @endforeach
            </select>
            @if ($errors->has('supplier'))
            <div class="alert alert-danger">{{$errors->first('supplier')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Price:</strong>
            <input type="number" name="price" class="form-control" id="price_field" />
            @if ($errors->has('price'))
            <div class="alert alert-danger">{{$errors->first('price')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Size:</strong>
            <input type="text" name="size" class="form-control" id="size_field" />
            @if ($errors->has('size'))
            <div class="alert alert-danger">{{$errors->first('size')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Brand:</strong>
            <select name="brand" class="form-control" id="brand_field">
              <option value="">Select Brand</option>
              @foreach ($brands as $id => $brand)
              <option value="{{ $id }}">{{ $brand }}</option>
              @endforeach
            </select>
            @if ($errors->has('brand'))
            <div class="alert alert-danger">{{$errors->first('brand')}}</div>
            @endif
          </div>

          <div class="form-group">
            <strong>Category:</strong>
            {!! $category_selection !!}
            @if ($errors->has('category'))
            <div class="alert alert-danger">{{$errors->first('category')}}</div>
            @endif
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-secondary">Update</button>
        </div>
      </form>
    </div>

  </div>
</div>

<script type="text/javascript">
  $(document).on('click', '.edit-modal-button', function() {
    var product = $(this).data('product');
    var url = 'quickSell/' + product.id + '/edit';

    $('#updateForm').attr('action', url);
    $('#supplier_select').val(product.supplier);
    $('#price_field').val(product.price);
    $('#size_field').val(product.size);
    $('#brand_field').val(product.brand);
    $('#category_selection').val(product.category);
  });
</script>

@endsection
