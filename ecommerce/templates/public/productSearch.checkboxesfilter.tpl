<div class="productsearch_field products_filter_checkboxes products_filter_type_{$filter->getType()}">
	<div class="productsearch_field_label">
		{$filter->getTitle()}:
		<div class="products_filter_checkboxes_icon"></div>
	</div>
	<div class="productsearch_field_controls">
		{foreach $filter->getOptionsInfo() as $optionInfo}
			<div class="productsearch_field_checkbox products_filter_checkboxes_option">
				<input type="checkbox" class="products_filter_checkbox checkbox_placeholder" value="{$optionInfo.id}"{if $optionInfo.selected} checked="checked"{/if} id="products_filter_checkbox_{$optionInfo.id}"/>
				<label class="productsearch_field_checkbox_label products_filter_checkbox_label" for="products_filter_checkbox_{$optionInfo.id}">{$optionInfo.title}</label>
			</div>
		{/foreach}
	</div>
</div>