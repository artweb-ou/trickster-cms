{if $brandElement=$element->getBrandElement()}
	<a class='product_details_brand' href="{$brandElement->URL}">
		<img class='product_details_brand_image' src='{$controller->baseURL}image/type:productDetailsBrand/id:{$brandElement->image}/filename:{$brandElement->originalName}' alt='{$brandElement->title}' />
	</a>
{/if}