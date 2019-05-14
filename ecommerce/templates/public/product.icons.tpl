<div class="product_icons {$class}">
    {if !empty($displayOldPrice)}
        {if $element->getOldPrice()}
            <div class="product_discount_container">
                <span class="product_discount">-{$element->getDiscountPercent()|round}%</span>
            </div>
        {/if}
    {/if}
    {foreach $iconsInfo as $iconInfo}
        {*{if !empty($iconInfo.iconProductAvail)}{var_dump($iconInfo.title,$iconInfo.iconProductAvail)}{/if}*}
        <img class='product_icons_image{if !empty($iconInfo.iconRole)} badge_{$iconInfo.iconRole}{/if}{if !empty($iconInfo.iconLocation)} badge_{$iconInfo.iconLocation}{/if}'
             src='{$controller->baseURL}image/type:productIcon/id:{$iconInfo.image}/filename:{$iconInfo.fileName}'
             {if (!empty($iconInfo.width))}style="width: {$iconInfo.width}%"{/if}
             alt='{$iconInfo.title}'/>
    {/foreach}
</div>
