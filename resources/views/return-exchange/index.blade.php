@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'List | Return Exchange')

@section('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
	<style>
	.form-group-extended{
		margin-bottom: 5px !important;
		width:19.5%  !important;
	}
	.form-group-extended input[type=text]{
		width:99%  !important;
	}
	.action{
		padding: 3px !important;
	}
	.action button{
		padding: 3px !important;
	}
	.modal-dialog-wide{ 
		max-width: 100%;
		width: auto !important;
		/*display: inline-block;*/
	}
	</style>
@endsection

@section('large_content')

<div class="row" id="return-exchange-page">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">Return Exchange <span id="total-counter"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
    	<div class="row" style="margin-bottom:20px;">
	    	<div class="col">
		    	<div class="h" style="margin-bottom:10px;">
		    		<form class="form-inline return-exchange-handler" method="post">
					  <div class="row">
				  		<div class="col">
				  			<div class="form-group form-group-extended">
							    <!--<label for="from">Customer Name:</label>-->
							    <?php echo Form::text("customer_name",request("customer_name"),["class"=> "form-control","placeholder" => "Enter Customer Name"]) ?>
						  	</div>
							<div class="form-group form-group-extended">
							    <!--<label for="from">Customer Email:</label>-->
							    <?php echo Form::text("customer_email",request("customer_email"),["class"=> "form-control","placeholder" => "Enter Customer Email"]) ?>
						  	</div>
							<div class="form-group form-group-extended">
							    <!--<label for="from">Customer Id:</label>-->
							    <?php echo Form::text("customer_id",request("customer_id"),["class"=> "form-control","placeholder" => "Enter Customer Id"]) ?>
						  	</div>
							<div class="form-group form-group-extended">
							    <!--<label for="from">Order Id:</label>-->
							    <?php echo Form::text("order_id",request("order_id"),["class"=> "form-control","placeholder" => "Enter Order Id"]) ?>
						  	</div>
						  	<div class="form-group form-group-extended">
							    <!--<label for="from">Product:</label>-->
							    <?php echo Form::text("product",request("product"),["class"=> "form-control","placeholder" => "Enter product sku/id/name"]) ?>
						  	</div>
							<div class="form-group form-group-extended">
							    <!--<label for="from">Product:</label>-->
							    <?php echo Form::text("website",request("website"),["class"=> "form-control","placeholder" => "Website"]) ?>
						  	</div>
				  			<div class="form-group form-group-extended">
							    <!--<label for="action">Status:</label>-->
							    <?php /*?><?php echo Form::select("status",\App\ReturnExchange::STATUS,request("limti"),[
							    	"class" => "form-control select2",
							    	"placeholder" => "-- Select Status --"
							    ]) ?><?php */?>
								<?php echo Form::select("status",\App\ReturnExchangeStatus::pluck("status_name","id")->toArray(),request("limti"),[
							    	"class" => "form-control",// select2
							    	"placeholder" => "-- Select Status --"
							    ]) ?>
						  	</div>
						  	<div class="form-group form-group-extended">
							    <!--<label for="action">Type:</label>-->
							    <?php echo Form::select("type",["refund" => "Refund", "exchange" => "Exchange"],request("limti"),[
							    	"class" => "form-control",//select2
							    	"placeholder" => "-- Select Type --"
							    ]) ?>
						  	</div>
						  	<div class="form-group form-group-extended">
							    <!--<label for="action">Number of records:</label>-->
							    <?php echo Form::select("limit",[10 => "10", 20 => "20", 30 => "30" , 50 => "50", 100 => "100" , 500 => "500" , 1000 => "1000"],request("limti"),[
									"class" => "form-control recCount",//select2
									"placeholder" => "Number of records"
									]) ?>
						  	</div>
						  	<div class="form-group form-group-extended">
						  		<!--<label for="button">&nbsp;</label>-->
						  		<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
						  			<img src="/images/search.png" style="cursor: default;">
						  		</button>
						  	</div>		
				  		</div>
					  </div>	
					</form>	
		    	</div>
		    </div>
	    </div>	
		<div class="row">
			<div class="col-md-12" style="padding:8px;">
				<div class="pull-right">
				  <a href="#" class="btn btn-xs btn-secondary delete-orders" id="bulk_delete">
						Delete
				  </a>
				  <a href="#" class="btn btn-xs update-customer btn-secondary" id="bulk_update">
						Update
				  </a>
				  <a href="#" class="btn btn-xs update-customer btn-secondary" id="create_status">
						Create Status
				  </a>
				</div>
			</div>
		</div>
		<div class="col-md-12 margin-tb infinite-scroll" id="page-view-result">

		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
  	<div class="modal-dialog modal-lg" role="document">
  	</div>	
</div>

@include("return-exchange.templates.list-template")
@include("return-exchange.templates.modal-emailToCustomer")
@include("return-exchange.templates.modal-createstatus")
@include("return-exchange.templates.modal-productDetails")
@endsection

@section('scripts')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<script type="text/javascript" src="/js/jsrender.min.js"></script>
	<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
	<script src="/js/jquery-ui.js"></script>
	<script type="text/javascript" src="/js/common-helper.js"></script>
	<script type="text/javascript" src="/js/return-exchange.js"></script>
	<script type="text/javascript">
		msQueue.init({
			bodyView : $("#return-exchange-page"),
			baseUrl : "<?php echo url("/"); ?>"
		});
		
		$(document).on('click', '.send-email-to-customer', function () {
            $('#emailToCustomerModal').find('form').find('input[name="customer_id"]').val($(this).data('id'));
            $('#emailToCustomerModal').modal("show");
        });
				
		$(document).on('change', '.recCountproduct', function () {
            $(this).parent().closest("form").submit();
        });
		
		$(document).on('submit', '#customerUpdateForm', function (e) {
			e.preventDefault();
			var data = $(this).serializeArray();
			$.ajax({
				url: "{{route('return-exchange.updateCusromer')}}",
				type: 'POST',
				data: data,
				success: function (response) {
					toastr['success']('Successful', 'success');
					$('#emailToCustomerModal').modal('hide');
					$("#customerUpdateForm").trigger("reset");
					$("tr").find('.select-id-input').each(function () {
					  if ($(this).prop("checked") == true) {
						$(this).prop("checked", false);
					  }
					});
					window.location.reload();
				},
				error: function () {
					alert('There was error loading priority task list data');
				}
			});
		});
		
		$(document).on('submit', '#createStatusForm', function (e) {
			e.preventDefault();
			var data = $(this).serializeArray();
			$.ajax({
				url: "{{route('return-exchange.createStatus')}}",
				type: 'POST',
				data: data,
				success: function (response) {
					toastr['success']('Successful', 'success');
					$('#createstatusModal').modal('hide');
					window.location.reload();
				},
				error: function () {
					alert('There was error loading priority task list data');
				}
			});
		});
		
	</script>
@endsection


