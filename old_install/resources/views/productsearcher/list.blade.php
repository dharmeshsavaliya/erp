@extends('layouts.app')


@section('content')
    <div class="row">
        <div class="col-lg-12 margin-tb">
            <div class="pull-left">
                <h2>Searcher</h2>
            </div>
        </div>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">
            <p>{{ $message }}</p>
        </div>
    @endif

    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Sku</th>
            <th>Image</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($productsearcher as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->sku }}</td>
                <td><img src="/uploads/{{ $product->image }}" class="img-responsive" style="max-width: 200px;" alt=""></td>
                <td>
                    <form action="{{ route('productselection.destroy',$product->id) }}" method="POST">
                        {{--<a class="btn btn-info" href="{{ route('products.show',$product->id) }}">Show</a>--}}
                        @can('product-edit')
                            <a class="btn btn-primary" href="{{ route('productselection.edit',$product->id) }}">Edit</a>
                        @endcan

                        @csrf
                        @method('DELETE')
                        @can('product-delete')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        @endcan
                    </form>
                </td>
            </tr>
        @endforeach
    </table>


    {!! $productsearcher->links() !!}


@endsection