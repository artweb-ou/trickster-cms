{if $filters = $element->getFilters()}
    <div class="products_filter">
        {foreach $filters as $filter}
            {if $filter->isRelevant()}
                {include $theme->template('component.filterdropdown.tpl')}
            {/if}
        {/foreach}
    </div>
{/if}