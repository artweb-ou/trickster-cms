{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="form_component deliverytype_form" method="post" enctype="multipart/form-data">
	<div class="deliverytype_form_fields_component">
		<table class="form_table deliverytype_form_fields_table">
			<tr class="fields_new">
				<td>
					<select class="dropdown_placeholder fields_new_selector" name='{$formNames.fieldId}'>
						{foreach from=$fieldsList item=field}
							<option value="{$field->id}" {if $field->selected}selected="selected"{/if}>{$field->title}</option>
						{/foreach}
					</select>
				</td>
				<td>
					<input class="input_component" type="text" value="{$formData.value}" name="{$formNames.value}" />
				</td>
			</tr>
			</tbody>
		</table>
	</div>
	{include file=$theme->template('block.controls.tpl') action="receive"}
</form>