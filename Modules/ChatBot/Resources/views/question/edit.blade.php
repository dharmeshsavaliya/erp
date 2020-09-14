@extends('layouts.app')
@section('favicon' , 'task.png')

@section('title', 'Intent | Chatbot')

@section('content')
<style type="text/css">
	.border-div {
		border-style: dashed;
	    margin: 2px;
	    text-align: center;
	    float: left;
	    border-width: 3px;
	    cursor: pointer;
	}

	.dashed-question.selected {
		border-color: coral
	}
</style>
<link rel="stylesheet" href="/css/bootstrap-datetimepicker.min.css">
<div class="row">
	<div class="col-lg-12 margin-tb">
	    <h2 class="page-heading">Edit {{ $chatbotQuestion->value }} | Chatbot</h2>
	</div>
</div>
<div class="tab-pane">
		<div class="row">
			<div class="col-lg-12 margin-tb">
			@if ($errors->any())
			    <div class="alert alert-danger">
			        <ul>
			            @foreach ($errors->all() as $error)
			                <li>{{ $error }}</li>
			            @endforeach
			        </ul>
			    </div>
			@endif
		</div>
	    <div class="col-lg-12 margin-tb">
	    	<div class="well">
	    		<form action="{{ route('chatbot.question.update',[$chatbotQuestion->id]) }}" method="post">
    				  <?php echo csrf_field(); ?>
					  @if($chatbotQuestion->keyword_or_question == 'intent')
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="value">Intent</label>
					      <small id="emailHelp" class="form-text text-muted">Name your intent to match the category of values that it will detect.</small>
					      <?php echo Form::text("value", $chatbotQuestion->value, ["class" => "form-control", "id" => "value", "placeholder" => "Enter your value"]); ?>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="question">Category</label>
					      <!-- <?php echo Form::select("category_id",$allCategoryList, null, ["class" => "form-control select-chatbot-category", "id" => "chatbot-category-id"]); ?> -->
						  <select name="category_id" id="" class="form-control question-category" data-id="{{$chatbotQuestion->id}}">
						  <option value="">Select</option>
						  @foreach($allCategoryList as $cat)
						  	<option {{$cat['id'] == $chatbotQuestion->category_id ? 'selected' : ''}} value="{{$cat['id']}}">{{$cat['text']}}</option>
						  @endforeach
						</select>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="question">User Intent</label>
					      <?php echo Form::text("question", null, ["class" => "form-control", "id" => "question", "placeholder" => "Enter your question"]); ?>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="value">Suggested Reply</label>
					      <?php echo Form::text("suggested_reply", $chatbotQuestion->suggested_reply, ["class" => "form-control", "id" => "suggested_reply", "placeholder" => "Enter your reply"]); ?>
					    </div>
					  </div>
					  @endif
					  @if($chatbotQuestion->keyword_or_question == 'entity')
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="keyword">Entity</label>
					      <small id="emailHelp" class="form-text text-muted">Name your entity to match the category of values that it will detect.</small>
						  <?php echo Form::text("value", $chatbotQuestion->value, ["class" => "form-control", "id" => "keyword", "placeholder" => "Enter your entity"]); ?>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="question">Category</label>
					      <!-- <?php echo Form::select("category_id",$allCategoryList, null, ["class" => "form-control select-chatbot-category", "id" => "chatbot-category-id"]); ?> -->
						  <select name="category_id" id="" class="form-control question-category" data-id="{{$chatbotQuestion->id}}">
						  <option value="">Select</option>
						  @foreach($allCategoryList as $cat)
						  	<option {{$cat['id'] == $chatbotQuestion->category_id ? 'selected' : ''}} value="{{$cat['id']}}">{{$cat['text']}}</option>
						  @endforeach
						</select>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="value">User Entity</label>
					      <?php echo Form::text("question", null, ["class" => "form-control", "id" => "value", "placeholder" => "Enter your value"]); ?>
					    </div>
					</div>
					<div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="value">Suggested Reply</label>
					      <?php echo Form::text("suggested_reply", $chatbotQuestion->suggested_reply, ["class" => "form-control", "id" => "suggested_reply", "placeholder" => "Enter your reply"]); ?>
					    </div>
					  </div>
					<div class="form-row align-items-end">
					    <div class="form-group col-md-2">
						    <label for="type">Type</label>
						    <?php echo Form::select("types",["synonyms" => "synonyms", "patterns" => "patterns"] ,null, ["class" => "form-control", "id" => "types"]); ?>
					    </div>
						<div class="form-group col-md-2">
							<div class="row align-items-end" id="typeValue_1">
								<div class="col-md-9">
									<?php echo Form::text("type[]", null, ["class" => "form-control", "id" => "type", "placeholder" => "Enter value", "maxLength"=> 64]); ?>
								</div>
							</div>
						</div>
						<div class="form-group col-md-2" id="add-type-value-btn">
				  	        <a href="javascript:;" class="btn btn-secondary btn-sm add-more-condition-btn">
			                    <span class="glyphicon glyphicon-plus"></span> 
			                </a>	
			      	    </div>
					</div>
					  @endif
					  @if($chatbotQuestion->keyword_or_question == 'simple' || $chatbotQuestion->keyword_or_question == 'priority-customer')
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="value">ERP Entity</label>
					      <small id="emailHelp" class="form-text text-muted">Name your erp entity to match the category of values that it will detect.</small>
					      <?php echo Form::text("value", $chatbotQuestion->value, ["class" => "form-control", "id" => "value", "placeholder" => "Enter your value"]); ?>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="question">Category</label>
						  <select name="category_id" id="" class="form-control question-category" data-id="{{$chatbotQuestion->id}}">
						  <option value="">Select</option>
						  @foreach($allCategoryList as $cat)
						  	<option {{$cat['id'] == $chatbotQuestion->category_id ? 'selected' : ''}} value="{{$cat['id']}}">{{$cat['text']}}</option>
						  @endforeach
						</select>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="question">Keyword</label>
					      <?php echo Form::text("keyword", null, ["class" => "form-control", "id" => "question", "placeholder" => "Enter your keyword"]); ?>
					    </div>
					  </div>
					  <div class="form-row">
					    <div class="form-group col-md-6">
					      <label for="value">Suggested Reply</label>
					      <?php echo Form::text("suggested_reply", $chatbotQuestion->suggested_reply, ["class" => "form-control", "id" => "suggested_reply", "placeholder" => "Enter your reply"]); ?>
					    </div>
					  </div>
					  <div class="form-row">
						<div class="form-group col-md-6">
							<strong>Repeat:</strong>
							<select class="form-control" name="repeat">
								<option value="">Don't Repeat</option>
								<option value="Every Day" {{$chatbotQuestion->repeat == 'Every Day' ? 'selected'  : ''}}>Every Day</option>
								<option value="Every Week" {{$chatbotQuestion->repeat == 'Every Week' ? 'selected'  : ''}}>Every Week</option>
								<option value="Every Month" {{$chatbotQuestion->repeat == 'Every Month' ? 'selected'  : ''}}>Every Month</option>
								<option value="Every Year" {{$chatbotQuestion->repeat == 'Every Year' ? 'selected'  : ''}}>Every Year</option>
							</select>
						</div>
					  </div>

					  <div class="form-row">
					  <div class="form-group col-md-6">
                        <strong>Completion Date:</strong>
                        <div class='input-group date' id='sending-datetime'>
								<input type='text' class="form-control" name="sending_time" value="{{ $chatbotQuestion->sending_time }}"/>

								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>

							@if ($errors->has('sending_time'))
								<div class="alert alert-danger">{{$errors->first('sending_time')}}</div>
							@endif
						</div>
					  </div>
					  @endif
					  <button type="submit" class="btn btn-primary">Add Intent</button>
				</form>
	    	</div>
		</div>
		<div class="col-lg-12 margin-tb">
		@if($chatbotQuestion->keyword_or_question == 'intent')
			<table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
			  <thead>
			    <tr>
			      <th class="th-sm">Id</th>
			      <th class="th-sm">User Intent</th>
			      <th class="th-sm">Annotation</th>
			      <th class="th-sm">Action</th>
			    </tr>
			  </thead>
			  <tbody>
			    <?php foreach ($chatbotQuestion->chatbotQuestionExamples as $value) {?>
				    <tr>
				      <td><?php echo $value->id; ?></td>
				      <td class="text-question" data-question-id="<?php echo $value->id; ?>" data-question="<?php echo $value->question; ?>">
				      	<?php echo $value->question; ?>
				     </td>
				     <td>
				     	<?php foreach ($value->highLightQuestion() as $key => $valueRaw) { ?>
				     			<div class="delete-annotation-raw" style="display: inline-block;padding-left: 2px;">
				     				<?php echo $valueRaw; ?>
					     			<span data-id="<?php echo $key; ?>" class="close delete-annotation" aria-label="Close"><span aria-hidden="true">&times;</span></span>
					     		</div>
				     	<?php } ?>
				     </td>	
				      <td>
                        <a class="btn btn-image delete-button" data-id="<?php echo $value->id; ?>" href="<?php echo route("chatbot.question-example.delete", [$chatbotQuestion->id, $value->id]); ?>">
                        	<img src="/images/delete.png">
                        </a>
                        <a class="btn btn-image annotation-button">
                        	<img src="/images/starred.png">
                        </a>
				      </td>
				    </tr>
				<?php }?>
			  </tbody>
			  <tfoot>
			    <tr>
			      <th>Id</th>
			      <th>User Example</th>
			      <th>Annotation</th>
			      <th>Action</th>
			    </tr>
			  </tfoot>
			</table>
			@endif
			@if($chatbotQuestion->keyword_or_question == 'entity')
			<table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
				  <thead>
				    <tr>
				      <th class="th-sm">Id</th>
				      <th class="th-sm">Value</th>
				      <th class="th-sm">Type</th>
				      <th class="th-sm">Extra Values</th>
				      <th class="th-sm">Action</th>
				    </tr>
				  </thead>
				  <tbody>
				    <?php foreach ($chatbotQuestion->chatbotQuestionExamples as $value) {?>
					    <tr>
					      <td><?php echo $value->id; ?></td>
					      <td><?php echo $value->question; ?></td>
					      <td><?php echo $value->types; ?></td>
					      <td><?php 
					      	$insertKeywords = [];
					      	if(!$value->chatbotKeywordValueTypes->isEmpty()) {
					      		foreach($value->chatbotKeywordValueTypes as $chWordVal) {
					      			$insertKeywords[] = $chWordVal->type;
					      		}
					      	}
					      	echo implode(",", $insertKeywords);
				       		?>
				       	 </td>	
					      <td>
	                        <a class="btn btn-image delete-button" data-id="<?php echo $value->id; ?>" href="<?php echo route("chatbot.question-example.delete", [$chatbotQuestion->id, $value->id]); ?>">
	                        	<img src="/images/delete.png">
	                        </a>
					      </td>
					    </tr>
					<?php }?>
				  </tbody>
				  <tfoot>
				    <tr>
				      <th>Id</th>
				      <th>Value</th>
				      <th>Type</th>
				      <th>Extra Values</th>
				      <th>Action</th>
				    </tr>
				  </tfoot>
				</table>
				@endif
				@if($chatbotQuestion->keyword_or_question == 'simple' || $chatbotQuestion->keyword_or_question == 'priority-customer')
				<table id="dtBasicExample" class="table table-striped table-bordered table-sm" cellspacing="0" width="100%">
				  <thead>
				    <tr>
				      <th class="th-sm">Id</th>
				      <th class="th-sm">Value</th>
				      <th class="th-sm">Action</th>
				    </tr>
				  </thead>
				  <tbody>
				    <?php foreach ($chatbotQuestion->chatbotQuestionExamples as $value) {?>
					    <tr>
					      <td><?php echo $value->id; ?></td>
					      <td><?php echo $value->question; ?></td>	
					      <td>
	                        <a class="btn btn-image delete-button" data-id="<?php echo $value->id; ?>" href="<?php echo route("chatbot.question-example.delete", [$chatbotQuestion->id, $value->id]); ?>">
	                        	<img src="/images/delete.png">
	                        </a>
					      </td>
					    </tr>
					<?php }?>
				  </tbody>
				  <tfoot>
				    <tr>
				      <th>Id</th>
				      <th>Value</th>
				      <th>Action</th>
				    </tr>
				  </tfoot>
				</table>

				@endif
		</div>
	</div>
</div>

@include('chatbot::partial.create_question_annotation')
<script src="/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript">

 $('#sending-datetime, #edit-sending-datetime').datetimepicker({
            format: 'YYYY-MM-DD HH:mm'
});
	var searchForKeyword = function(ele) {
    	var keywordBox = ele.find(".search-keyword");
	    if (keywordBox.length > 0) {
	        keywordBox.select2({
	            placeholder: "Enter entity name or create new one",
	            width: "100%",
	            tags: true,
	            allowClear: true,
	            ajax: {
	                url: '/chatbot/question/keyword/search',
	                dataType: 'json',
	                processResults: function(data) {
	                    return {
	                        results: data.items
	                    };
	                }
	            }
	        });
	    }
	};
	
	var annotationButton = $(".annotation-button");
		annotationButton.on("click",function() {
			var text =  $(this).closest("tr").find(".text-question");
			var words = text.data("question").split(" ");
			var html = "";
			var char = 0; 
			var start = 0; 
			$.each(words,function(k,v) {
				char += v.length;
				var ln = (k == 0) ? 0 : char;

				if(k != 0) {
					start = ln - v.length;
				}

				html += "<div class='border-div dashed-question' data-start='"+start+"' data-end='"+ln+"'>"+v+"</div>";
				char += 1;
			})
			text.html(html);
		});

		$(document).on("click",".dashed-question",function() {
			var $this = $(this);
			var question = $this.closest(".text-question");
				if($this.hasClass("selected")) {
					$this.removeClass("selected")
				}else{
					$this.addClass("selected");
				}
				if(question.find(".selected").length > 1) {
					$.each($this.prevAll(),function(k,v){
						if($(v).hasClass("selected")) {
							return false;
						}else{
							$(v).addClass("selected");
						}
					})
					
					searchForKeyword($("#create-question-annotation"));

					var startRange = question.find(".selected").first().data("start");
					var endRange   = question.find(".selected").last().data("end");

					var keywordValue = question.data("question").substring(startRange, endRange);
					
					var modelBox = $("#create-question-annotation");
						modelBox.find("#question-example-id").val(question.data("question-id"));
						modelBox.find("#start-char-range").val(startRange);
						modelBox.find("#end-char-range").val(endRange);
						modelBox.find("#keyword-value").val(keywordValue);
						modelBox.modal("show");
				}
				$this.nextAll().removeClass("selected");
		});

		$("#create-question-annotation").on("click",".form-save-btn",function() {
			var form = $(this).closest("form");
			$.ajax({
				type: form.attr("method"),
	            url: form.attr("action"),
	            data: form.serialize(),
	            dataType : "json",
	            success: function (response) {
	               if(response.code == 200) {
	               	  toastr['success']('data updated successfully!');
	               	  location.reload();
	               }else{
	               	  toastr['error']('data is not correct or duplicate!');
	               } 
	            },
	            error: function () {
	               toastr['error']('Could not change module!');
	            }
	        });
		});

		$(document).on("click",".delete-annotation",function() {
			var $this = $(this);
			var anntid = $this.data("id");
			$.ajax({
				type: "GET",
	            url: "/chatbot/question/annotation/delete",
	            data: {id : anntid},
	            dataType : "json",
	            success: function (response) {
	               if(response.code == 200) {
	               	  toastr['success']('data updated successfully!');
	               	  $this.closest(".delete-annotation-raw").remove();
	               }else{
	               	  toastr['error']('Oops, something went wrong!');
	               } 
	            },
	            error: function () {
	               toastr['error']('Could not change module!');
	            }
	        });
		});

		$(".select-chatbot-category").select2({
            placeholder: "Enter category name or existing",
            width: "100%",
            tags: true,
            allowClear: true,
            ajax: {
                url: '/chatbot/question/search-category',
                dataType: 'json',
                processResults: function(data) {
                    return {
                        results: data.items
                    };
                }
            }
        });
    var idValue=1;
	$(".add-more-condition-btn").on("click", function(e){
		idValue++;
		var removeBtnId = '#typeValue_'+(idValue-1);
		var selectedType = $(this).closest("form").find("select[name = 'types']").val();
		if ( selectedType == "synonyms" || idValue<=5 ){
			$(removeBtnId).append('<input type="button" value="-" class="btn btn-secondary" onclick="remove(this)"/>');
		    $("<div class='form-group col-md-2' ><div class='row align-items-end' id='typeValue_"+idValue+"' ><div class='col-md-9'><label for='type'>&nbsp</label><input type='text' name='type[]' class='form-control' placeholder='Enter value' maxLength = 64/><div/></div></div>").insertBefore('#add-type-value-btn')
		} else {
			alert("maximum pattern value limit reached : 5")
			idValue--;
		}
	});
	$("#types").on("change", function(e) {
		var typeValueCount = $(this).closest("form").find("input[name = 'type[]']").length;
		if(e.target.value == 'patterns' && typeValueCount>5) {
			alert('You are changing a synonym value to a pattern value. You currently have '+ typeValueCount+ ' synonyms associated with this value, but patterns may only have 5');
			$(this).closest("form").find("select[name = 'types']").val('synonyms').change()
			e.preventDefault();
		}
	});
	function remove(ele) {
		$(ele).parents('div.col-md-2').remove()
	}
</script>

@endsection