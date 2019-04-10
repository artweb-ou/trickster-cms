{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="form_component deliverytype_form" method="post" enctype="multipart/form-data">
	<div class="deliverytype_form_prices_component">
		<table class="form_table deliverytype_form_prices_table">
			<tbody class="deliverytype_form_prices_rows">
			<tr>
				<th>
					{translations name='deliverytype.allowed_region'}
				</th>
				<th colspan="2">
					{translations name='deliverytype.price'}
				</th>
			</tr>
			{foreach from=$element->getCountriesList() item=country}
				{if $country->selected}
					<tr class="prices_row prices_row_id_{$country->id}">
						<td>
							{$country->title}
						</td>
						<td>
							<input class='input_component' type="text" value="{$country->deliveryPrice}" name="{$formNames.prices}[{$country->id}]" />
							{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="prices"}
						</td>
						<td>
							<a class="prices_row_remove icon icon_delete" href="" title="{translations name="deliverytype.remove"}"></a>
						</td>
					</tr>
				{/if}
				{foreach from=$country->citiesList item=city}
					{if $city->selected}
						<tr class="prices_row prices_row_id_{$city->id}">
							<td>
								{$country->title} / {$city->title}
							</td>
							<td>
								<input class='input_component' type="text" value="{$city->deliveryPrice}" name="{$formNames.prices}[{$city->id}]" />
								{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="prices"}
							</td>
							<td>
								<a class="prices_row_remove icon icon_delete" href="" title="{translations name="deliverytype.remove"}"></a>
							</td>
						</tr>
					{/if}
				{/foreach}
			{/foreach}
			<tr class="deliverytype_form_separator">
				<td colspan="3">
					<hr />
				</td>
			</tr>
			<tr class="prices_new">
				<td>
					<select class="dropdown_placeholder prices_new_selector">
						{foreach $element->getCountriesList() as $country}
							{foreach from=$country->citiesList item=city}
								<option value="{$city->id}" {if $city->selected}selected="selected"{/if}>{$country->title} / {$city->title}</option>
								{foreachelse}
								<option value="{$country->id}" {if $country->selected}selected="selected"{/if}>{$country->title}</option>
							{/foreach}
						{/foreach}
					</select>
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="input"}
				</td>
				<td>
					<input class='prices_new_price input_component' type="text" value="0" name="{$formNames.prices}" />
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="prices"}
				</td>
				<td>
					<a class="prices_new_add button primary_button" href="">{translations name="deliverytype.add"}</a>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	{include file=$theme->template('component.controls.tpl') action="receivePrices"}
</form>