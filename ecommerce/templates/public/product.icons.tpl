{function productIconsCell}
    <span class="product_icons_cell {$cellLocation}">
        {foreach $iconsInfo as $iconInfo}
            {if $iconInfo.iconLocation == $cellLocation}
                {if !empty($iconInfo.image)}
                    <img class='product_icon product_icon_image {$iconInfo.iconRole}'
                         {if !empty($iconInfo.width)}style="width:{$iconInfo.width * 2}%;"{/if}
                         src='{$controller->baseURL}image/type:productIcon/id:{$iconInfo.image}/filename:{$iconInfo.fileName}'
                         alt='{$iconInfo.title}'
                    />
                {elseif !empty($iconInfo.title)}
                    {$iconStyle = ''}
                    {if (!empty($iconInfo.iconTextColor))} {$iconStyle = "color:{$iconInfo.iconTextColor};"}{/if}
                    {if (!empty($iconInfo.iconBgColor))} {$iconStyle = "{$iconStyle}background-color:{$iconInfo.iconBgColor};"}{/if}
                    <span class='product_icon product_icon_text {$iconInfo.iconRole}' {if !empty($iconStyle)}style="{$iconStyle}"{/if}>{$iconInfo.title}</span>
                {/if}
            {/if}
        {/foreach}
    </span>
{/function}
<span class="product_icons {$class}">
    <span class="product_icons_cells">
        {productIconsCell cellLocation='loc_top_left'}
        {productIconsCell cellLocation='loc_top_right'}
    </span>
    <span class="product_icons_cells">
        {productIconsCell cellLocation='loc_bottom_left'}
        {productIconsCell cellLocation='loc_bottom_right'}
    </span>
</span>
