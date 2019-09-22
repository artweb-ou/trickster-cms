{if $filters = $element->getFilters()}
    <div class="products_filter">
        {foreach $filters as $filter}
            {include $theme->template('component.filterdropdown.tpl')}
        {/foreach}
    </div>
{/if}