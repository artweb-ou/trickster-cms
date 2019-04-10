{if $element->getDeliveryStatus()}
	<div class="product_details_delivery">
		<span class="product_details_delivery_label">{translations name='product.delivery'}:</span>
		<span class="product_details_delivery_value">&#160;{$element->getDeliveryStatus()}</span>
	</div>
{/if}