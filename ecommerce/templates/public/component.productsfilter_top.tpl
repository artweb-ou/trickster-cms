{if !empty($displayFilterTopInfo)}
    <div class="products_filter_top">
        {if $amount = $element->getFilteredProductsAmount()}
            <div class="products_filter_amount">{translations name='category.productsamount' s=$amount}</div>
        {/if}
        <button class="products_filter_mobile mobile_common_menu_button button" data-menuid=".productsearch"
                data-menuclass="productsearch_mobilemenu"><span class="button_text">{translations name='category.filter'}</span><span class="products_filter_mobile_amount">{$element->getSelectedFiltersCount()}</span></button>
    </div>
{/if}