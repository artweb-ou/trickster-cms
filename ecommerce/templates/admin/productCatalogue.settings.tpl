{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="form_component productcatalogue_form" method="post" enctype="multipart/form-data">
	<table class="form_table">
		<tr>
			<td class="form_label">
				{translations name='field.productparameters'}:
			</td>
			<td>
				<select class="select_multiple" name="{$formNames.parameters}[]" multiple="multiple">
					<option value=""></option>
					{foreach from=$element->allParametersGroups item=group}
						<optgroup label="{$group->title}">
							{foreach from=$group->getParametersList() item=parameterInfo}
								<option value="{$parameterInfo->id}" {if $parameterInfo->selected}selected='selected'{/if}>{$parameterInfo->title}</option>
							{/foreach}
						</optgroup>
					{/foreach}
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="parameters"}
			</td>
		</tr>

		<tr>
			<td class="form_label">
				{translations name='category.defaultorder'}:
			</td>
			<td>
				<select class="dropdown_placeholder" name="{$formNames.defaultOrder}">
					<option value='price' {if $formData.defaultOrder=='price'}selected='selected'{/if}>
						{translations name='category.pricesorting'}
					</option>
					<option value='price;desc' {if $formData.defaultOrder=='price;desc'}selected='selected'{/if}>
						{translations name='category.pricesorting_descending'}
					</option>
					<option value='title' {if $formData.defaultOrder=='title'}selected='selected'{/if}>
						{translations name='category.titlesorting'}
					</option>
					<option value='title;desc' {if $formData.defaultOrder=='title;desc'}selected='selected'{/if}>
						{translations name='category.titlesorting_descending'}
					</option>
					<option value='brand;asc' {if $formData.defaultOrder=='brand;asc'}selected='selected'{/if}>
						{translations name='category.brandsorting'}
					</option>
					<option value='brand;desc' {if $formData.defaultOrder=='brand;desc'}selected='selected'{/if}>
						{translations name='category.brandsorting_descending'}
					</option>
					<option value='date;asc' {if $formData.defaultOrder=='date;asc'}selected='selected'{/if}>
						{translations name='category.datesorting'}
					</option>
					<option value='date;desc' {if $formData.defaultOrder=='date;desc'}selected='selected'{/if}>
						{translations name='category.datesorting_descending'}
					</option>
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="defaultOrder"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='category.manualsortingenabled'}:
			</td>
			<td>
				<select class="dropdown_placeholder" name="{$formNames.manualSortingEnabled}">
					<option value='1' {if $formData.manualSortingEnabled=='1'}selected='selected'{/if}>{translations name='category.enabled'}</option>
					<option value='2' {if $formData.manualSortingEnabled=='2'}selected='selected'{/if}>{translations name='category.disabled'}</option>
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="manualSortingEnabled"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='category.pricesortingenabled'}:
			</td>
			<td>
				<select class="dropdown_placeholder" name="{$formNames.priceSortingEnabled}">
					<option value='1' {if $formData.priceSortingEnabled=='1'}selected='selected'{/if}>{translations name='category.enabled'}</option>
					<option value='2' {if $formData.priceSortingEnabled=='2'}selected='selected'{/if}>{translations name='category.disabled'}</option>
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="priceSortingEnabled"}
			</td>
		</tr>

		<tr>
			<td class="form_label">
				{translations name='category.namesortingenabled'}:
			</td>
			<td>
				<select class="dropdown_placeholder" name="{$formNames.nameSortingEnabled}">
					<option value='1' {if $formData.nameSortingEnabled=='1'}selected='selected'{/if}>{translations name='category.enabled'}</option>
					<option value='2' {if $formData.nameSortingEnabled=='2'}selected='selected'{/if}>{translations name='category.disabled'}</option>
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="nameSortingEnabled"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='category.dateSortingEnabled'}:
			</td>
			<td>
				<select class="dropdown_placeholder" name="{$formNames.dateSortingEnabled}">
					<option value='1' {if $formData.dateSortingEnabled=='1'}selected='selected'{/if}>{translations name='category.enabled'}</option>
					<option value='2' {if $formData.dateSortingEnabled=='2'}selected='selected'{/if}>{translations name='category.disabled'}</option>
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="dateSortingEnabled"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='category.brandfilterenabled'}:
			</td>
			<td>
				<select class="dropdown_placeholder" name="{$formNames.brandFilterEnabled}">
					<option value='1' {if $formData.brandFilterEnabled=='1'}selected='selected'{/if}>{translations name='category.enabled'}</option>
					<option value='2' {if $formData.brandFilterEnabled=='2'}selected='selected'{/if}>{translations name='category.disabled'}</option>
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="brandFilterEnabled"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='category.parameterfilterenabled'}:
			</td>
			<td>

				<select class="dropdown_placeholder" name="{$formNames.parameterFilterEnabled}">
					<option value='1' {if $formData.parameterFilterEnabled=='1'}selected='selected'{/if}>{translations name='category.enabled'}</option>
					<option value='2' {if $formData.parameterFilterEnabled=='2'}selected='selected'{/if}>{translations name='category.disabled'}</option>
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="parameterFilterEnabled"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='category.discountfilterenabled'}:
			</td>
			<td>
				<select class="dropdown_placeholder" name="{$formNames.discountFilterEnabled}">
					<option value='1' {if $formData.discountFilterEnabled=='1'}selected='selected'{/if}>{translations name='category.enabled'}</option>
					<option value='2' {if $formData.discountFilterEnabled=='2'}selected='selected'{/if}>{translations name='category.disabled'}</option>
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="discountFilterEnabled"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='category.availabilityfilterenabled'}:
			</td>
			<td>
				<select class="dropdown_placeholder" name="{$formNames.availabilityFilterEnabled}">
					<option value='1' {if $formData.availabilityFilterEnabled=='1'}selected='selected'{/if}>{translations name='category.enabled'}</option>
					<option value='2' {if $formData.availabilityFilterEnabled=='2'}selected='selected'{/if}>{translations name='category.disabled'}</option>
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="availabilityFilterEnabled"}
			</td>
		</tr>
		<tr>
			<td class="form_label">
				{translations name='category.amount_on_page_enabled'}:
			</td>
			<td>
				<select class="dropdown_placeholder" name="{$formNames.amountOnPageEnabled}">
					<option value='1' {if $formData.amountOnPageEnabled=='1'}selected='selected'{/if}>{translations name='category.enabled'}</option>
					<option value='2' {if $formData.amountOnPageEnabled=='2'}selected='selected'{/if}>{translations name='category.disabled'}</option>
				</select>
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="amountOnPageEnabled"}
			</td>
		</tr>
	</table>
	{include file=$theme->template('component.controls.tpl') action="receiveSettingsForm"}
</form>
