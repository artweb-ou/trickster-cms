{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="form_component" method="post" enctype="multipart/form-data">
	<table class="form_table">
		{foreach from=$formData.introduction key=languageId item=content}
			<tr {if $formErrors.introduction.$languageId}class="form_error"{/if}>
				<td class="form_label">
					{translations name='shop.introduction'} ({$languageNames.$languageId}):
				</td>
				<td>
					{include file=$theme->template('component.htmleditor.tpl') data=$content name=$formNames.introduction.$languageId}
				</td>
			</tr>
		{/foreach}
		{foreach from=$formData.content key=languageId item=content}
			<tr {if $formErrors.content.$languageId}class="form_error"{/if}>
				<td class="form_label">
					{translations name='shop.content'} ({$languageNames.$languageId}):
				</td>
				<td>
					{include file=$theme->template('component.htmleditor.tpl') data=$content name=$formNames.content.$languageId}
				</td>
			</tr>
		{/foreach}
		{foreach from=$formData.contactInfo key=languageId item=contactInfo}
			<tr {if $formErrors.contactInfo.$languageId}class="form_error"{/if}>
				<td class="form_label">
					{translations name='shop.contactinfo'} ({$languageNames.$languageId}):
				</td>
				<td>
					{include file=$theme->template('component.htmleditor.tpl') data=$contactInfo name=$formNames.contactInfo.$languageId}
				</td>
			</tr>
		{/foreach}
	</table>
	{include file=$theme->template('component.controls.tpl') action="receiveTexts"}
</form>