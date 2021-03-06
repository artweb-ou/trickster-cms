{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}

{capture assign="moduleContent"}
	<form action="{$currentElement->getFormActionURL()}" class='passwordreminder_form' method="post" enctype="multipart/form-data" role="form">
		{if $element->resultMessage == ''}
		<div class='passwordreminder_contents'>
			<div class='html_content'>
				{$element->content}
			</div>
		</div>
		{/if}
		<div class='passwordreminder_form_block'>
			{if $element->resultMessage != ''}
				<div class='html_content passwordreminder_form_msg_success'>
					{$element->resultMessage}
				</div>
			{/if}
			{if $element->errorMessage != ''}
				<div class='html_content form_error' role="alert">
					{$element->errorMessage}
				</div>
			{/if}
			{if $element->resultMessage == ''}
			<table class='form_table'>
				<tr class='{if $formErrors.email} form_error{/if}'>
					<td class='form_label'>
						{translations name='passwordreminder.email'}:
					</td>
					<td class='form_star'>*</td>
					<td class='form_field'>
						<input class="input_component" type="text" value="{$formData.email}" name="{$formNames.email}" />
					</td>
					<td class='form_extra'></td>
				</tr>
			</table>
			<div class='form_controls'>
				<table class='form_table'>
					<tr class=''>
						<td class='form_label'></td>
						<td class='form_star'></td>
						<td class='form_field'>
							<span tabindex="0" class="button passwordreminder_submit"><span class='button_text'>{translations name='passwordreminder.send'}</span></span>
						</td>
						<td class='form_extra'></td>
					</tr>
				</table>
			</div>
			{/if}
			<input type="hidden" value="{$element->id}" name="id" />
			<input type="hidden" value="submit" name="action" />
		</div>
	</form>
{/capture}

{assign moduleTitle $element->title}
{assign moduleClass "passwordreminder_block"}

{include file=$theme->template("component.contentmodule.tpl")}
