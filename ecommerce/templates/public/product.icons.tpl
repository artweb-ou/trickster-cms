<div class="product_icons {$class}">
    {assign var="iconPrefix" value='product_icons'}
    {$iconsCell_loc_top_left = ""}
    {$iconsCell_loc_top_right = ""}
    {$iconsCell_loc_bottom_left = ""}
    {$iconsCell_loc_bottom_right = ""}
    {$iconsCell_product_icons_image = ""}

    {foreach $iconsInfo as $iconInfo}
        {$iconStyle = ''}
        {if !empty($iconInfo.width)} {$iconStyle = "width:"|cat:$iconInfo.width * 2|cat:"%;"}{/if}
        {$iconLocation = $iconInfo.iconLocation}
        {$iconRole = $iconInfo.iconRole}
        {$iconAdditionalClass =  "{$iconRole} {$iconLocation}"}
        {if !empty($iconInfo.image)}
            {if !empty($iconStyle)}{$iconStyle = ' style="'|cat:$iconStyle|cat:'"'}{/if}
            {$iconsCell_{$iconLocation} = $iconsCell_{$iconLocation}|cat:"<img class='product_icons_image {$iconAdditionalClass}'{$iconStyle} src='{$controller->baseURL}image/type:productIcon/id:{$iconInfo.image}/filename:{$iconInfo.fileName}' alt='{$iconInfo.title}'/>"}
        {elseif !empty($iconInfo.title)}
            {if (!empty($iconInfo.iconTextColor))} {$iconStyle = $iconStyle|cat:"color:"|cat:$iconInfo.iconTextColor|cat:";"}{/if}
            {if (!empty($iconInfo.iconBgColor))} {$iconStyle = $iconStyle|cat:"background-color:"|cat:$iconInfo.iconBgColor|cat:";"}{/if}
            {if !empty($iconStyle)}{$iconStyle = ' style="'|cat:$iconStyle|cat:'"'}{/if}
            {$iconsCell_{$iconLocation} = $iconsCell_{$iconLocation}|cat:"<span class='product_icons_title {$iconAdditionalClass}'{$iconStyle}>{$iconInfo.title}</span>"}
        {/if}
    {/foreach}
    <div class="{$iconPrefix}_cells">
        <div class="{$iconPrefix}_cell loc_top_left">{$iconsCell_loc_top_left}</div>
        <div class="{$iconPrefix}_cell loc_top_right">{$iconsCell_loc_top_right}</div>
    </div>
    <div class="{$iconPrefix}_cells">
        <div class="{$iconPrefix}_cell loc_bottom_left">{$iconsCell_loc_bottom_left}</div>
        <div class="{$iconPrefix}_cell loc_bottom_right">{$iconsCell_loc_bottom_right}</div>
    </div>
</div>
