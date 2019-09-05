{if $element->getImagesList()}
	<div class="side_separator left_before"></div>
	<div class="product_details_left">
		{include file=$theme->template('product.details.gallery.tpl')}
		{if empty($noProductIcons)}
			{if  $iconsInfo = $element->getIconsInfo()}
				{include file=$theme->template('product.icons.tpl') iconSize='product' class='product_details_icons'}
			{/if}
		{/if}
	</div>
	<div class="side_separator left_after"></div>
{/if}