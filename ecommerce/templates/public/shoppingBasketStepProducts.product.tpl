<tr class="shoppingbasket_table_product" data-id="{$element->getBasketProductId()}">
    <td class="shoppingbasket_table_image_container">
        {if $element->getImage()}<img class="shoppingbasket_table_image" src="{$element->getImage()}">{/if}
    </td>
    <td class="shoppingbasket_table_title">
        <a class="shoppingbasket_table_title"
           href="{$element->getUrl()}">
            {$element->getTitle()}
        </a>
        <div class="shoppingbasket_table_category_title">{$element->getCategory()}</div>
        <div class="shoppingbasket_table_code">{translations name='shoppingbasket.productstable_productcode'}
            : {$element->getCode()}</div>
        <div class="shoppingbasket_table_description">{$element->getVariationsText()}</div>
    </td>
    <td class="shoppingbasket_table_price">
        {if $element->getSalesPrice() != $element->getPrice()}
            <div class="shoppingbasket_table_full_price_value">{$element->getPrice()} {$selectedCurrencyItem->symbol}</div>{/if}
        <div class="shoppingbasket_table_price_value">{if $element->getSalesPrice() != $element->getPrice()}{$element->getSalesPrice()} {$selectedCurrencyItem->symbol}{else}{$element->getPrice()} {$selectedCurrencyItem->symbol}{/if}</div>
    </td>
    <td class="shoppingbasket_table_amount">
        {if !$checkout}
        <div class="shoppingbasket_table_amount_container">
            <span class="button shoppingbasket_table_amount_minus"><span class="button_text">-</span></span>
            <input class="input_component shoppingbasket_table_amount_input" value="{$element->getAmount()}">
            <span class="button shoppingbasket_table_amount_plus"><span class="button_text">+</span></span>
        </div>
        {else}
            {$element->getAmount()}
        {/if}
    </td>
    <td class="shoppingbasket_table_totalprice">
        <span class="shoppingbasket_table_totalprice_value">{$element->getTotalPrice()} {$selectedCurrencyItem->symbol}</span>
    </td>
    {if !$checkout}
    <td class="shoppingbasket_table_remove"><a class="shoppingbasket_table_remove_button"></a></td>
    {/if}
</tr>