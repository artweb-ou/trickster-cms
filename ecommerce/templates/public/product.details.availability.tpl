<div class="product_details_availability">
	<span class="product_details_stock_status">{translations name='product.availability'}: </span>
	<span class="product_details_stock_value">
	{if $element->availability == "quantity_dependent"}
		{translations name='product.instock' q=$element->quantity}
	{elseif $element->availability == "inquirable"}
		{translations name='product.inquirable'}
	{elseif $element->availability == "unavailable"}
		{translations name='product.unavailable'}
	{elseif $element->availability == "available_inquirable"}
		{translations name='product.available_inquirable'}
	{else}
		{translations name='product.available'}
	{/if}
	</span>
</div>
