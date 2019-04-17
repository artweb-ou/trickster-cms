<div class="products_filter_item">
	<div class="products_filter_label">
		{$filter->getTitle()}:
	</div>
	<select class="products_filter_dropdown products_filter_dropdown_type_{$filter->getType()} dropdown_placeholder">
		<option value=''>{translations name="products.filter_select"}</option>
		{foreach $filter->getOptionsInfo() as $optionInfo}
			<option value="{$optionInfo.id}"{if $optionInfo.selected} selected="selected"{/if}>
				{$optionInfo.title}
			</option>
		{/foreach}
	</select>
</div>