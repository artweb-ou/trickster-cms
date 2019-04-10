
<form action="{$currentElement->getFiltrationUrl()}" class="panel_component catalogue_masseditor filtration_form" method="post" enctype="multipart/form-data">
	<div class="panel_heading">
		{translations name='catalogue.mass_edit'}
	</div>
	<div class="panel_content filtration_sections">
		<div class="filtration_section filtration_form_items">
			<div class="filtration_section_title">
				{translations name='catalogue.connections'}
			</div>
			<label class="filtration_form_item">
				<span class="filtration_form_item_label">
					{translations name='catalogue.set_categories'}
				</span>
				<span class="filtration_form_item_field">
					<select class="select_multiple catalogue_masseditor_categoryselect" multiple='multiple' name="{$formNames.newCategories}[]" autocomplete='off'>
						<option value=''></option>
					</select>
				</span>
			</label>
			<label class="filtration_form_item">
				<span class="filtration_form_item_label">
					{translations name='catalogue.set_brand'}
				</span>
				<span class="filtration_form_item_field">
					<select class="catalogue_masseditor_brandselect" name="{$formNames.newBrand}" autocomplete='off'>
						<option value=''></option>
					</select>
				</span>
			</label>
			<label class="filtration_form_item">
				<span class="filtration_form_item_label">
					{translations name='catalogue.set_discounts'}
				</span>
				<span class="filtration_form_item_field">
					<select class="select_multiple catalogue_masseditor_discountselect" multiple='multiple' name="{$formNames.newDiscounts}[]" autocomplete='off'>
						<option value=''></option>
					</select>
				</span>
			</label>
		</div>
		<div class="filtration_section filtration_form_items">
			<div class="filtration_section_title">
				{translations name='catalogue.edit_fields'}
			</div>
			<label class="filtration_form_item">
				<span class="filtration_form_item_label">
					{translations name='catalogue.productprices_multiply'}
				</span>
				<span class="filtration_form_item_field">
					<input class="input_component" type="text" value="{$formData.productPriceMultiplier}" name="{$formNames.productPriceMultiplier}" />
				</span>
			</label>
			<label class="filtration_form_item">
				<span class="filtration_form_item_label">
					{translations name='catalogue.productprices_add'}
				</span>
				<span class="filtration_form_item_field">
					<input class="input_component" type="text" value="{$formData.productPriceAddition}" name="{$formNames.productPriceAddition}" />
				</span>
			</label>
		</div>
		<div class="filtration_section filtration_form_items">
			<div class="filtration_section_title">
				{translations name='catalogue.action'}
			</div>
			<label class="filtration_form_item">
				<span class="filtration_form_item_label">
					{translations name='catalogue.massedit_method'}
				</span>
				<span class="filtration_form_item_field">
					<select class="dropdown_placeholder" multiple='multiple' name="{$formNames.massEditMethod}" autocomplete='off'>
						<option value='replace'{if $element->massEditMethod == 'replace'} selected="selected"{/if}>{translations name='catalogue.massedit_method_replace'}</option>
						<option value='add'{if $element->massEditMethod == 'add' || !$element->massEditMethod} selected="selected"{/if}>{translations name='catalogue.massedit_method_add'}</option>
					</select>
				</span>
			</label>
		</div>
		<div class="filtration_section filtration_form_items">
			<label class="filtration_form_item">
				<span class="filtration_form_item_label">
					{translations name='catalogue.massedit_all_results'}
				</span>
				<span class="filtration_form_item_field">
					<input class='catalogue_masseditor_targetall_checkbox checkbox_placeholder' type="checkbox" value="1" name="{$formNames.targetAll}"{if $element->targetAll} checked="checked"{/if} id="catalogue_masseditor_targetall_checkbox" />
				</span>
			</label>
		</div>
	</div>
	<div class="panel_controls">
		<input type="hidden" name="action" value="massModify" />
		<input type="hidden" name="id" value="{$currentElement->id}" />
		<input class="catalogue_masseditor_targets_input" type="hidden" name="{$formNames.targets}" value="" />
		<button type="submit" class="button primary_button">
			<span>{translations name="catalogue.mass_modify"}</span>
		</button>
	</div>
</form>