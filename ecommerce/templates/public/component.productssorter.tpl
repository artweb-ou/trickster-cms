{if empty($titleType)}{$titleType = 'label'}{/if}
<div class="products_sorter products_filter_item {if !empty($class)}{$class}{/if}">
	{if $titleType === 'label'}
	    <div class="products_sorter_label products_filter_label">{translations name='products.sorting'}</div>
	{/if}
	<select class="dropdown_placeholder products_filter_dropdown">
		{if $titleType == 'option'}
			<option value=''>{translations name='products.sorting'}</option>
		{/if}
		{foreach $element->getSortingOptions() as $sortingOption}
			<option value="{$sortingOption.value}"{if $sortingOption.active} selected="selected"{/if}>
				{$sortingOption.label}
			</option>
		{/foreach}
	</select>
</div>