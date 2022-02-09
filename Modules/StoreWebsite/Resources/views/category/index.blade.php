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
	    			{{-- <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button> --}}
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
		<div class="row">
			<div class="col-md-12">
				<div class="alert alert-success" id="alert-msg" style="display: none;">
					<p></p>
				</div>
			</div>
		</div>
		<div class="col-md-12 margin-tb" id="page-view-result">
			<div class="row">
				<table class="table table-bordered">
				    <thead>
				      <tr>
				      	<th>Id</th>
				        <th>Category</th>
				        <?php foreach($storeWebsite as $sw) { ?>
				        	<th><?php echo $sw->website; ?></th>
				        <?php } ?>	
				      </tr>
				    </thead>
				    <tbody>
				    	<?php foreach($categories as $category) { ?>
 					      <tr>
					      	<td><?php echo $category->id; ?></td>
					      	<td><?php echo $category->title; ?> <span href="javascript:void(0);" class="checkinglog" data-id="{{ $category->id }}" ><i class="fa fa-history"></i></span> </td>
					      	<?php foreach($storeWebsite as $sw) { 
					      			$checked = ""; 
					      			$catName = ""; 
								  ?>
								  @forelse ($appliedQ as $item)
									  	@if($item->category_id == $category->id && $item->store_website_id == $sw->id)
										  	@php $checked = "checked"; $catName = $item->category_name; @endphp
									  	@endif
								  @empty
								  @endforelse
					        	<td>
									<input data-category="{{ $category->id }}" data-sw="{{ $sw->id }}" <?php echo $checked; ?> class="push-category" type="checkbox" name="category_website">
									<input data-category="{{ $category->id }}" data-sw="{{ $sw->id }}" class="rename-category" type="text" name="category_name" value="{{ $catName }}">
								</td>
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

<div id="category-history-modal" class="modal fade" role="dialog">
  	<div class="modal-dialog modal-lg">
    	<div class="modal-content">
	      	<div class="modal-body">
	        	<div class="col-md-12">
	          		<table class="table table-bordered">
	            		<thead>
			              	<tr>
			                	<th>Sl no</th>
			                	<th>Log case id</th>
			                	<th>Category id</th>
			                	<th>Store id</th>
			                	<th>Log detail</th>
			                	<th>Description</th>
			              	</tr>
			            </thead>
	            		<tbody class="category-history-list-view">
	                    </tbody>
	          		</table>
	        	</div>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      	</div>
    	</div>
  	</div>
</div>


<script type="text/javascript" src="/js/jsrender.min.js"></script>
<script type="text/javascript" src="/js/jquery.validate.min.js"></script>
<script src="/js/jquery-ui.js"></script>

<script>
$(document).on('change', '.push-category', function() {
	var catId = $(this).attr('data-category');
	var swId = $(this).attr('data-sw');
	var check = 0;
	if($(this).is(":checked")) {
		check = 1;
	} else {
		$(this).parent('td').find('.rename-category').val('');
	}
	var catName = $(this).parent('td').find('.rename-category').val();
	ajaxCall(catId, swId, check, catName);
});

$(document).on('blur', '.rename-category', function() {
	var catId = $(this).attr('data-category');
	var swId = $(this).attr('data-sw');
	var check = 0;
	if($(this).parent('td').find('.push-category').is(":checked")) {
		check = 1;
	}
	var catName = $(this).val();
	ajaxCall(catId, swId, check, catName);
});

function ajaxCall(catId, swId, check, catName) {
	$.ajax({
		url: "{{ route('store-website.save.store.category') }}",
		type: 'POST',
		data:{category_id: catId, store: swId, check: check, category_name: catName, '_token': "{{ csrf_token()}}"},
		beforeSend :  function() {
			$("#loading-image").show();
		},
		success: function(data) {
			$("#loading-image").hide();
			if(data.message) {
				$('#alert-msg p').text(data.message);
				$('#alert-msg').show();
			}
		}
	})
}

$(document).on('click','.checkinglog',function(){
	var category_id = $(this).data('id');
	$.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('store-website.category,categoryHistory') }}",
        data: {
          category_id:category_id,
        },
    }).done(response => {
      $('#category-history-modal').find('.category-history-list-view').html('');
        if(response.success==true){
          $('#category-history-modal').find('.category-history-list-view').html(response.html);
          $('#category-history-modal').modal('show');
        }

    }).fail(function(response) {

      alert('Could not fetch payments');
    });
});

</script>

@endsection

