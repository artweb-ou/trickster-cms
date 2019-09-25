{if $filters = $element->getFilters()}
    <div class="products_filter">
        {foreach $filters as $filter}
            {include $theme->template('component.productsfilter_item.tpl')}
        {/foreach}
    </div>
{/if}