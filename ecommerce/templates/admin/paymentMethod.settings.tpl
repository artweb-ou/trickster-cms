{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
<form action="{$element->getFormActionURL()}" class="form_component" method="post" enctype="multipart/form-data">
	<table class='form_table'>
		{foreach $element->getSpecialFields() as $field}
			{assign "fieldName" $field@key}

			{if isset($field.hidden) && $field.hidden}
				{continue}
			{/if}

			{if isset($field.multiLanguage) && $field.multiLanguage}
				{if $field.format == "text"}
					{foreach $formData.$fieldName as $languageField}
						<tr{if $formErrors.$fieldName.{$languageField@key}} class="form_error"{/if}>
							<td class="form_label">
								{translations name='paymentmethod.'|cat:$fieldName} ({$languageNames.{$languageField@key}}):
							</td>
							<td colspan='2'>
								<input class='input_component' type="text" value="{$languageField}" name="{$formNames.$fieldName.{$languageField@key}}" />
							</td>
						</tr>
					{/foreach}
				{/if}
			{else}
				<tr{if $formErrors.$fieldName} class="form_error"{/if}>
					<td class="form_label">
						{translations name='paymentmethod.'|cat:$fieldName}:
					</td>
					<td class="form_field">
						{if $field.format == "text"}
							<input class="input_component" type="text" value="{$formData.$fieldName}" name="{$formNames.$fieldName}" />
						{elseif $field.format == "file"}
								<input class="fileinput_placeholder" type="file" name="{$formNames.$fieldName}"/>
								{if $element->$fieldName}
									<div class="file_container">
										<span>{$formData.$fieldName}</span>
										<a class="button file_delete_button warning_button" href="{$element->URL}id:{$element->id}/action:deleteFile/file:{$fieldName}">
											<span class="icon icon_delete"></span>
											{translations name="$fieldName.deletefile"}
										</a>
									</div>
								{/if}
							{/if}
						{/if}
					</td>
				</tr>
		{/foreach}
	</table>
	{include file=$theme->template('component.controls.tpl') action="receivePaymentSettings"}
</form>