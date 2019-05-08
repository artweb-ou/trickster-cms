{assign moduleTitle $element->title}
{capture assign="moduleContent"}
	<script>window.products = window.products ? window.products: []</script>
	<script>window.products.push({$element->getElementData()|json_encode})</script>
	<a href="{$element->URL}" class="product_thumbnailsmall_link">
		{if $element->originalName != ""}
			<div class="product_thumbnailsmall_image_container">
				{include file=$theme->template('component.elementimage.tpl') type='productSmallThumb' class='product_thumbnailsmall_image' lazy=true}
				{if $iconsInfo = $element->getIconsInfo()}
					{include file=$theme->template('product.icons.tpl') class='product_thumbnailsmall_icons' displayOldPrice=true}
				{/if}
			</div>
		{/if}
		{if $element->getPrice(false)}
			<span class="product_thumbnailsmall_price">{if !$element->isEmptyPrice()}{$element->getPrice()}&#xa0;{$selectedCurrencyItem->symbol}{/if}</span>
		{/if}
	</a>
{/capture}
{assign moduleClass "product_thumbnailsmall product_short productid_{$element->id}"}
{assign moduleTitleClass "product_thumbnailsmall_title"}


{include file=$theme->template("component.subcontentmodule_square.tpl")}