{$icons=$element->getIconsCompleteList()}
{$connectedDiscounts=$element->getCampaignDiscounts()}
{if $icons || $connectedDiscounts}
	<div class="product_details_icons">
		{foreach $icons as $icon}
			<img class='product_details_icons_image' src='{$controller->baseURL}image/type:productIconBig/id:{$icon->image}/filename:{$icon->originalName}' alt='{$icon->title}' />
		{/foreach}

		{foreach from=$connectedDiscounts item=discount}
			{if $discount->icon}
				<img class='product_thumbnailsmall_icons_image discount_icon lazy_image' src='{$controller->baseURL}image/type:productIconBig/id:{$discount->icon}/filename:{$discount->iconOriginalName}' alt='{$discount->title}' />
			{/if}
		{/foreach}
	</div>
{/if}