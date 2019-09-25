{if empty($titleType)}{$titleType = 'label'}{/if}
{if empty($selectorType)}{$selectorType = 'dropdown'}{/if}
<div class="products_filter_item products_filter_{$filter->getId()} {if !empty($class)}{$class}{/if}">
	{if $titleType === 'label'}
		<div class="products_filter_label">{$filter->getTitle()}:</div>
	{/if}
	{if $selectorType == 'checkbox'}
	    {foreach $filter->getOptionsInfo() as $optionInfo}
			<div class="productsearch_field_checkbox products_filter_checkboxes_option">
				<input type="checkbox" class="products_filter_checkbox checkbox_placeholder" value="{$optionInfo.id}"{if $optionInfo.selected} checked="checked"{/if} id="products_filter_checkbox_{$optionInfo.id}"/>
				<label class="productsearch_field_checkbox_label products_filter_checkbox_label" for="products_filter_checkbox_{$optionInfo.id}">{$optionInfo.title}</label>
			</div>
		{/foreach}
	{elseif $selectorType == 'dropdown'}
        <select autocomplete="off" class="products_filter_dropdown products_filter_dropdown_type_{$filter->getType()} dropdown_placeholder">
            {if $titleType == 'option'}
                <option value=''>{$filter->getTitle()}</option>
            {else}
                <option value=''>{translations name="products.filter_select"}</option>
            {/if}
            {foreach $filter->getOptionsInfo() as $optionInfo}
                <option value="{$optionInfo.id}"{if $optionInfo.selected} selected="selected"{/if}>
                    {$optionInfo.title}
                </option>
            {/foreach}
        </select>
	{/if}
</div>