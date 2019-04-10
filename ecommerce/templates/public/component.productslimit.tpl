{if $element->getLimits()}
    <div class="products_limit products_filter_item">
        <div class="products_limit_title products_filter_label">
            {translations name="categories.display_on_page"}
        </div>
        <select class="products_limit_dropdown dropdown_placeholder products_filter_dropdown">
            {foreach $element->getLimits() as $limitValue => $limit}
                <option value="{$limit.url}"{if $limit.selected} selected="selected"{/if}>
                    {$limitValue}
                </option>
            {/foreach}
        </select>
    </div>
{/if}