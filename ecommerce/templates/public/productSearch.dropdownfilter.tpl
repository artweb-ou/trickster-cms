<div class="productsearch_field products_filter_dropdown">
	<select class="products_filter_dropdown_dropdown products_filter_dropdown_type_{$filter->getType()} dropdown_placeholder">
		{if $filter->titleEnabled}
			<option value=''>{$filter->getTitle()}</option>
		{/if}
		{foreach $filter->getOptionsInfo() as $optionInfo}
			<option value="{$optionInfo.id}"{if $optionInfo.selected} selected="selected"{/if}>
				{$optionInfo.title}
			</option>
		{/foreach}
	</select>
</div>