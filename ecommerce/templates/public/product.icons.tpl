<div class="product_icons {$class}">
    {if !empty($displayOldPrice)}
        {if $element->getOldPrice()}
            <div class="product_discount_container">
                <span class="product_discount">-{$element->getDiscountPercent()|round}%</span>
            </div>
        {/if}
    {/if}
    {foreach $iconsInfo as $iconInfo}
        <img class='product_icons_image'
             src='{$controller->baseURL}image/type:productIcon/id:{$iconInfo.image}/filename:{$iconInfo.fileName}'
             {if (!empty($iconInfo.width))}style="width: {$iconInfo.width}%"{/if}
             alt='{$iconInfo.title}'/>
    {/foreach}
</div>
