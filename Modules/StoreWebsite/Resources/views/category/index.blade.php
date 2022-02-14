@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', $title)

@section('content')
<style type="text/css">
	.preview-category input.form-control {
	  width: auto;
	}
	#page-view-result{
    overflow-x: auto;
	}
</style>

<div class="row" id="common-page-layout">
	<div class="col-lg-12 margin-tb">
        <h2 class="page-heading">{{$title}} <span class="count-text"></span></h2>
    </div>
    <br>
    <div class="col-lg-12 margin-tb">
    	<div class="row">
	    	<div class="col col-md-3">
		    	<div class="row">
	    			{{-- <button style="display: inline-block;width: 10%" class="btn btn-sm btn-image btn-add-action">
		  				<img src="/images/add.png" style="cursor: default;">
		  			</button> --}}
				 </div> 		
		    </div>
		    <div class="col col-md-9">
		    	<div class="h" style="margin-bottom:10px;">
					<div class="row">
		    			<form class="form-inline message-search-handler" method="get">
					  		<div class="col">
							  	<div class="form-group">
								   <label for="keyword">Categories:</label>
								   <select name="category_id" class="form-control">
								   	@foreach($allcategories as $value)
								   	<option value="{{ $value->id }}" <?php echo (isset($_GET['category_id'])&&($_GET['category_id']==$value->id)) ?'selected' : ""; ?>  >{{ $value->title }}</option>
								   	@endforeach
								   </select> 	
							  	</div>
							  	<div class="form-group">
								   <label for="keyword">Websites:</label>
								   <select name="website_id" class="form-control">
								   	@foreach($allstoreWebsite as $value)
								   	<option value="{{ $value->id }}"  <?php echo (isset($_GET['website_id'])&&($_GET['website_id']==$value->id)) ?'selected' : ""; ?>>{{ $value->website }}</option>
								   	@endforeach
								   </select> 
							  	</div>
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
				        <th>Action</th>
				      </tr>
				    </thead>
				    <tbody>
				    	<?php foreach($categories as $category) { ?>
 					      <tr class="category_tr<?php echo $category->id; ?>">
					      	<td><?php echo $category->id; ?></td>
					      	<td><?php echo $category->title; ?> <span href="javascript:void(0);" class="checkinglog" data-id="{{ $category->id }}" ><i class="fa fa-history"></i></span> </td>
					      	<?php foreach($storeWebsite as $sw) { 
					      			$checked = ""; 
					      			$catName = ""; 
					      			$remote_id = ""; 
								  ?>
								  @forelse ($appliedQ as $item)
									  	@if($item->category_id == $category->id && $item->store_website_id == $sw->id)
										  	@php $checked = "checked"; $catName = $item->category_name; @endphp
									  		@php $remote_id = $item->remote_id  @endphp
									  	@endif
								  @empty
								  @endforelse
					        	<td>
									<input data-category="{{ $category->id }}" data-sw="{{ $sw->id }}" <?php echo $checked; ?> class="push-category" type="checkbox" name="category_website"> {{ $remote_id }}

									<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-date-history" title="Show Date History" data-category="{{ $category->id }}" data-id="{{ $sw->id }}"><i class="fa fa-info-circle"></i></button>


									<input data-category="{{ $category->id }}" data-id="{{ $sw->id }}" class="rename-category" type="text" name="category_name" value="{{ $catName }}">
								</td>
					        <?php } ?>
					        <td>

                    	<button type="submit" category-id="{{ $category->id }}" class="btn btn-image delete-category  pd-5 btn-ht"><img title="Delete Category" src="{{asset('images/delete.png')}}" /></button>
                  </td>
					      </tr>
					    <?php } ?>
				    </tbody>
				</table>
				{!! $categories->links() !!}
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
			                	<th>Date / Time</th>
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

<div id="swc-user-history-modal" class="modal fade" role="dialog">
  	<div class="modal-dialog modal-lg">
  		
    	<div class="modal-content">
    		<div class="modal-header">
            <h5 class="modal-title">Store Website Category User History</h5>
      </div>
	      	<div class="modal-body">
	        	<div class="col-md-12">
	          		<table class="table table-bordered">
	            		<thead>
			              	<tr>
			                	<th>Date / Time</th>
			                	<th>Old Value</th>
			                	<th>New Value</th>
			                	<th>Updated by</th>
			              	</tr>
			            </thead>
	            		<tbody class="swc-user-history-list-view">
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
$(document).on('click','.show-date-history',function(){
	var store_id = $(this).data('id');
	var category_id = $(this).data('category');
	$.ajax({
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
        },
        url: "{{ route('store-website.category,webiteCategoryUserHistory') }}",
        data: {
          store_id:store_id,
          category_id:category_id,
        },
    }).done(response => {
      $('#swc-user-history-modal').find('.swc-user-history-list-view').html('');
        if(response.success==true){
          $('#swc-user-history-modal').find('.swc-user-history-list-view').html(response.html);
          $('#swc-user-history-modal').modal('show');
        }

    }).fail(function(response) {

      alert('Could not fetch payments');
    });
});

$(document).on('click','.delete-category',function()
{
	if(confirm("Are you sure you want to delete this?"))
	{    
		var category_id = $(this).attr('category-id');
		$.ajax({
	      type: 'POST',
	      headers: {
	         'X-CSRF-TOKEN': jQuery('meta[name="csrf-token"]').attr('content')
	      },
	      url: "{{ route('store-website.delete-category') }}",
	      data: {
	         category_id:category_id,
	      },
	   }).done(response => 
	   {
      	if(response.code == 200)
      	{
      		setInterval(function() 
   			{
   				window.location.reload();
   			}, 5000);
      		$('#alert-msg p').text(response.msg);
				$('#alert-msg').show();
				$("#category_tr"+category_id).remove();
      	}
    	}).fail(function(response) 
    	{
      	alert('Could not fetch category');
    	});
   }
   else
   {
      return false;
   } 
});
</script>

@endsection

