<div class="products_filter_item">
	<div class="products_filter_label">
		{$filter->getTitle()}:
	</div>
	<div class="products_filter_radios">
		<div class="products_filter_radio">
			<input id="filter{$filter->getSelectionId()}" type="radio" name="products_filter_radio[{$filter->getSelectionId()}]" class="radio_holder products_filter_radio products_filter_radio_type_{$filter->getType()}" value="" checked="checked" />
			<label for="filter{$filter->getSelectionId()}">{translations name="products.filter_select_all"}</label>
		</div>
		{foreach $filter->getOptionsInfo() as $optionInfo}
			<div class="products_filter_radio">
				<input id="{$optionInfo.id}" type="radio" name="products_filter_radio[{$filter->getSelectionId()}]" class="radio_holder products_filter_radio products_filter_radio_type_{$filter->getType()}" value="{$optionInfo.id}"{if $optionInfo.selected} checked="checked"{/if} />
				<label for="{$optionInfo.id}">{$optionInfo.title}</label>
			</div>
		{/foreach}
	</div>
</div>