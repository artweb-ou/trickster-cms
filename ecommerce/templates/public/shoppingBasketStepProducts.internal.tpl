<table class='shoppingbasket_table table_component'>
    <thead class="shoppingbasket_table_header">
    <tr>
        <th class='shoppingbasket_table_header_title' colspan='2'>
            {translations name='shoppingbasket.productstable_product'}
        </th>
        <th class='shoppingbasket_table_header_price'>
            {translations name='shoppingbasket.productstable_price'}
        </th>
        <th class='shoppingbasket_table_header_amount'>
            {translations name='shoppingbasket.productstable_amount'}
        </th>
        <th class='shoppingbasket_table_header_totalprice'>
            {translations name='shoppingbasket.productstable_total'}
        </th>
        {if !$checkout}
            <th class='shoppingbasket_table_header_remove'>
                {translations name='shoppingbasket.productstable_remove'}
            </th>
        {/if}
    </tr>
    </thead>
    <tbody class="shoppingbasket_table_rows">
    {foreach from=$element->getProducts() item=product}
        {include file='shoppingBasketStepProducts.product.tpl' element=$product}
    {/foreach}
    </tbody>
    <tbody class="shoppingbasket_total shoppingbasket_total_products">
    <tr class="shoppingbasket_total">
        <th class="shoppingbasket_total_title" colspan="4">
            {translations name='shoppingbasket.productstable_productsprice'}:
        </th>
        <td class="shoppingbasket_total_value">
            {$element->getProductsPrice()} {$selectedCurrencyItem->symbol}
        </td>
        <td class="shoppingbasket_total_value_spacer"></td>
    </tr>
    </tbody>
</table>
