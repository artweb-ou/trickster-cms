{if $element->getImagesList()}
	<div class="product_details_left">
		{include file=$theme->template('product.details.gallery.tpl')}
		{if $iconsInfo = $element->getIconsInfo()}
			{include file=$theme->template('product.icons.tpl') class='product_details_icons'}
		{/if}
	</div>
{/if}