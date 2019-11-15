{assign moduleTitle $element->title}
{capture assign="moduleContent"}
    <span class="product_thumbnail_image_container">
        {if $element->originalName != ""}
            {include file=$theme->template('component.elementimage.tpl') jsfix=1 type='productThumb' class='product_thumbnail_image' lazy=true}
        {/if}
        {if $iconsInfo = $element->getIconsInfo()}
            {include file=$theme->template('product.icons.tpl') class='product_thumbnail_icons'}
        {/if}
    </span>
    <span class="product_thumbnail_price">{if !$element->isEmptyPrice()}{$element->getPrice()}&#xa0;{$selectedCurrencyItem->symbol}{/if}</span>
{/capture}
{assign moduleClass "product_thumbnail product_short productid_{$element->id}"}
{assign moduleTitleClass "product_thumbnail_title"}
{assign moduleTag "a"}
{assign moduleAttributes "href='{$element->URL}'"}
{include file=$theme->template("component.subcontentmodule_square.tpl")}