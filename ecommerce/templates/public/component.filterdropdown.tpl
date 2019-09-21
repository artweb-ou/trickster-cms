{if empty($titleType)}{$titleType = 'label'}{/if}
<div class="products_filter_item products_filter_{$filter->getId()} {if !empty($class)}{$class}{/if}">
	{if $titleType == 'label'}
		<div class="products_filter_label">{$filter->getTitle()}:</div>
	{/if}
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
</div>