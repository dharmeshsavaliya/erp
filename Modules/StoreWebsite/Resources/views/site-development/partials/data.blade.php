    @php
    $isAdmin = auth()->user()->isAdmin();
    $isHod = auth()->user()->hasRole('HOD of CRM');
    $hasSiteDevelopment = auth()->user()->hasRole('Site-development');
    $userId = auth()->user()->id;
    $pagrank = $categories->perPage() * ($categories->currentPage()- 1) + 1;
    @endphp 
	 @foreach($categories as $key => $category) 
    <?php
    $site = $category->getDevelopment($category->id, isset($website) ? $website->id : null, $category->site_development_id);//

    if ($isAdmin || $hasSiteDevelopment || ($site && $site->developer_id == $userId)) {
    ?>

        <tr>
            <td>
                {{ $pagrank++  }}
            </td>        
            <td>
                @include("storewebsite::site-development.partials.edit-modal")
                @include("storewebsite::site-development.partials.site-asset-modal")
                {{ $category->title }}

                <div style="display: flex; float: right">  <button onclick="checkAsset({{$category->id}}, {{ $category->site_development_id }})" style="background-color: transparent;border: 0; margin-top:0px;" class="p-2" title="Set this category in site assets for this website"><i class="fa fa-info-circle"></i></button></div>
              <div style="display: flex;float: right">  <button onclick="editCategory({{$category->id}})" style="background-color: transparent;border: 0;margin-top:0px;" class="pl-0"><i class="fa fa-edit"></i></button>


                <!-- <input class="fa-ignore-category" type="checkbox" data-onstyle="secondary" data-category-id="{{$category->id}}" data-site-id="{{ isset($website) ? $website->id : '' }}" <?php echo (request('status') == 'ignored') ? "checked" : "" ?>
                data-on="Allow" data-off="Disallow"
                data-toggle="toggle" data-width="90"> -->
                @if(request('status') == 'ignored')
                <button style="padding:0px;" type="button" class="btn btn-image fa-ignore-category pl-0 mt-0" data-category-id="{{$category->id}}" data-site-id="{{ isset($website) ? $website->id : '' }}" data-status="1">
                    <i class="fa fa-ban" aria-hidden="true" style="color:red;"></i>
                </button>
                @else
                <button style="padding:0px;" type="button" class="btn btn-image fa-ignore-category pl-0" data-category-id="{{$category->id}}" data-site-id="{{ isset($website) ? $website->id : '' }}" data-status="0">
                    <i class="fa fa-ban" aria-hidden="true"></i>
                </button>
                @endif
              </div>
				<form>
                    <label class="radio-inline">
                        <input class="save-artwork-status" type="radio" name="artwork_status" data-category="{{ $category->id }}" value="Yes" data-type="artwork_status" data-site="@if($site){{ $site->id }}@endif" @if($site){{ $site->artwork_status == 'Yes' ? 'checked' : '' }}@endif />Yes
                    </label>
                    <label class="radio-inline">
                        <input class="save-artwork-status" type="radio" name="artwork_status" data-category="{{ $category->id }}" value="No" data-type="artwork_status" data-site="@if($site){{ $site->id }}@endif" @if($site){{ $site->artwork_status == 'No' ? 'checked' : '' }}@endif />No
                    </label>
                    <label class="radio-inline">
                        <input class="save-artwork-status" type="radio" name="artwork_status" data-category="{{ $category->id }}" value="Done" data-type="artwork_status" data-site="@if($site){{ $site->id }}@endif" @if($site){{ $site->artwork_status == 'Done' ? 'checked' : '' }}@endif />Done
                    </label>
                </form>
            </td>
            <td>{{$category->website}}</td>
			<td>
				{{Form::select('site_development_master_category_id', [''=>'- Select-']+$masterCategories, $category->site_development_master_category_id, array('class'=>'save-item-select globalSelect2','data-category'=>$category->id, 'data-type'=>'site_development_master_category_id', 'data-site'=>$site ? $site->id : '0'))}}
            </td>
			<td class="pt-0 pr-2">
				<div class="col-md-12 mb-1 p-0 d-flex  pt-2 mt-1">
					 <input style="margin-top: 0px;width:auto !important;" type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="" id="remark_{{$key}}" data-catId="{{ $category->id }}" data-siteId="@if($site){{ $site->id }}@endif" data-websiteId="{{ isset($website) ? $website->id : '' }}" >
					 <div style="margin-top: 0px;" class="d-flex p-0">
						<button class="btn pr-0 btn-xs btn-image " onclick="saveRemarks({{$key}})"><img src="/images/filled-sent.png" /></button>
					 </div>
                </div>
				<div class="col-md-12 p-0 pl-1">
				  <div class="d-flex">
                                      @if($site->lastRemark) Remarks = @endif
                                      <div class="justify-content-between expand-row-msg" data-id="@if($site->lastRemark){{$site->lastRemark->id}}@endif">
                                          <span class="td-full-container-@if($site->lastRemark){{$site->lastRemark->id}}@endif" > @if($site->lastRemark)  {{ str_limit($site->lastRemark->remarks, 10, '...') }} @endif</span>
                                      </div>
                    </div>
                 </div>
            </td>
			
			<td colspan=2>
			<table class="assign">	 
				@foreach($category->assignedTo as $assignedTo)   
					<tr>
                        <td width="32%">
						
							  @if(auth()->user()->isAdmin())
							<select class="form-control assign-user" data-id="{{$assignedTo['id']}}"  name="master_user_id">
                                <option value="">Select...</option>
                                @foreach($users_all as $value)
                                    @if( $assignedTo['assigned_to_name'] == $value->name  )
                                        <option value="{{$value->id }}" selected>{{ $value->name }}</option>
                                    @else
                                         <option value="{{$value->id }}">{{ $value->name }}</option>
                                    @endif
                                @endforeach
                            </select>
							@else
							{{$assignedTo['assigned_to_name']}}
							@endif
						</td>
					<td class="pt-2">
                          <div class="col-md-12 mb-1 p-0 d-flex pl-4 pt-2 mt-1 msg">
                              <?php
                              $MsgPreview = '# ';
                              if ($website) {
                                  $MsgPreview = $website->website;
                              }
                              if ($site) {
                                  $MsgPreview = $MsgPreview . ' ' . $site->title;
                              }
                              ?>
							  <input type="text" style="width: 100%; float: left;" class="form-control quick-message-field input-sm" name="message" placeholder="Message" value="">
                                 <div class="d-flex p-0">
								     <button style="float: left;padding: 0 0 0 5px" class="btn btn-sm btn-image send-message" title="Send message" data-taskid="{{$assignedTo['id']}}"><img src="/images/filled-sent.png" style="cursor: default;"></button>
								 </div>
								 <button type="button" class="btn btn-xs btn-image load-communication-modal load-body-class" data-object="{{$assignedTo['message_type']}}" data-id="{{$assignedTo['id']}}" title="Load messages" data-dismiss="modal"><img src="/images/chat.png" alt=""></button>
							</div>
						  
                          <div class="col-md-12 p-0 pl-1 text">
								<!-- START - Purpose : Show / Hide Chat & Remarks , Add Last Remarks - #DEVTASK-19918 -->
                                  <div class="d-flex">
                                      <div class="justify-content-between expand-row-msg-chat" data-id="{{$assignedTo['id']}}">
                                          <span class="td-full-chat-container-{{$assignedTo['id']}} pl-1"> {{ str_limit($assignedTo['message'], 20,'...') }} </span>
                                      </div>
                                  </div>
                                  <div class="expand-row-msg-chat" data-id="{{$assignedTo['id']}}">
                                      <span class="td-full-chat-container-{{$assignedTo['id']}} hidden">  {{ $assignedTo['message'] }} </span>
                                  </div>
                                  <!-- END - #DEVTASK-19918 -->
                          </div>
						  </td>
					
					</tr>
				@endforeach
				 </table>
			</td> 
		
            <td style="display:none;">
                <input type="hidden" id="website_id" value="{{ isset($website) ? $website->id : ''}}">
                <input style="margin-top: 0;" type="text" class="form-control save-item" data-category="{{ $category->id }}" data-type="title" value="@if($site){{ $site->title }}@endif" data-site="@if($site){{ $site->id }}@endif">
                <form>
                    <label class="radio-inline">
                        <input class="save-artwork-status" type="radio" name="artwork_status" data-category="{{ $category->id }}" value="Yes" data-type="artwork_status" data-site="@if($site){{ $site->id }}@endif" @if($site){{ $site->artwork_status == 'Yes' ? 'checked' : '' }}@endif />Yes
                    </label>
                    <label class="radio-inline">
                        <input class="save-artwork-status" type="radio" name="artwork_status" data-category="{{ $category->id }}" value="No" data-type="artwork_status" data-site="@if($site){{ $site->id }}@endif" @if($site){{ $site->artwork_status == 'No' ? 'checked' : '' }}@endif />No
                    </label>
                    <label class="radio-inline">
                        <input class="save-artwork-status" type="radio" name="artwork_status" data-category="{{ $category->id }}" value="Done" data-type="artwork_status" data-site="@if($site){{ $site->id }}@endif" @if($site){{ $site->artwork_status == 'Done' ? 'checked' : '' }}@endif />Done
                    </label>
                </form>
            </td>
            <?php /* <td><input type="text" class="form-control save-item" data-category="{{ $category->id }}" data-type="description" value="@if($site){{ $site->description }}@endif" data-site="@if($site){{ $site->id }}@endif"></td> */ ?>
            <td  style="display:none;">
                <div class="row m-0">
                    <div class="col-md-6 mb-1 pr-0 pl-0">
                        <select style="margin-top: 5px;" class="form-control save-item-select developer assign-to select2" data-category="{{ $category->id }}" data-type="developer" data-site="@if($site){{ $site->id }}@endif" name="developer_id" id="user-@if($site){{ $site->id }}@endif">
                            <option value="">Select Developer</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" @if($site && $site->developer_id == $user->id) selected @endif >{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-1 pl-2 pr-2">
                        <select style="margin-top: 5px;" name="designer_id" class="form-control save-item-select designer assign-to select2" data-category="{{ $category->id }}" data-type="designer_id" data-site="@if($site) {{ $site->id }} @endif" id="user-@if($site){{ $site->id }}@endif">
                            <option value="">Select Designer</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" @if($site && $site->designer_id == $user->id) selected @endif >{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row m-0">
                    <div class="col-md-6 mb-1 pr-0 pl-0">
                        <select style="margin-top: 5px;" name="html_designer" class="form-control save-item-select html assign-to select2" data-category="{{ $category->id }}" data-type="html_designer" data-site="@if($site) {{ $site->id }} @endif" id="user-@if($site){{ $site->id }}@endif">
                            <option value="">Select Html</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" @if($site && $site->html_designer == $user->id) selected @endif >{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-1 pl-2 pr-2">
                        <select style="margin-top: 5px;" name="tester_id" class="form-control save-item-select html assign-to select2" data-category="{{ $category->id }}" data-type="tester_id" data-site="@if($site) {{ $site->id }} @endif" id="user-@if($site){{ $site->id }}@endif">
                            <option value="">Select Tester</option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}" @if($site && $site->tester_id == $user->id) selected @endif >{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </td>
           
            <td class="pt-1">
                <button type="button" data-site-id="@if($site){{ $site->id }}@endif" data-site-category-id="{{ $category->id }}" data-store-website-id="{{ isset($website) ? $website->id : '' }} " class="btn btn-file-upload pd-5">
                    <i class="fa fa-upload" aria-hidden="true"></i>
                </button>
                @if($site)
                <button type="button" data-site-id="@if($site){{ $site->id }}@endif" data-site-category-id="{{ $category->id }}" data-store-website-id="{{ isset($website) ? $website->id : '' }}" class="btn btn-file-list pd-5">
                    <i class="fa fa-list" aria-hidden="true"></i>
                </button>
                <button type="button" data-site-id="@if($site){{ $site->id }}@endif" data-site-category-id="{{ $category->id }}" data-store-website-id="{{ isset($website) ? $website->id : '' }}" class="btn btn-store-development-remark pd-5">
                    <i class="fa fa-comment" aria-hidden="true"></i>
                </button>
                <button type="button" title="Artwork status history" class="btn artwork-history-btn pd-5" data-id="@if($site){{ $site->id }}@endif">
                    <i class="fa fa-history" aria-hidden="true"></i>
                </button>
                @endif
                <button type="button" class="btn preview-img-btn pd-5" data-id="@if($site){{ $site->id }}@endif">
                    <i class="fa fa-eye" aria-hidden="true"></i>
                </button>
                @if(Auth::user()->isAdmin() || $hasSiteDevelopment)
                @php
                    $websitenamestr = ($website) ? $website->title : "";
                @endphp
                <button style="padding:3px;" title="create quick task" type="button" class="btn btn-image d-inline create-quick-task pd-5" data-id="@if($site){{ $site->id }}@endif" data-title="@if($site){{ $websitenamestr.' '.$site->title }}@endif"><img style="width:12px !important;" src="/images/add.png" /></button>
                <button style="padding-left: 0;float: right;padding-right:0px;" type="button" class="btn pt-1 btn-image d-inline count-dev-customer-tasks" title="Show task history" data-id="@if($site){{ $site->id }}@endif" data-category="{{$category->id}}"><i class="fa fa-info-circle"></i></button>
                <button style="padding-left: 0;float: right;padding-right:0px;" type="button" class="btn pt-1 btn-image d-inline tasks-relation" title="Show task relation" data-id="@if($site){{ $site->id }}@endif"><i class="fa fa-dashboard"></i></button>
                @endif
                <button class="btn btn-image d-inline create-quick-task pd-5">
                    <span>
                <?php $status = ($site) ? $site->status : 0; ?>
                        @if($status==3)
                            <i class="fa fa-ban save-status" data-text="4" data-site="{{ ($site) ? $site->id : '' }}" data-category="{{$category->id}}"  data-type="status" aria-hidden="true" style="color:red;" title="Deactivate"></i>
                        @elseif($status==4 || $status==0 )
                            <i class="fa fa-ban save-status" data-text="3" data-site="{{ ($site) ? $site->id : '' }}" data-category="{{$category->id}}"  data-type="status" aria-hidden="true" style="color:black;" title="Activate"></i>
                        @endif
                </span>
                </button>
                <?php /* <button style="padding:3px;" type="button" class="btn btn-image d-inline toggle-class pd-5" data-id="{{ $category->id }}"><img width="2px;" src="/images/forward.png" /></button> */ ?>
                <button type="button" data-site-id="@if($site){{ $site->id }}@endif" class="btn btn-status-histories-get pd-5" title="Status History">
                    <i class="fa fa-empire" aria-hidden="true"></i>
                </button>
				
				

                <?php echo Form::select("status", ["" => "-- Select --"] + $allStatus, $site->status , [
                    "class" => "form-control save-item-select width-auto globalSelect2",
                    "data-category" => $category->id,
                    "data-type" => "status",
                    "data-site" => ($site) ? $site->id : ""
                ])  ?>

            </td>
        </tr>

        <?php /* <tr class="hidden_row_{{ $category->id  }} dis-none" data-eleid="{{ $category->id }}">
            <td colspan="2">
                <?php  echo Form::select("status", ["" => "-- Select --"] + $allStatus, ($site) ? $site->status : 0, [
                    "class" => "form-control save-item-select",
                    "data-category" => $category->id,
                    "data-type" => "status",
                    "data-site" => ($site) ? $site->id : ""
                ])  ?>

            </td>
            <?php  <td colspan="2">
            <select style="margin-top: 5px;" class="form-control save-item-select developer" data-category="{{ $category->id }}" data-type="developer" data-site="@if($site){{ $site->id }}@endif" name="developer_id" id="user-@if($site){{ $site->id }}@endif">
    				<option value="">Select Developer</option>
    				@foreach($users as $user)
    					<option value="{{ $user->id }}" @if($site && $site->developer_id == $user->id) selected @endif >{{ $user->name }}</option>
    				@endforeach
    			</select>
            <select style="margin-top: 5px;" name="designer_id" class="form-control save-item-select designer" data-category="{{ $category->id }}" data-type="designer_id" data-site="@if($site) {{ $site->id }} @endif" id="user-@if($site){{ $site->id }}@endif">
                    <option value="">Select Designer</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"@if($site && $site->designer_id == $user->id) selected @endif >{{ $user->name }}</option>
                    @endforeach
                </select>
                <select style="margin-top: 5px;" name="html_designer" class="form-control save-item-select html" data-category="{{ $category->id }}" data-type="html_designer" data-site="@if($site) {{ $site->id }} @endif" id="user-@if($site){{ $site->id }}@endif">
                    <option value="">Select Html</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @if($site && $site->html_designer == $user->id) selected @endif >{{ $user->name }}</option>
                    @endforeach
                </select>
                <select style="margin-top: 5px;" name="tester_id" class="form-control save-item-select html" data-category="{{ $category->id }}" data-type="tester_id" data-site="@if($site) {{ $site->id }} @endif" id="user-@if($site){{ $site->id }}@endif">
                    <option value="">Select Tester</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @if($site && $site->tester_id == $user->id) selected @endif >{{ $user->name }}</option>
                    @endforeach
                </select>
            </td>
            <td></td>
            <td></td>
        </tr> */ ?>
    <?php } ?>

    @endforeach
