<table class="form_table">
	{foreach from=$formData.title key=languageId item=title}
	<tr{if $formErrors.title.$languageId} class="form_error"{/if}>
		<td class="form_label">
			{translations name='field.heading'} ({$languageNames.$languageId})
		</td>
		<td>
			<input class="input_component" type="text" value="{$title}" name="{$formNames.title.$languageId}" />
			{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="title"}
		</td>
	</tr>
	{/foreach}
	<tr{if $formErrors.image} class="form_error"{/if}>
		<td class="form_label">
			{translations name='field.image'}:
		</td>
		<td>
			{if $element->originalName != ""}
				<img src='{$controller->baseURL}image/type:adminImage/id:{$element->image}/filename:{$element->originalName}' />
				<a href="{$element->URL}id:{$element->id}/action:deleteImage" >{translations name='label.deleteimage'}</a>
			{/if}
			<input class="fileinput_placeholder" type="file" name="{$formNames.image}" />
		</td>
	</tr>
	{foreach from=$formData.introduction key=languageId item=content}
	<tr{if $formErrors.introduction.$languageId} class="form_error"{/if}>
		<td class="form_label">
			{translations name='field.introduction'} ({$languageNames.$languageId}):
		</td>
		<td>
			{include file=$theme->template('component.htmleditor.tpl') data=$content name=$formNames.introduction.$languageId}
		</td>
		<td class="form_helper_cell">{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="introduction"}
		</td>
	</tr>
	{/foreach}
	{foreach from=$formData.content key=languageId item=content}
	<tr{if $formErrors.content.$languageId} class="form_error"{/if}>
		<td class="form_label">
			{translations name='field.content'} ({$languageNames.$languageId}):
		</td>
		<td>
			{include file=$theme->template('component.htmleditor.tpl') data=$content name=$formNames.content.$languageId}
		</td>
		<td class="form_helper_cell">{include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="content"}
		</td>
	</tr>
	{/foreach}
	<tr>
		<td class="form_label">
			{translations name='field.hidden'}:
		</td>
		<td>
			<input class="checkbox_placeholder" type="checkbox" value="1" name="{$formNames.hidden}" {if $formData.hidden == '1'}checked="checked"{/if} />
		</td>
		<td>

		</td>
	</tr>
</table>
{include file=$theme->template('component.controls.tpl')}