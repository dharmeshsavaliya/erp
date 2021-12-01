@extends('layouts.app')
@section('title')
    New Sizes Reference 
@endsection
@section('content')
<style type="text/css">
    .small-field { 
        margin-bottom: 0px;
     }
     .small-field-btn {
        padding: 0px 13px;
     }   
</style>
<div class="row">
    <div class="col-md-12">
        <h2 class="page-heading">New Sizes Reference ({{ $sizes->count() }})</h2>
    </div>
	<div class="col-md-12">
        <form>
            <div class="form-group col-md-3">
                <input type="search" name="search" class="form-control" value="{{ request('search') }}">
            </div>
            <div class="form-group col-md-2">
                <button type="submit" class="btn btn-secondary">Search</button>
            </div>
        </form>
<!--         <div class="form-group small-field col-md-3">
            <select class="select2 form-control change-list-categories">
                @foreach($sizes as $size)
                    <option value="{{ $size->id }}">{{ $size->name }}</option>
                @endforeach
            </select>
        </div> -->
<!--         <div class="form-group col-md-4">
            <button type="button" class="btn btn-secondary update-category-selected col-md-3">Update</button>
        </div> -->
    </div>
    <div class="col-md-12 mt-5">
        <table class="table table-bordered">
            <tr>
                <th width="10%"><input type="checkbox" class="check-all-btn">&nbsp;SN</th>
                <th width="30%">Size</th>
                <th width="40%">Erp Size</th>
               <!--  <th width="20%">Action</th> -->
            </tr>
            <?php $count = 1; ?>
            @foreach($sizes as $size)
                <tr>
                    <td>{{ $count }}</td>
                    <td>
						<span class="call-used-product">{{ $size->size }}</span> 
					</td>
                    <td>
						<select class="select2 form-control change-list-size" id="{{ $size->id }}">
                            @foreach($erpSizes as $erpSize)
                                <option value="{{ $erpSize->id }}">{{ $erpSize->erp_size }}</option>
                            @endforeach
                        </select>
                   </td>
                </tr>
                <?php $count++; ?>
            @endforeach
        </table>
       
    </div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal show-listing-exe-records" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
    </div>  
</div>
@endsection
@section('scripts')
    <script type="text/javascript">
            $(".select2").select2({"tags" : true});
            $(document).on("change",".change-list-size",function() {
                var $this = $(this);
                $.ajax({
                    type: 'POST',
                    url: '/sizes/new-references/update-size',
                    beforeSend: function () {
                        $("#loading-image").show();
                    },
                    data: {
                        id : $this.data("id"),
                        erp_size_id : $this.val()
                    },
                    dataType: "json"
                }).done(function (response) {
                    $("#loading-image").hide();
                    toastr['success']('Products updated successfully', 'success');
                }).fail(function (response) {
                    $("#loading-image").hide();
                    console.log("Sorry, something went wrong");
                });
            });
    </script>
@endsection
