<script type="text/x-jsrender" id="template-result-block">
	<div class="table-responsive mt-3">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>ID</th>
                <th>Summary</th>
                <th>Type of Bug</th>
                <th>Steps to reproduce</th>
                <th>Environment</th>
                <th> Screenshot/Video url</th>
                <th>Assign to</th>
                <th>Severity</th>
                <th>Status</th>
                <th>Module/Feature</th>
                <th>Remarks </th>
                <th>Website</th>
                <th width="200px">Action</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			        <td class='break'>{{:prop.summary}}</td>
			        <td class='break'>{{:prop.bug_type_id}}</td>
			        <td class='break'>{{:prop.step_to_reproduce}}</td>
			        <td class='break'>{{:prop.bug_environment_id}}</td>
			        <td class='break'>{{:prop.url}}</td>
			        <td class='break'>
			        <select class='form-control assign_to'  data-id="{{>prop.id}}">
			        <?php
                         foreach($users as $user){
                             echo "<option {{if prop.assign_to == '".$user->id."'}} selected {{/if}} value='".$user->id."'>".$user->name."</option>";
                         }
                         ?>
			        </select>
			        </td>
			        <td class='break'>{{:prop.bug_severity_id}}</td>
			        <td class='break'>{{:prop.bug_status_id}}</td>
			        <td class='break'>{{:prop.module_id}}</td>
			        <td class='break'>{{:prop.remark}}</td>
			        <td class='break'>{{:prop.website}}</td>

			        <td>
			        	<button type="button" title="Edit" data-id="{{>prop.id}}" class="btn btn-edit-template">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" title="Push"  data-id="{{:prop.id}}" class="btn btn-push">
			        	<i class="fa fa-eye" aria-hidden="true"></i>
			        	</button>

			        	<button type="button" title="Delete" data-id="{{>prop.id}}" class="btn btn-delete-template">
			        		<i class="fa fa-trash" aria-hidden="true"></i>
			        	</button>
			        </td>
			      </tr>
			    {{/props}}  
		    </tbody>
		</table>
		{{:pagination}}
	</div>
</script>