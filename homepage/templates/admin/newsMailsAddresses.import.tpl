{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}


<form class="productimport_form" action="{$element->getFormActionURL()}" method="post" enctype="multipart/form-data">
	<table class="form_table">
		<input type="hidden" value="{$element->id}" name="id" />
		<input type="hidden" value="receive" name="action" />
		<tr>
			<td class="form_label">
				{translations name='newsMailsAddresses.groups'}:
			</td>
			<td>
				<select class="select_multiple" multiple name="{$formNames.groupId}[]">
					{foreach from=$element->getGroups() item=group}
						<option value="{$group->id}"{if in_array($group->id, $formData.groupId)} selected="selected"{/if}>{$group->title}</option>
					{/foreach}
				</select>
			</td>
		</tr>
		<tr{if $formErrors.delimiter} class="form_error"{/if}>
			<td class="form_label">
				{translations name='newsMailsAddresses.delimiter'}:
			</td>
			<td>
				<input class='input_component' type="text" value="{$formData.delimiter}" name="{$formNames.delimiter}" />
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="delimiter"}
			</td>
		</tr>
		<tr{if $formErrors.importFile} class="form_error"{/if}>
			<td class="form_label">
				{translations name='newsMailsAddresses.csvupload'}:
			</td>
			<td>
				<input class="fileinput_placeholder" type="file" name="{$formNames.importFile}" />
			</td>
		</tr>
	</table>

	<div class="controls_block form_controls">
		<input class="button button_green" type="submit" value="{translations name='import.import'}" />
		<input type="hidden" value="{$element->id}" name="id" />
		<input type="hidden" value="import" name="action" />
	</div>

</form>
