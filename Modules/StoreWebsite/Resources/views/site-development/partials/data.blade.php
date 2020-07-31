    @php
        $isAdmin            = auth()->user()->isAdmin();
        $isHod              = auth()->user()->hasRole('HOD of CRM');
        $hasSiteDevelopment = auth()->user()->hasRole('Site-development');
        $userId             = auth()->user()->id;
    @endphp

    @foreach($categories as $category)
		<?php
            $site = $category->getDevelopment($category->id,$website->id);
            if($isAdmin || $hasSiteDevelopment || ($site && $site->developer_id == $userId)) {
        ?>
    	<tr>
    		<td>
    			@if($website) {{ $website->website }} @endif
    			<br>
    			{{ $category->title }}
    			<br>
    			<button onclick="editCategory({{$category->id}})" style="background-color: transparent;border: 0;"><i class="fa fa-edit"></i></button>
                <input class="fa-ignore-category" type="checkbox" data-onstyle="secondary" data-category-id="{{$category->id}}" data-site-id="@if($website) {{ $website->id }} @endif" <?php echo (request('status') == 'ignored') ? "checked" : "" ?>
                data-on="Allow" data-off="Disallow"
                data-toggle="toggle" data-width="150">
    		</td>
    		<td>
    			<input type="hidden" id="website_id" value="@if($website) {{ $website->id }} @endif">
    			<input type="text" class="form-control save-item" data-category="{{ $category->id }}" data-type="title" value="@if($site){{ $site->title }}@endif" data-site="@if($site){{ $site->id }}@endif"></td>
    		<td><input type="text" class="form-control save-item" data-category="{{ $category->id }}" data-type="description" value="@if($site){{ $site->description }}@endif" data-site="@if($site){{ $site->id }}@endif"></td>
    		<td>
    			<?php echo Form::select("status",["" => "-- Select --"] + $allStatus,($site) ? $site->status : 0,[
      				"class" => "form-control save-item-select" ,
      				"data-category" => $category->id,
      				"data-type" => "status",
      				"data-site" => ($site) ? $site->id : ""
      			]) ?>
      			<select style="margin-top: 5px;" class="form-control save-item-select developer" data-category="{{ $category->id }}" data-type="developer" data-site="@if($site){{ $site->id }}@endif" name="developer_id" id="user-@if($site){{ $site->id }}@endif">
    				<option>Select Developer</option>
    				@foreach($users as $user)
    					<option value="{{ $user->id }}" @if($site && $site->developer_id == $user->id) selected @endif >{{ $user->name }}</option>
    				@endforeach
    			</select>
                <select style="margin-top: 5px;" name="designer_id" class="form-control save-item-select designer" data-category="{{ $category->id }}" data-type="designer_id" data-site="@if($site) {{ $site->id }} @endif" id="user-@if($site){{ $site->id }}@endif">
                    <option>Select Designer</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}"@if($site && $site->designer_id == $user->id) selected @endif >{{ $user->name }}</option>
                    @endforeach
                </select>
                <select style="margin-top: 5px;" name="html_designer" class="form-control save-item-select html" data-category="{{ $category->id }}" data-type="html_designer" data-site="@if($site) {{ $site->id }} @endif" id="user-@if($site){{ $site->id }}@endif">
                    <option>Select Html</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @if($site && $site->html_designer == $user->id) selected @endif >{{ $user->name }}</option>
                    @endforeach
                </select>
    		</td>
            <td>
    			@if($site)
    				<div class="chat_messages expand-row table-hover-cell">
    					<button type="button" class="btn btn-xs btn-image load-communication-modal" data-is_admin="{{ $isAdmin }}" data-is_hod_crm="{{ $isHod }}" data-object="site_development" data-id="{{$site->id}}" data-load-type="text" data-all="1" title="Load messages"><img src="/images/chat.png" alt=""></button>
    					<span class="chat-mini-container"> @if($site->lastChat) {{ $site->lastChat->message }} @endif</span>
    			     	<span class="chat-full-container hidden"></span>
    				</div>
    			@endif
    			<div class="d-flex">
                    <input type="text" class="form-control quick-message-field" name="message" placeholder="Message" value="" id="message-@if($site){{ $site->id }}@endif">
                    <button class="btn btn-sm btn-image send-message-site" data-id="@if($site){{ $site->id }}@endif"><img src="/images/filled-sent.png"/></button>
                    <br/>
                </div>
                <input type="checkbox" id="developer_{{$category->id}}" name="developer" value="developer">
                <label for="developer">Developer</label><br>
                <input type="checkbox" id="designer_{{$category->id}}" name="designer" value="designer">
                <label for="designer">Designer</label><br>
                <input type="checkbox" id="html_{{$category->id}}" name="html" value="html">
                <label for="html">Html</label><br><br>
    		</td>
    		<td>@if($site) {{ $site->created_at }}@endif</td>
            <td>
                <button type="button" data-site-id="@if($site){{ $site->id }}@endif" data-site-category-id="{{ $category->id }}" data-store-website-id="@if($website) {{ $website->id }} @endif" class="btn btn-file-upload">
                    <i class="fa fa-upload" aria-hidden="true"></i>
                </button>
                @if($site)
                    <button type="button" data-site-id="@if($site){{ $site->id }}@endif" data-site-category-id="{{ $category->id }}" data-store-website-id="@if($website) {{ $website->id }} @endif" class="btn btn-file-list">
                        <i class="fa fa-list" aria-hidden="true"></i>
                    </button>
                    <button type="button" data-site-id="@if($site){{ $site->id }}@endif" data-site-category-id="{{ $category->id }}" data-store-website-id="@if($website) {{ $website->id }} @endif" class="btn btn-store-development-remark">
                        <i class="fa fa-comment" aria-hidden="true"></i>
                    </button>
                @endif
            </td>
    	</tr>
    <?php } ?>
		@include("storewebsite::site-development.partials.edit-modal")
  @endforeach
