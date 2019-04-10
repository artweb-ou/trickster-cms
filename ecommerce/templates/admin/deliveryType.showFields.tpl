{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="form_component deliverytype_form" method="post" enctype="multipart/form-data">
	<div class="deliverytype_form_fields_component">
		<table class="form_table deliverytype_form_fields_table">
			<tbody class="deliverytype_form_fields_rows">
			<tr>
				<th>
					{translations name="deliverytype.field_name"}
				</th>
				<th>
					{translations name="deliverytype.active"}
				</th>
			</tr>
			{foreach from=$element->fieldsList item=field}
				{if $field->selected}
					<tr class="fields_row fields_row_id_{$field->id}">
						<td>
							{$field->title}
						</td>
						<td>
							<input class='fields_required checkbox_placeholder' type="checkbox" value="1" {if $field->required}checked="checked"{/if}/>
							<input class='fields_hidden' type="hidden" name="{$formNames.fields}[{$field->id}]" value="{$field->required}" />
						</td>
						<td>
							<a class="fields_row_remove icon icon_delete" href="" title="{translations name="deliverytype.remove"}"></a>
						</td>
					</tr>
				{/if}
			{/foreach}
			<tr class="deliverytype_form_separator">
				<td colspan="3">
					<hr />
				</td>
			</tr>
			<tr class="fields_new">
				<td>
					<select class="dropdown_placeholder fields_new_selector">
						{foreach from=$element->fieldsList item=field}
							<option value="{$field->id}" {if $field->selected}selected="selected"{/if}>{$field->title}</option>
						{/foreach}
					</select>
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="input"}
				</td>
				<td>
					<input class='fields_new_field checkbox_placeholder' name="{$formNames.fields}" type="checkbox" value="0" />
					{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="fields"}
				</td>
				<td>
					<a class="fields_new_add button primary_button" href="">{translations name="deliverytype.add"}</a>
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	{include file=$theme->template('component.controls.tpl') action="receiveFields"}
</form>