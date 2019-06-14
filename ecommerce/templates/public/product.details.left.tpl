{if $element->getImagesList()}
	<div class="side_separator"></div>
	<div class="product_details_left">
		{include file=$theme->template('product.details.gallery.tpl')}
		{if !$noProductIcons} {
			{if  $iconsInfo = $element->getIconsInfo()}
				{include file=$theme->template('product.icons.tpl') class='product_details_icons'}
			{/if}
		{/if}
	</div>
	<div class="side_separator"></div>
{/if}