<script type="text/x-jsrender" id="template-task-history">
<!-- <form name="template-create-goal1" method="post"> -->
		<div class="modal-content tasks_list_tbl">
		   <div class="modal-header">
		      <h5 class="modal-title">Task</h5>
		      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		      <span aria-hidden="true">&times;</span>
		      </button>
		   </div>
		   <div class="modal-body">

           <div class="task_hours_section" style="text-align:center;">
                    <p style="margin:0px;"><strong>Pending Task Estimated Hours:</strong> <span>{{:userTiming.total_pending_hours}} Hours</span></><br>
                    <p style="margin:0px;"><strong>Total Available Hours:</strong>  <span>{{:userTiming.total_avaibility_hour}} Hours</span></p>
            </div>


           <table class="table table-bordered" style="table-layout:fixed;">
		    <thead>
		      <tr>
		      	<th style="width:25%">Task</th>
				<th style="width:25%">Description</th>
		      	<th style="width:25%">Approximate time</th>
				<th style="width:25%">Action</th> 
			</tr>
		    </thead>
		    <tbody>
				{{props taskList}}
			      <tr>
			      	<td>
					  {{if prop.type == 'TASK'}}
					  #TASK-{{:prop.task_id}} => {{:prop.subject}}
					  {{else}}
					  #DEVTASK-{{:prop.task_id}} => {{:prop.subject}}
					  {{/if}}

					 
					  </td>
					<td>
						<div class="show_hide_description">Show Description</div>
						<div class="description_content" style="display:none">
							{{:prop.details}}
						</div>
					</td>
			      	<td>
					  <div class="form-group">
							<div class='input-group estimate_minutes'>
								<input style="min-width: 30px;" placeholder="E.minutes" value="{{:prop.approximate_time}}" type="text" class="form-control estimate-time-change" name="estimate_minutes_{{:prop.task_id}}" data-id="{{:prop.task_id}}" id="estimate_minutes_{{:prop.task_id}}" data-type={{:prop.type}}>

								<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-time-history" title="Show History" data-id="{{:prop.task_id}}" data-type={{:prop.type}}><i class="fa fa-info-circle"></i></button>
							</div>
						</div>
                        <div class="d-flex">

                            <div class="form-group" style="padding-top:5px;">
                                <div class='input-group date due-datetime'>
									<input type="text" class="form-control input-sm due_date_cls" name="due_date" data-type={{:prop.type}} value="{{:prop.due_date}}"/>
								</div>
							</div>
                            <button class="btn btn-sm btn-image set-due-date" title="Set due date" data-taskid="{{:prop.task_id}}" style="padding:0px;"><img style="padding: 0;margin-top: -14px;" src="/images/filled-sent.png"/></button>
                        </div>
					  </td>
					  <td>
					  	<input type="text" class="form-control quick-message-field input-sm" name="message" placeholder="Message" value="">

                          <div class="d-flex" style="float:right;">
                          <button style="padding:2px;" class="btn btn-sm btn-image task-send-message-btn" data-type="{{:prop.type}}" data-id="{{:prop.task_id}}"><img src="/images/filled-sent.png"/></button>
                          {{if prop.type == 'TASK'}}
                          <button style="padding:2px;" type="button" class="btn btn-xs btn-image load-communication-modal" data-object="task" data-id="{{:prop.task_id}}" title="Load messages"><img src="/images/chat.png" alt="" style="cursor: nwse-resize;"></button>
					  {{else}}
					  <button type="button" class="btn btn-xs btn-image load-communication-modal" data-object="developer_task" data-id="{{:prop.task_id}}" title="Load messages"><img src="/images/chat.png" alt="" style="cursor: nwse-resize;"></button>
					  {{/if}}
                        
                                                 
                    </div>
					  	
					  </td>
				  </tr>
				  {{/props}}
		    </tbody>
		</table>
		   </div>
		   <div class="modal-footer">
		      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
		   </div>
		</div>
	<!-- </form> -->

</script>
<script>
	// $('.due-datetime').datetimepicker({
    //     format: 'YYYY-MM-DD HH:mm'
    // }); 

//     $('#time_from').datetimepicker({
//     format: 'YYYY-MM-DD HH:mm:ss'
// });


	$(document).on('click', '.task-send-message-btn', function () {
		var cached_suggestions = localStorage['message_suggestions'];
            var thiss = $(this);
            var data = new FormData();
            var task_id = $(this).data('id');
            var type = $(this).data('type');
            var message = $(this).siblings('input').val();
            data.append("task_id", task_id);
            data.append("message", message);
            data.append("status", 1);
            if(type == 'DEVTASK') {
                $.ajax({
                            url: "/whatsapp/sendMessage/issue",
                            type: 'POST',
                            data: {
                                "issue_id": task_id,
                                "message": message,
                                "status": 2
                            },
                            dataType: "json",
                            success: function (response) {
                                toastr["success"]("Message sent successfully!", "Message");
                                $(self).removeAttr('disabled');
                                $(self).val('');
                            },
                            beforeSend: function () {
                                $(self).attr('disabled', true);
                            },
                            error: function () {
                                alert('There was an error sending the message...');
                                $(self).removeAttr('disabled', true);
                            }
                        });
            }
            else {
                if (message.length > 0) {
                            if (!$(thiss).is(':disabled')) {
                                $.ajax({
                                    url: '/whatsapp/sendMessage/task',
                                    type: 'POST',
                                    "dataType": 'json',           // what to expect back from the PHP script, if anything
                                    "cache": false,
                                    "contentType": false,
                                    "processData": false,
                                    "data": data,
                                    beforeSend: function () {
                                        $(thiss).attr('disabled', true);
                                    }
                                }).done(function (response) {
                                    $(thiss).siblings('input').val('');

                                    if (cached_suggestions) {
                                        suggestions = JSON.parse(cached_suggestions);

                                        if (suggestions.length == 10) {
                                            suggestions.push(message);
                                            suggestions.splice(0, 1);
                                        } else {
                                            suggestions.push(message);
                                        }
                                        localStorage['message_suggestions'] = JSON.stringify(suggestions);
                                        cached_suggestions = localStorage['message_suggestions'];

                                        console.log('EXISTING');
                                        console.log(suggestions);
                                    } else {
                                        suggestions.push(message);
                                        localStorage['message_suggestions'] = JSON.stringify(suggestions);
                                        cached_suggestions = localStorage['message_suggestions'];

                                        console.log('NOT');
                                        console.log(suggestions);
                                    }

                                    $(thiss).attr('disabled', false);
                                }).fail(function (errObj) {
                                    $(thiss).attr('disabled', false);

                                    alert("Could not send message");
                                    console.log(errObj);
                                });
                            }
                        } else {
                            alert('Please enter a message first');
                        }
            }
        });
	</script>
