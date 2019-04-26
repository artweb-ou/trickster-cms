{if $element->getDeliveryStatus()}
	<div class="product_details_delivery">
		<span class="product_details_delivery_label">{translations name='product.delivery'}: </span>
		<span class="product_details_delivery_value">{$element->getDeliveryStatus()}</span>
	</div>
{/if}