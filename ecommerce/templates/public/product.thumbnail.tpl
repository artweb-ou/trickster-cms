{assign moduleTitle $element->title}
{capture assign="moduleContent"}
	<script>window.products = window.products ? window.products : []</script>
	<script>window.products.push({$element->getElementData()|json_encode})</script>
	<a href="{$element->URL}" class="product_thumbnail_link">
		<div class="product_thumbnail_image_container">
			{if $element->originalName != ""}
				{include file=$theme->template('component.elementimage.tpl') type='productThumb' class='product_thumbnail_image' lazy=true}
			{/if}
			{if $element->getOldPrice()}
				{if $discount = $element->getDiscountPercent()}
					<div class="product_discount_container">
						<span class="product_discount">
							-{$discount}%
						</span>
					</div>
				{/if}
			{/if}
		</div>
		{$icons=$element->getIconsCompleteList()}
		{$connectedDiscounts=$element->getCampaignDiscounts()}
		{if $icons || $connectedDiscounts}
			<div class="product_thumbnail_icons">
				{foreach $icons as $icon}
					<img class='product_thumbnail_icons_image lazy_image' src="{$theme->getImageUrl('lazy.png')}" data-lazysrc='{$controller->baseURL}image/type:productIcon/id:{$icon->image}/filename:{$icon->originalName}' alt='{$icon->title}'{if $icon->iconWidth > 0} style="max-width: {$icon->iconWidth}%; width: auto; max-height: none; height: auto;"{/if}/>
				{/foreach}

				{foreach from=$connectedDiscounts item=discount}
					{if $discount->icon}
						<img class='product_thumbnail_icons_image lazy_image' src="{$theme->getImageUrl('lazy.png')}" data-lazysrc='{$controller->baseURL}image/type:productIcon/id:{$discount->icon}/filename:{$discount->iconOriginalName}' alt='{$discount->title}'{if $discount->iconWidth > 0} style="max-width: {$discount->iconWidth}%; width: auto; max-height: none; height: auto;"{/if}/>
					{/if}
				{/foreach}
			</div>
		{/if}
		<span class="product_thumbnail_price">{if !$element->isEmptyPrice()}{$element->getPrice()}&#xa0;{$selectedCurrencyItem->symbol}{/if}</span>
	</a>
{/capture}
{assign moduleClass "product_thumbnail product_short productid_{$element->id}"}
{assign moduleTitleClass "product_thumbnail_title"}
{include file=$theme->template("component.subcontentmodule_square.tpl")}