{assign moduleTitle $element->title}
{capture assign="moduleContent"}
		{if $element->originalName != ""}
			<div class="product_thumbnailsmall_image_container">
				{include file=$theme->template('component.elementimage.tpl') jsfix=1 type='productSmallThumb' class='product_thumbnailsmall_image' lazy=true}
				{if $iconsInfo = $element->getIconsInfo()}
					{include file=$theme->template('product.icons.tpl') class='product_thumbnailsmall_icons'}
				{/if}
			</div>
		{/if}
		{if $element->getPrice(false)}
			<span class="product_thumbnailsmall_price">{if !$element->isEmptyPrice()}{$element->getPrice()}&#xa0;{$selectedCurrencyItem->symbol}{/if}</span>
		{/if}
{/capture}
{assign moduleClass "product_thumbnailsmall product_short productid_{$element->id}"}
{assign moduleTitleClass "product_thumbnailsmall_title"}
{assign moduleTag "a"}
{assign moduleAttributes "href='{$element->URL}'"}
{include file=$theme->template("component.subcontentmodule_square.tpl")}