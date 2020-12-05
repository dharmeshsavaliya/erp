<script type="text/x-jsrender" id="template-result-block">
	<div class="row page-template-{{:page}}">
		<table class="table table-bordered" style="table-layout:fixed;">
		    <thead>
		      <tr>
		      	<th style="width:5%"><input type="checkbox" class="select-all-records"></th>
		        <th style="width:7%">Customer</th>
		        <th style="width:7%">Product</th>
				<th style="width:9%">Website</th>
		        <th style="width:5%">Type</th>
		        <th style="width:5%">Refund amount</th>
		        <th style="width:9%">Reason for refund</th>
		        <th style="width:6%">Status</th>
				<th style="width:6%">Change Status</th>
		        <th style="width:8%">Pickup Address</th>
		        <th style="width:10%">Refund details</th>
		        <th style="width:10%">Est Refund / Exchange date</th>
		        <th style="width:5%">Remarks</th>
		        <th style="width:4%">Created At</th>
		        <th width="10%" align="center">Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
				<tr>
			      	<td><input class="select-id-input" type="checkbox" name="ids[]" value="{{:prop.id}}">&nbsp;{{:prop.id}}</td>
			      	<td class="expand-row-msg" data-name="customer" data-id="{{:prop.id}}">
					  <span class="show-short-customer-{{:prop.id}}">{{:~trimlength(prop.customer_name, 10)}}</span>
					  <span class="show-full-customer-{{:prop.id}} hidden">{{:prop.customer_name}}</span>
					</td>
			      	<td class="expand-row-msg" data-name="product" data-id="{{:prop.id}}">
					  <span class="show-short-product-{{:prop.id}}">{{:~trimlength(prop.name, 4)}}</span>
					  <span class="show-full-product-{{:prop.id}} hidden">{{:prop.name}}</span>
					</td>
					<td class="expand-row-msg" data-name="website" data-id="{{:prop.id}}">
						<span class="show-short-website-{{:prop.id}}">{{:~trimlength(prop.website, 10)}}</span>
					   <span class="show-full-website-{{:prop.id}} hidden">{{:prop.website}}</span>
					</td>
			        <td>{{:prop.type}}</td>
			        <td>{{:prop.refund_amount}}</td>

					<td class="expand-row-msg" data-name="reason" data-id="{{:prop.id}}">
						<span class="show-short-reason-{{:prop.id}}">{{:~trimlength(prop.reason_for_refund, 10)}}</span>
					   <span class="show-full-reason-{{:prop.id}} hidden"><span style="word-break:break-all;">{{:prop.reason_for_refund}}</span></span>
					</td>

					<td class="expand-row-msg" data-name="statusName" data-id="{{:prop.id}}">
						<span class="show-short-statusName-{{:prop.id}}">{{:~trimlength(prop.status_name, 7)}}</span>
					   <span class="show-full-statusName-{{:prop.id}} hidden"><span style="word-break:break-all;">{{:prop.status_name}}</span></span>
					</td>
					<td>to be added</td>
					<td class="expand-row-msg" data-name="pickupAdd" data-id="{{:prop.id}}">
						<span class="show-short-pickupAdd-{{:prop.id}}">{{:~trimlength(prop.pickup_address, 10)}}</span>
					   <span class="show-full-pickupAdd-{{:prop.id}} hidden"><span style="word-break:break-all;">{{:prop.pickup_address}}</span></span>
					</td>
			        <td class="expand-row" data-id="{{>prop.id}}">
					<div class="td-mini-container-{{>prop.id}}">
						<p>DOR : {{:prop.date_of_request_formated}}</p>
					</div>
					<div class="td-full-container-{{>prop.id}} hidden">
						<p>DOR : {{:prop.date_of_request_formated}}</p>
						<p>DOI : {{:prop.date_of_issue_formated}}</p>
						<p>DOD: {{:prop.dispatch_date_formated}}</p>
						<p>Credited: {{if prop.credited}} Yes {{else}} No {{/if}}</p>
					</div>
					</td>
					<td style="padding:1px;">
					<div class="form-group" style="margin-bottom:0px;">
					<div class="d-flex">
						<div class='input-group estimate_dates'>
							<input style="min-width: 30px;" placeholder="E.Date" value="{{>prop.est_completion_date}}" type="text" class="form-control estimate-date" name="estimate_date_{{>prop.id}}" data-id="{{>prop.id}}" id="estimate_date_{{>prop.id}}">
							
						</div>
						<button style="padding: 0px;" class="btn btn-sm btn-image estimate-date-submit" data-id="{{>prop.id}}"><img src="images/filled-sent.png" style="cursor: nwse-resize;"></button>
						<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-date-history" title="Show Date History" data-id="{{>prop.id}}"><i class="fa fa-info-circle"></i></button>
					</div>
					</div>
					</td>
					<td class="expand-row-msg" data-name="remarks" data-id="{{:prop.id}}">
						<span class="show-short-remarks-{{:prop.id}}">{{:~trimlength(prop.remarks, 5)}}</span>
					   <span class="show-full-remarks-{{:prop.id}} hidden">{{:prop.remarks}}</span>
					</td>
              		<td>{{:prop.created_at_formated}}</td>
			        <td class="action" align="center">
						<div class="cls_action_btn">
			        	<button type="button" class="btn btn-delete-template no_pd" onClick='return confirm("Are you sure you want to delete this request ?")' data-id="{{>prop.id}}"><img width="15px" src="/images/delete.png"></button>
			        	<button type="button" class="btn btn-edit-template no_pd" data-id="{{>prop.id}}"><img width="15px" src="/images/edit.png"></button>
			        	<button type="button" class="btn btn-history-template no_pd" data-id="{{>prop.id}}" ><img width="15px" src="/images/list-128x128.png"></button>
						<button type="button" class="btn send-email-to-customer no_pd" data-id="{{>prop.customer_id}}"><i class="fa fa-envelope-square"></i></button>
						{{if prop.product_id}}
						<button type="button" class="btn show-product no_pd" data-id="{{>prop.product_id}}"><i class="fa fa-product-hunt"></i></button>
						{{/if}}
            			<button type="button" data-id="{{>prop.product_id}}" class="btn btn-product-info-template no_pd"><img width="15px" src="/images/view.png"></button>
						{{if !prop.credited}}
						<button type="button" data-id="{{>prop.id}}" class="btn create-update-refund no_pd" title="Create or update refund"><i class="fa fa-exchange"></i></button>
						{{/if}}
            			<button type="button" class="btn resend-confirmation-email" data-id="{{>prop.customer_id}}">
            				<i class="fa fa-paper-plane" aria-hidden="true"></i>
            			</button>
						</div>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>
<script type="text/x-jsrender" id="template-history-block">
	<div class="modal-content">
		<div class="modal-body">
			<div class="col-md-12">
				<table class="table table-bordered">
				    <thead>
				      <tr>
				      	<th>Id</th>
				        <th>Status</th>
				        <th>Comment</th>
				        <th>Updated By</th>
				        <th>Created at</th>
				      </tr>
				    </thead>
				    <tbody>
				    	{{props data}}
					      <tr>
					      	<td>{{:prop.id}}</td>
					      	<td>{{:prop.status}}</td>
					      	<td>{{:prop.comment}}</td>
					      	<td>{{:prop.user_name}}</td>
					      	<td>{{:prop.created_at}}</td>
					      </tr>
					    {{/props}}  
				    </tbody>
				</table>
			</div>
		</div> 		
	</div>
</script>

<script type="text/x-jsrender" id="date-history-block">
	<div class="modal-content">
		<div class="modal-body">
			<div class="col-md-12">
				<table class="table table-bordered">
				    <thead>
				      <tr>
				      	<th>Id</th>
				        <th>Old value</th>
				        <th>New value</th>
				        <th>Updated By</th>
				        <th>Created at</th>
				      </tr>
				    </thead>
				    <tbody>
				    	{{props data}}
					      <tr>
					      	<td>{{:prop.id}}</td>
					      	<td>{{:prop.old_value}}</td>
					      	<td>{{:prop.new_value}}</td>
					      	<td>{{:prop.user_name}}</td>
					      	<td>{{:prop.created_at}}</td>
					      </tr>
					    {{/props}}  
				    </tbody>
				</table>
			</div>
		</div> 		
	</div>
</script>


<script type="text/x-jsrender" id="template-edit-block">
	<div class="modal-content">
	<div class="modal-body">
	    <form action="/return-exchange/{{:data.return_exchange.id}}/update" method="POST" enctype="multipart/form-data" class="" id="return-exchange-update-form" data-reload='1'>
		    <?php echo csrf_field(); ?>
		    <div class="row">
		        <div class="col">
		            <div class="form-group">
		                <strong>Type&nbsp;:&nbsp;</strong>
		                <span><input type="radio" {{if data.return_exchange.type == "refund"}} checked="checked" {{/if}} name="type" value="refund" />Refund</span>
		                <span><input type="radio" {{if data.return_exchange.type == "exchange"}} checked="checked" {{/if}} name="type" value="exchange" />Exchange</span>
		            </div>
		        </div>
		    </div>
		    <div class="row refund-section" style="display: none">
		        <div class="col">
		            <div class="form-group">
		                <strong>Reason for refund&nbsp;:&nbsp;</strong>
		                <input type="text" class="form-control" value="{{:data.return_exchange.reason_for_refund}}" name="reason_for_refund"></textarea>
		            </div>
		        </div>
		        <div class="col">
		            <div class="form-group">
		                <strong>Refund Amount&nbsp;:&nbsp;</strong>
		                <input type="text" class="form-control" value="{{:data.return_exchange.refund_amount}}" name="refund_amount"></textarea>
		            </div>
		        </div>
		    </div>

		    <div class="row">
		        <div class="col">
		            <div class="form-group">
		                <strong>Status&nbsp;:&nbsp;</strong>
		                <select name="status" class="form-control select-multiple" style="width: 100%;">
		                    {{props data.status ~selectedStatus=data.return_exchange.status}}
		                        <option {{if selectedStatus == key}} selected="selected" {{/if}} value="{{>key}}">{{>prop}}</option>
		                    {{/props}}
		                </select>
		            </div>
		        </div>
		    </div>

		    <div class="row">
		        <div class="col">
		            <div class="form-group">
		                <strong>Pickup Address&nbsp;:&nbsp;</strong>
		                <textarea class="form-control" name="pickup_address">{{:data.return_exchange.pickup_address}}</textarea>
		            </div>
		        </div>
		    </div>

		    <div class="row">
		        <div class="col">
		            <div class="form-group">
		                <strong>Remarks&nbsp;:&nbsp;</strong>
		                <textarea class="form-control" name="remarks">{{:data.return_exchange.remarks}}</textarea>
		            </div>
		        </div>
		    </div>
		    <div class="row">
		        <div class="col">
		            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		            <button type="submit" class="btn btn-secondary" id="btn-return-exchage-request">Submit</button>
		      	</div>
		    </div>
		</form>
	</div> 		
</div>
</script>
<script type="text/x-jsrender" id="template-product-block">
	<div class="modal-content">
		<div class="modal-body">
			<div class="col-md-12">
				<table class="table table-bordered">
				    <thead>
				      <tr>
						<th>Order number</th>
						<th>Brand</th>
						<th>Product Name</th>
						<th>Image</th>
						<th>Price</th>
				      </tr>
				    </thead>
				    <tbody>
				    	{{props data}}
					      <tr>
							<td>{{:prop.order_number}}</td>
							<td>{{:prop.product_brand}}</td>
							<td>{{:prop.product_name}}</td>
							<td><img width="30px" src="{{:prop.product_image}}"></td>
							<td>{{:prop.product_price}}</td>
					      </tr>
					    {{/props}}  
				    </tbody>
				</table>
			</div>
		</div> 		
	</div>
</script>