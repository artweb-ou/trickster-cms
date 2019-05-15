<div class="product_icons {$class}">
    {assign var="iconRoleGeneralDiscount" value=false}
    {assign var="iconPrefix" value='giBadge'}
    {assign var="iconStyle" value=''}
    {$iconsCell_loc_top_left = ""}
    {$iconsCell_loc_top_right = ""}
    {$iconsCell_loc_bottom_left = ""}
    {$iconsCell_loc_bottom_right = ""}
    {$iconsCell_product_icons_image = ""}

    {foreach $iconsInfo as $iconInfo}
        {if !empty($iconInfo.iconRole) && $iconInfo.iconRole =='role_general_discount'}
            {$iconRoleGeneralDiscount = true}
        {/if}
    {/foreach}
    {if !empty($displayOldPrice) && $iconRoleGeneralDiscount !==true}
        {if $element->getOldPrice()}
            <div class="product_discount_container">
                <span class="product_discount">-{$element->getDiscountPercent()|round}%</span>
            </div>
        {/if}
    {/if}
    {foreach $iconsInfo as $iconInfo}
        {$iconStyle = ''}
        {if !empty($iconInfo.width) && $iconInfo.iconStructureType != 'genericIcon'} {$iconStyle = "width:"|cat:$iconInfo.width|cat:"%;"}{/if}
        {if $iconInfo.iconStructureType == 'genericIcon'}
            {if !empty($iconInfo.width)} {$iconStyle = "width:"|cat:$iconInfo.width /2|cat:"%;"}{/if}
            {$iconLocation = $iconInfo.iconLocation}
            {$iconRole = $iconInfo.iconRole}
            {$iconAdditionalClass =  "{$iconPrefix} {$iconPrefix}_{$iconRole} {$iconPrefix}_{$iconLocation}"}

{*
'loc_top_left',
'loc_top_right',
'loc_bottom_left',
'loc_bottom_right',
*}
            {if !empty($iconInfo.image)}
                {if !empty($iconStyle)}{$iconStyle = ' style="'|cat:$iconStyle|cat:'"'}{/if}
                {$iconsCell_{$iconLocation} = $iconsCell_{$iconLocation}|cat:"<img class='product_icons_image {$iconAdditionalClass}'{$iconStyle} src='{$controller->baseURL}image/type:productIcon/id:{$iconInfo.image}/filename:{$iconInfo.fileName}' alt='{$iconInfo.title}'/>"}
            {elseif !empty($iconInfo.title)}
                {if (!empty($iconInfo.iconTextColor))} {$iconStyle = $iconStyle|cat:"color:"|cat:$iconInfo.iconTextColor|cat:";"}{/if}
                {if (!empty($iconInfo.iconBgColor))} {$iconStyle = $iconStyle|cat:"background-color:"|cat:$iconInfo.iconBgColor|cat:";"}{/if}
                {if !empty($iconStyle)}{$iconStyle = ' style="'|cat:$iconStyle|cat:'"'}{/if}
                {$iconsCell_{$iconLocation} = $iconsCell_{$iconLocation}|cat:"<span class='product_icons_title {$iconAdditionalClass}'{$iconStyle}>{$iconInfo.title}</span>"}
            {/if}
        {else}
            {if !empty($iconStyle)}{$iconStyle = ' style="'|cat:$iconStyle|cat:'"'}{/if}
            {$iconsCell_product_icons_image = $iconsCell_product_icons_image|cat:"<img class='product_icons_image' src='{$controller->baseURL}image/type:productIcon/id:{$iconInfo.image}/filename:{$iconInfo.fileName}'{$iconStyle} alt='{$iconInfo.title}'/>"}
        {/if}
    {/foreach}
    <div class="{$iconPrefix}-cells">
        <div class="cell-loc_top_left">{$iconsCell_loc_top_left}{$iconsCell_product_icons_image}</div>
        <div class="cell-loc_top_right">{$iconsCell_loc_top_right}</div>
    </div>
    <div class="{$iconPrefix}-cells">
        <div class="cell-loc_bottom_left">{$iconsCell_loc_bottom_left}</div>
        <div class="cell-loc_bottom_right">{$iconsCell_loc_bottom_right}</div>
    </div>
    {*<div class="cell-product_icons_image">{$iconsCell_product_icons_image}</div>*}
</div>
