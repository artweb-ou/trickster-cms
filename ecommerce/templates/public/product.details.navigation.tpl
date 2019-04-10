<div class="product_details_nav">
	{if $product = $element->getPreviousProduct()}
		<a class="product_details_navlink_previous" href="{$product->URL}">{translations name='product.previousproduct'}</a>
	{/if}
	{if $product = $element->getNextProduct()}
		<a class="product_details_navlink_next" href="{$product->URL}">{translations name='product.nextproduct'}</a>
	{/if}
</div>