@extends('layouts.app')

@section("styles")
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/css/bootstrap-multiselect.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
<style>
.mr-pd {
    margin: 0px;
    padding: 2px;
    margin-bottom:10px !important;
}
.des-pd {
    padding:2px;
}
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Inventory Data ({{ $inventory_data_count }})</h2>
    </div>
</div>

@if ($message = Session::get('success'))
<div class="alert alert-success">
    {{ $message }}
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
<div class="col-lg-12 margin-tb">
    <form action="{{ url('productinventory/inventory-list') }}" method="GET" class="form-inline align-items-start">
        <div class="form-group mr-pd col-md-2">
            {!! Form::text('term',request("term"), ['placeholder' => 'Search by product, sku, brand, category','class' => 'form-control','style' => 'width: 100%;']) !!}
        </div>
        <div class="form-group mr-pd col-md-2">
            {!! Form::select('brand_names[]',$brands_names, request("brand_names",[]), ['data-placeholder' => 'Select a Brand','class' => 'form-control select-multiple2', 'multiple' => true]) !!}
        </div>
        <div class="form-group mr-pd col-md-2">
            {!! $products_categories !!}
        </div>
        <div class="form-group mr-pd col-md-2">
            {!! Form::select('in_stock',["" => "--All--" , "1" => "In Stock", "2" => "Out Of Stock"], request("in_stock",null), ['data-placeholder' => 'Select a In Stock','class' => 'form-control']) !!}
        </div>
        <div class="form-group mr-3 mb-3">
            {!! Form::select('supplier[]',$supplier_list, request("supplier",[]), ['data-placeholder' => 'Select a Supplier','class' => 'form-control select-multiple2', 'multiple' => true]) !!}
        </div>
        <!-- <div class="form-group mr-3 mb-3">
                {!! Form::select('product_sku[]',$products_sku, request("product_sku",[]), ['data-placeholder' => 'Select a Sku','class' => 'form-control select-multiple2', 'multiple' => true]) !!}
            </div> -->
        <div class="form-group mr-pd col-md-1">
            {!! Form::select('product_status[]',$status_list, request("product_status",[]), ['data-placeholder' => 'Select a Status','class' => 'form-control select-multiple2', 'multiple' => true]) !!}
        </div>
        <div class="form-group mr-pd col-md-2">
            {!! Form::checkbox('no_category',"on",request("no_category"), ['class' => 'form-control']) !!} No Category
        </div>
        <div class="form-group mr-pd col-md-2">
            <div class='input-group date' id='filter-date'>
                <input type='text' class="form-control" name="date" value="{{ request('date','') }}" placeholder="Date" />
                <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
        <div class="form-group mr-pd col-md-1">
        <button type="submit" class="btn btn-secondary"><i class="fa fa-filter"></i>Filter</button>
        </div>
        

    </form>
</div>
<div class="table-responsive" id="inventory-data">
    <table class="table table-bordered infinite-scroll">
        <thead>
            <tr>
                <th>Id</th>
                <th>Sku</th>
                <th>Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Supplier</th>
                <th>Status</th>
                <th>Created Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @include("product-inventory.inventory-list-partials.grid")
        </tbody>
    </table>
</div>
<img class="infinite-scroll-products-loader center-block" src="/images/loading.gif" alt="Loading..." style="display: none" />


<div id="medias-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Medias</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

<div id="status-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Status History</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>


<div id="inventory-history-modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Inventory History</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-multiselect/0.9.15/js/bootstrap-multiselect.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<script>
    $(".select-multiple").multiselect();
    $(".select-multiple2").select2();

    $('body').delegate('.show-medias-modal', 'click', function() {
        id = $(this).data('id');
        $.ajax({
                url: '/productinventory/product-images/' + id,
                type: 'GET'
            })
            .done(function(data) {
                $('#medias-modal .modal-body').html('');
                let result = '';
                if (data.urls.length > 0) {
                    result += '<table class="table table-bordered">';
                    result += '<thead><th>Image</th></thead>';
                    result += '<tbody>';
                    for (var i = 0; i < data.urls.length; i++) {
                        result += '<tr>';
                        result += '<td><img style="height:100px" src="' + data.urls[i] + '" /></td>'
                        result += '</tr>';
                    }
                    result += '</tbody>';
                    result += '</table>';

                } else {
                    result = '<h3>This product dont have any media </h3>';
                }
                $('#medias-modal .modal-body').html(result);

            })
            .fail(function(jqXHR, ajaxOptions, thrownError) {});


        // let data = $(this).parent().parent().find('.medias-data').attr('data')

        // let result = '';

        // if(data != '[]') {
        //     data = JSON.parse(data)

        //     result += '<table class="table table-bordered">';
        //     result += '<thead><th>Directory</th><th>filename</th><th>extension</th><th>disk</th></thead>';
        //     result += '<tbody>';
        //     for(let value in data) {
        //         console.log(data[value]);
        //         result += '<tr>';
        //         result += "<td>"+data[value].directory+"</td>"
        //         result += "<td>"+data[value].filename+"</td>"
        //         result += "<td>"+data[value].extension+"</td>"
        //         result += "<td>"+data[value].disk+"</td>"
        //         result += '</tr>';
        //     }
        //     result += '</tbody>';
        //     result += '</table>';

        // } else {
        //     result = '<h3>this product dont have any media</h3>';
        // }


        $('#medias-modal').modal('show')
    })

    $('body').delegate('.show-status-history-modal', 'click', function() {

        let data = $(this).parent().parent().find('.status-history').attr('data')
        let result = '';

        if (data != '[]') {
            data = JSON.parse(data)

            result += '<table class="table table-bordered">';
            result += '<thead><th>old status</th><th>new status</th><th>created at</th></thead>';
            result += '<tbody>';
            for (let value in data) {
                result += '<tr>';
                result += "<td>" + data[value].old_status + "</td>"
                result += "<td>" + data[value].new_status + "</td>"
                result += "<td>" + data[value].created_at + "</td>"
                result += '</tr>';
            }
            result += '</tbody>';
            result += '</table>';

        } else {
            result = '<h3>This Product dont have status history</h3>';
        }

        $('#status-history-modal .modal-body').html(result)

        $('#status-history-modal').modal('show')
    })


    $('body').delegate('.show-inventory-history-modal', 'click', function() {
    // let data = $(this).parent().parent().find('.inventory-history').attr('data')
        var id = $(this).data('id');
            $.ajax({
                url: '/productinventory/inventory-history/'+id,
                type: 'GET',
                dataType:'json',
                success: function (response) {
                     let result = '';

                    result += '<table class="table table-bordered">';
                    result += '<thead><th>Supplier</th><th>Date</th><th>Prev Stock</th><th>In Stock</th></thead>';
                    result += '<tbody>';
                    $.each(response.data, function(i, item) {
                        result += '<tr>';
                            result += "<td>" + item.supplier + "</td>"
                            result += "<td>" + item.date + "</td>"
                            result += "<td>" + item.prev_in_stock + "</td>"
                            result += "<td>" + item.in_stock + "</td>"
                            result += '</tr>';
                        });

                        result += '</tbody>';
                         result += '</table>';
                         $('#inventory-history-modal .modal-body').html(result)
                        $('#inventory-history-modal').modal('show')
                },
                error: function () {
                }
            });
return;
    if (data != '[]') {
        data = JSON.parse(data)

        result += '<table class="table table-bordered">';
        result += '<thead><th>Supplier</th><th>Date</th><th>Prev Stock</th><th>In Stock</th></thead>';
        result += '<tbody>';
        for (let value in data) {
            result += '<tr>';
            result += "<td>" + data[value].supplier + "</td>"
            result += "<td>" + data[value].date + "</td>"
            result += "<td>" + data[value].prev_in_stock + "</td>"
            result += "<td>" + data[value].in_stock + "</td>"
            result += '</tr>';
        }
        result += '</tbody>';
        result += '</table>';

    } else {
        result = '<h3>This Product dont have inventory history</h3>';
    }

    $('#inventory-history-modal .modal-body').html(result)

    $('#inventory-history-modal').modal('show')
    })
    var isLoadingProducts = false;
    let page = 1;
    let last_page = {{$inventory_data->lastPage()}};
    $(function() {
        $(window).scroll(function() {
            if (($(window).scrollTop() + $(window).outerHeight()) >= ($(document).height() - 2500)) {
                loadMoreProducts();
            }
        });
    });

    function loadMoreProducts() {
        if (isLoadingProducts) return;

        isLoadingProducts = true;

        var loader = $('.infinite-scroll-products-loader');

        let url = "";
        page++;

        @if(!empty(request()->input()))
        url = new DOMParser().parseFromString('{{ url(request()->getRequestUri()."&page=") }}' + page, "text/html");
        @else
        url = new DOMParser().parseFromString('{{ url(request()->getRequestUri()."?page=") }}' + page, "text/html");
        @endif

        let parsed_url = url.documentElement.textContent;

        $.ajax({
                url: parsed_url,
                type: 'GET',
                beforeSend: function() {
                    loader.show();
                }
            })
            .done(function(data) {
                loader.hide();
                if (page > last_page) return;
                $('#inventory-data tbody').append(data);
                isLoadingProducts = false;
            })
            .fail(function(jqXHR, ajaxOptions, thrownError) {
                console.error('something went wrong');
                isLoadingProducts = false;
            });
    }
    $('#filter-date').datetimepicker({
        format: 'YYYY-MM-DD'
    });
</script>
@endsection