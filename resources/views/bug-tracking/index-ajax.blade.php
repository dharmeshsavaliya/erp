
<?php foreach($data as $prop) {
	
	


	?>
			      <tr>
			      	<td class='break'><?php echo $prop->id; ?></td>
			      	<td><?php echo  $prop->created_at_date;  ?></td>
			        <td class='break expand-row-msg' data-name="summary" id="copy" data-id="<?php echo  $prop->id;  ?>"><span class="show-short-summary-<?php echo $prop->id; ?>" onclick="copySumText()"><?php echo  $prop->summary_short  ?></span>
                        <span class="show-full-summary-<?php echo  $prop->id  ?> hidden" ><?php echo  $prop->summary;  ?></span>
                    </td>
			        <td class='break'  data-bug_type="<?php echo  $prop->bug_type_id_val  ?>"><?php echo  $prop->bug_type_id;  ?></td>
			        <td class='break expand-row-msg' data-name="step_to_reproduce" data-id="<?php echo  $prop->id;  ?>" data-toggle="tooltip"> <span class="show-short-Steps to reproduce-<?php echo  $prop->id;  ?>"><?php echo  $prop->step_to_reproduce_short  ?></span>
                        <span class="show-full-step_to_reproduce-<?php echo  $prop->id;  ?> hidden" ><?php echo  $prop->step_to_reproduce;  ?></span>
                    </td>
			        <td class='break'><?php echo  $prop->bug_environment_id;  ?> <?php echo  $prop->bug_environment_ver  ?></td>
			        <td class='break'><?php echo  $prop->expected_result;  ?></td>

			        <td class='break expand-row-msg' data-name="url" data-id="<?php echo  $prop->id;  ?>">
			            <a href="<?php echo  $prop->url  ?>" target="_blank">
			                <span href="" class="show-short-url-<?php echo  $prop->id  ?>"><?php echo  $prop->url_short;  ?></span>
                            <span href="" class="show-full-url-<?php echo  $prop->id  ?> hidden" ><?php echo  $prop->url;  ?></span>
                        </a>

                        <button type="button"  class="btn btn-copy-url btn-sm" data-id="<?php echo  $prop->url;  ?>" >
                            <i class="fa fa-clone" aria-hidden="true"></i></button>
                     </td>
                     <td class='break'><?php echo  $prop->created_by;  ?></td>

			        <td class='break'>
						<div class="d-flex">
							<select class='form-control assign_to'  data-id="<?php echo  $prop->id;  ?>" style="padding:0px;" data-token=<?php echo csrf_token(); ?> >
								<?php
									foreach ($users as $user) { ?>
									 <option <?php if ($prop->assign_to == $user->id) { echo " selected"; }  ?>   value="<?php echo $user->id; ?>"><?php echo $user->name; ?></option>
									
									<?php
									}
							?>
							</select>
							<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-user-history" title="Show History" data-id="<?php echo  $prop->id ; ?>"><i class="fa fa-info-circle"></i></button>
						</div>
			        </td>
			        <td class='break'>
						<div class="d-flex">
						
						   <select class='form-control bug_severity_id' id="bug_severity_id_<?php echo  $prop->id  ?>"  data-id="<?php echo  $prop->id  ?>" style="padding:0px;" data-token=<?php echo csrf_token(); ?>>
						   <option value="">-Select-</option>
							<?php
							foreach ($bugSeveritys as $bugSeverity) { ?>
								
								
								<option <?php if ($prop->bug_severity_id == $bugSeverity->id) { echo " selected"; }  ?>  value="<?php echo $bugSeverity->id; ?>" ><?php echo $bugSeverity->name; ?></option>
							<?php
							}
							?>
							</select>
							<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-severity-history" title="Show Severity History" data-id="<?php echo  $prop->id  ?>"><i class="fa fa-info-circle"></i></button>
						</div>
			        </td>
			        <td class='break'>
						<div class="d-flex">
							<select class='form-control bug_status_id'  data-id="<?php echo  $prop->id;  ?>" style="padding:0px;" data-token=<?php echo csrf_token(); ?>>
								<?php
								foreach ($bugStatuses as $bugStatus) { ?>
								
								<option <?php if ($prop->bug_status_id == $bugStatus->id) { echo " selected"; }  ?>  value="<?php echo $bugStatus->id; ?>"><?php echo $bugStatus->name; ?></option>
								
								<?php
								   
								}
							?>
							</select>
							<button style="float:right;padding-right:0px;" type="button" class="btn btn-xs show-status-history" title="Show Status History" data-id="<?php echo  $prop->id;  ?>"><i class="fa fa-info-circle"></i></button>
						</div>

			        </td>
			        <td class='break'><?php echo  $prop->module_id  ?></td>
			        <td class='break'>
			          <div style="margin-bottom:10px;width: 100%;">
                    <div class="d-flex">
                       <input type="text" style="width: 100%;" class="form-control quick-message-field input-sm" data-bid="<?php echo  $prop->id;  ?>"  id="getMsg<?php echo  $prop->id;  ?>" name="message" placeholder="Message" value=""><div style="max-width: 30px;">
                       <button class="btn btn-sm btn-image send-message" title="Send message" data-id="<?php echo  $prop->id;  ?>"><img src="images/filled-sent.png" data-id="<?php echo  $prop->id;  ?>" /></button> </div>
                        
                        </div>
						<div class="d-flex">
							<div style="margin-bottom:10px;width: 100%;">
								<div class="d-flex justify-content-between expand-row-msg-chat" data-id="<?php echo  $prop->id;  ?>">
									<span class="td-mini-container-<?php echo  $prop->id;  ?> text-danger" style="margin:0px;">
										<?php echo $prop->last_chat_message_short; ?>
									</span>
								</div>
								<div class="expand-row-msg-chat" data-id="<?php echo  $prop->id;  ?>">
									<span class="td-full-container-<?php echo  $prop->id;  ?> hidden text-danger">
										<?php echo $prop->last_chat_message_long; ?>
									</span>
								</div>
							</div>
							<div style="max-width: 100%;text-align: right;padding-top: 10px;">
								<button type="button" class="btn btn-xs btn-image load-communication-modal" data-object='bug' data-id="<?php echo  $prop->id;  ?>" title="Load messages"><img src="images/chat.png" alt=""></button>
							</div>
						</div> 
                    </div>
			        </td>
			        <td><?php echo $prop->website;  ?></td>
			        <td>
						
						<div  class="d-flex" style="margin-left:5px;">
						<input type="checkbox" id="chkBug<?php echo  $prop->id  ?>" data-user="<?php echo  $prop->assign_to  ?>" name="chkBugName" class="chkBugNameCls"   value="<?php echo  $prop->id  ?>">					 
						 <button  title="create quick task" type="button" class="btn btn-image d-inline create-quick-task " data-id="<?php echo  $prop->id;  ?>"  data-category_title="<?php echo  $prop->module_id;  ?>"  data-module_id="<?php echo  $prop->module_id;  ?>" data-website_id="<?php echo  $prop->website_id_val;  ?>"  data-website="<?php echo  $prop->website  ?>" data-bug_type_id="<?php echo  $prop->bug_type_id_val  ?>" data-title="<?php echo  $prop->website  ?> - <?php echo  $prop->module_id;  ?>"><img style="width:12px !important;margin-left:5px;" src="/images/add.png" /></button>
						 
						 <button  type="button" class="btn btn-image d-inline count-dev-customer-tasks" title="Show task history" data-id="<?php echo  $prop->id;  ?>" data-category="297"><i class="fa fa-info-circle"></i></button>
						 
						 </div>
						
						 <div  class="d-flex">
			        	<button type="button" title="Edit" data-id="<?php echo  $prop->id ; ?>" class="btn btn-edit-template">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" title="Push"  data-id="<?php echo  $prop->id;  ?>" class="btn btn-push">
			        	<i class="fa fa-eye" aria-hidden="true"></i>
			        	</button>

			        	<button type="button" title="Delete" data-id="<?php echo  $prop->id;  ?>" class="btn btn-delete-template">
			        		<i class="fa fa-trash" aria-hidden="true"></i>
			        	</button>
						</div>
			        
			        </td>
			      </tr>
<?php } ?>
				
				
