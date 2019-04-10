{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="form_component" method="post" enctype="multipart/form-data">
	<table class='product_variations_table'>
		{foreach from=$formData.title key=languageId item=title}
		<tr {if $formErrors.title.$languageId}class="form_error"{/if}>
			<td>
				{translations name='field.heading'} ({$languageNames.$languageId}):
			</td>
			<td>
				<input type="text" value="{$title}" name="{$formNames.title.$languageId}" />
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="title"}
			</td>
		</tr>
		{/foreach}
		<tr>
			<td>
				{translations name='field.color'}
			</td>
			<td>
				<input type="text" value="{$formData.color}" name="{$formNames.color}" />
				{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="color"}
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				<input type="submit" value='{translations name='button.save'}' />
				<input type="hidden" value="{$element->id}" name="id" />
				<input type="hidden" value="receive" name="action" />
			</td>
		</tr>
	</table>
</form>