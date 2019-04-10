<div class="products_sorter products_filter_item">
	<div class="products_sorter_label products_filter_label">{translations name='products.sorting'}</div>
	<select class="dropdown_placeholder redirect_select products_filter_dropdown">
		{foreach $element->getSortingOptions() as $sortingOption}
			<option value="{$sortingOption.url}"{if $sortingOption.active} selected="selected"{/if}>
				{$sortingOption.label}
			</option>
		{/foreach}
	</select>
</div>