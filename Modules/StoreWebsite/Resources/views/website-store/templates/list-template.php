<script type="text/x-jsrender" id="template-result-block">
	<div class="table-responsive mt-3">
		<table class="table table-bordered">
		    <thead>
		      <tr>
		      	<th>Id</th>
				<th>Name</th>
				<th>Code</th>
				<th>Country code</th>
				<th>Root Category</th>
				<th>Site</th>
				<th>Actions</th>
		      </tr>
		    </thead>
		    <tbody>
		    	{{props data}}
			      <tr>
			      	<td>{{:prop.id}}</td>
			        <td>{{:prop.name}}</td>
			        <td>{{:prop.code}}</td>
			        <td>{{:prop.country_code}}</td>
			        <td>{{:prop.root_category}}</td>
			        <td>{{:prop.website_name}}</td>
			        <td>
			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-edit-template">
			        		<i class="fa fa-edit" aria-hidden="true"></i>
			        	</button>
			        	<button type="button" data-id="{{>prop.id}}" class="btn btn-delete-template">
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