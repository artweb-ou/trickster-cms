{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
{assign 'connectedCategories' $element->getConnectedCategories()}
{assign 'connectedBrands' $element->getConnectedBrands()}
{assign 'connectedDiscounts' $element->getConnectedDiscounts()}
{assign 'connectedProductSelectionIds' $element->getConnectedProductSelectionIds()}
{assign 'parameters' $element->getProductSelectionParameters()}

<form class="selectedproducts_form form_component" action="{$element->getFormActionURL()}" method="post" enctype="multipart/form-data">

	<table class='form_table'>
		<tr>
			<td class="form_label">
				{translations name='selectedproducts.catalogue'}:
			</td>
			<td>
				<select class="dropdown_placeholder" name="{$formNames.catalogueFilterId}">
					{foreach $productCataloguesInfo as $productCatalogueInfo}
						<option value='{$productCatalogueInfo.id}'{if $productCatalogueInfo.linkExists} selected='selected'{/if}>
							{$productCatalogueInfo.title}
						</option>
					{/foreach}
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="catalogueFilterId"}
			</td>
		</tr>


		<tr>
			<td class="form_label">
				{translations name='selectedproducts.filter_category'}:
			</td>
			<td>
				<input class='checkbox_placeholder' type="checkbox" value="1" name="{$formNames.filterCategory}"{if $element->filterCategory} checked="checked"{/if}/>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="filterCategory"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='selectedproducts.filter_brand'}:
			</td>
			<td>
				<input class='checkbox_placeholder' type="checkbox" value="1" name="{$formNames.filterBrand}"{if $element->filterBrand} checked="checked"{/if}/>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="filterBrand"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='selectedproducts.filter_discount'}:
			</td>
			<td>
				<input class='checkbox_placeholder' type="checkbox" value="1" name="{$formNames.filterDiscount}"{if $element->filterDiscount} checked="checked"{/if}/>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="filterDiscount"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='selectedproducts.filter_availability'}:
			</td>
			<td>
				<input class='checkbox_placeholder' type="checkbox" value="1" name="{$formNames.availabilityFilterEnabled}"{if $element->availabilityFilterEnabled} checked="checked"{/if}/>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="availabilityFilterEnabled"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='selectedproducts.parameters'}:
			</td>
			<td>
				{assign 'parameters' $element->getConnectedParameters()}
				<select class="select_multiple selectedproducts_form_parameters" multiple='multiple' name="{$formNames.parametersIds}[]" autocomplete='off'>
					<option value=''></option>
					{foreach $parameters as $parameter}
						<option value="{$parameter->id}" selected="selected">
							{$parameter->title}
						</option>
					{/foreach}
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="parametersIds"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='selectedproducts.filter_price'}:
			</td>
			<td>
				<input class='checkbox_placeholder' type="checkbox" value="1" name="{$formNames.filterPriceEnabled}"{if $element->filterPriceEnabled} checked="checked"{/if}/>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="filterPriceEnabled"}
			</td>
		</tr>
		<tr class="form_label"{if $formErrors.priceInterval} class="form_error"{/if}>
			<td>
				{translations name='selectedproducts.price_interval'}:
			</td>
			<td>
				<input class="input_component" type="text" value="{$formData.priceInterval}" name="{$formNames.priceInterval}" />
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="priceInterval"}
			</td>
		</tr>

	</table>

	{include file=$theme->template('component.controls.tpl') action="receiveFilters"}
</form>
