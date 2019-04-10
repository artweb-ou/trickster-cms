<form class="panel_component catalogue_filter filtration_form" action="{$element->getFormActionURL()}" method="GET" enctype="multipart/form-data">
	<div class="panel_heading">
		{translations name='catalogue.filter'}
	</div>
	<div class="panel_content filtration_form_items">
		<label class="filtration_form_item">
			<span class="filtration_form_item_label">
				{translations name='catalogue.filter_category'}:
			</span>
			<span class="filtration_form_item_field">
				<select class="select_multiple catalogue_filter_categoryselect" multiple='multiple' name="category[]" autocomplete='off'>
					<option value=''></option>
					{foreach $filterCategories as $filterElement}
						<option value="{$filterElement->id}" selected="selected">
							{$filterElement->getTitle()}
						</option>
					{/foreach}
				</select>
			</span>
		</label>
		<label class="filtration_form_item">
			<span class="filtration_form_item_label">
				{translations name='catalogue.filter_brand'}:
			</span>
			<span class="filtration_form_item_field">
				<select class="select_multiple catalogue_filter_brandselect" multiple='multiple' name="brand[]" autocomplete='off'>
					<option value=''></option>
					{foreach $filterBrands as $filterElement}
						<option value="{$filterElement->id}" selected="selected">
							{$filterElement->getTitle()}
						</option>
					{/foreach}
				</select>
			</span>
		</label>
		<label class="filtration_form_item">
			<span class="filtration_form_item_label">
				{translations name='catalogue.filter_discount'}:
			</span>
			<span class="filtration_form_item_field">
				<select class="select_multiple catalogue_filter_discountselect" multiple='multiple' name="discount[]" autocomplete='off'>
					<option value=''></option>
					{foreach $filterDiscounts as $filterElement}
						<option value="{$filterElement->id}" selected="selected">
							{$filterElement->getTitle()}
						</option>
					{/foreach}
				</select>
			</span>
		</label>
	</div>
	<div class="panel_controls">
		<input type="hidden" value="1" name="filter" />
		<button type="submit" class="button primary_button">
			{translations name='catalogue.filter_submit'}
		</button>
		{if $controller->getParameter('filter')}
			<a class="button warning_button" href="{$element->URL}">
				{translations name='catalogue.filter_clear'}
			</a>
		{/if}
	</div>
</form>