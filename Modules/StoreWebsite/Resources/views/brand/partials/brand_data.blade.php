<?php 
foreach($brands as $brand) { 
	if(request()->get('brd_store_website_id')){
		if(request()->get('no_brand')){
			if(in_array(request()->get('brd_store_website_id'), $apppliedResult[$brand->id])){
				continue;
			}
		}else{
			if(!in_array(request()->get('brd_store_website_id'), $apppliedResult[$brand->id])){
				continue;
			}
		}	
	}
?>		
	<tr>
		<td><?php echo $brand->id; ?></td>
		<td><a class="text-dark" target="_blank" href="{{ route('product-inventory.new') }}?brand[]={{ $brand->id }}">{{ $brand->name }}  ( {{ $brand->counts }} )</a></td>
		<td><?php echo $brand->min_sale_price; ?></td>
		<td><?php echo $brand->max_sale_price; ?></td>
	<?php 
	foreach($storeWebsite as $swid => $sw) { 
		$checked = (isset($apppliedResult[$brand->id]) && in_array($swid, $apppliedResult[$brand->id])) ? "checked" : ""; 
	?>
		<td>
			<input data-brand="<?php echo $brand->id; ?>" data-sw="<?php echo $swid; ?>" <?php echo $checked; ?> class="push-brand" type="checkbox" name="brand_website">
			<a href="javascript:;" data-href="{!! route('store-website.brand.history',['brand'=>$brand->id,'store'=>$swid]) !!}" class="log_history text-dark">
				<i class="fa fa-info-circle" aria-hidden="true"></i>
			</a>
			<br>
			<span>
				@php $magentoStoreBrandId = $brand->storewebsitebrand($swid); @endphp
				{{ $magentoStoreBrandId ? $magentoStoreBrandId : '' }}
			</span>
		</td>
	<?php 
	} 
	?>
	</tr>
<?php 
} 
?>