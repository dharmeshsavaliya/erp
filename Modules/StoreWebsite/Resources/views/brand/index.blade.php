@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}
</style>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
    	<div class="row">
	    	<div class="col col-md-9">
		    	<div class="row">
	    			<button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button>
				 </div> 		
		    </div>
		    <div class="col">
		    	<div class="h" style="margin-bottom:10px;">
					<div class="row">
		    			<form class="form-inline message-search-handler" method="get">
					  		<div class="col">
					  			<div class="form-group">
								    <label for="keyword">Keyword:</label>
								    <?php echo Form::text("keyword",request("keyword"),["class"=> "form-control","placeholder" => "Enter keyword"]) ?>
							  	</div>
							  	<div class="form-group">
							  		<label for="button">&nbsp;</label>
							  		<button type="submit" style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-search-action">
							  			<img src="/images/search.png" style="cursor: default;">
							  		</button>
							  	</div>		
					  		</div>
				  		</form>
					</div>
		    	</div>
		    </div>
	    </div>	
		<div class="col-md-12 margin-tb" id="page-view-result">
			<div class="row">
				<table class="table table-bordered">
				    <thead>
				      <tr>
				      	<th width="5%">Id</th>
				        <th width="10%">Brand</th>
				        <th width="10%">Min Price</th>
				        <th width="10%">Max Price</th>
				        <?php foreach($storeWebsite as $sw) { ?>
				        	<th width="10%"><?php echo $sw->title; ?></th>
				        <?php } ?>	
				      </tr>
				    </thead>
				    <tbody>
				    	<?php foreach($brands as $brand) { ?>
 					      <tr>
					      	<td><?php echo $brand->id; ?></td>
					      	<td><?php echo $brand->name; ?></td>
					      	<td><?php echo $brand->min_sale_price; ?></td>
					      	<td><?php echo $brand->max_sale_price; ?></td>
					      	<?php foreach($storeWebsite as $sw) { 
					      			$checked = (isset($apppliedResult[$brand->id]) && in_array($sw->id, $apppliedResult[$brand->id])) ? "checked" : ""; 
					      		?>
					        	<td><input data-brand="<?php echo $brand->id; ?>" data-sw="<?php echo $sw->id; ?>" <?php echo $checked; ?> class="push-brand" type="checkbox" name="brand_website"></td>
					        <?php } ?>
					      </tr>
					    <?php } ?>
				    </tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<div id="loading-image" style="position: fixed;left: 0px;top: 0px;width: 100%;height: 100%;z-index: 9999;background: url('/images/pre-loader.gif') 
          50% 50% no-repeat;display:none;">
</div>
<div class="common-modal modal" role="dialog">
  	<div class="modal-dialog" role="document">
  	</div>	
</div>

<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>
<script type="text/javascript" src="/js/common-helper.js"></script>
<script type="text/javascript" src="/js/store-website-brand.js"></script>

<script type="text/javascript">
	page.init({
		bodyView : $("#common-page-layout"),
		baseUrl : "<?php echo url("/"); ?>"
	});
</script>

@endsection

