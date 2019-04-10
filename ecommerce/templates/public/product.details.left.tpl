{if $element->getImagesList()}
	<div class="product_details_left">
		{include file=$theme->template('product.details.gallery.tpl')}
		{include file=$theme->template('product.details.icons.tpl')}
	</div>
{/if}