{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}

{if $element->title}
	{capture assign='moduleTitle'}
		{$element->title}
	{/capture}
{/if}

{capture assign="moduleContent"}
	<div class="registration_basket_description html_content">
		{$element->content}
	</div>
	<form action="{$currentElement->getFormActionURL()}" class='registration_basket_form' method="post" enctype="multipart/form-data" role="form">
		{if $element->errorMessage != ''}
			<div class='form_error_message' role="alert">
				{$element->errorMessage}
			</div>
		{/if}

		{if $element->resultMessage == ''}

		<table class='form_table'>
			{foreach $element->getConnectedFields() as $field}
				<tr class='{if $element->getDynamicFieldError($field->id)} form_error{/if}'>
					<td class='form_label'>
						{$field->title}:
					</td>
					<td class='form_star'>{if $field->required}*{/if}</td>
					<td class='form_field'>
						<input class='input_component' type="{$field->getInputType()}" value="{$element->getFieldValue($field->id)}" name="{$formNames.dynamicFieldsData}[{$field->id}]"/>
					</td>
					<td class='form_extra'></td>
				</tr>
			{/foreach}

			<tr>
				<td class='form_empty' colspan='3'></td>
			</tr>
			<tr class='{if $formErrors.subscribe} form_error{/if}'>
				<td class="form_label"></td>
				<td class="form_star"></td>
				<td class='form_field'>
					<input type="checkbox" class="checkbox_placeholder" name="{$formNames.subscribe}" value="1" {if $formData.subscribe == "1"}checked="checked"{/if} id="checkbox_subscribe"/>
					<label for="checkbox_subscribe">{translations name='registration.form_subscribe'}</label>
				</td>
				<td class='form_extra'></td>
			</tr>
			<tr>
				<td></td>
				<td></td>
				<td>
					<div class='form_controls'>
						<button class="button registration_form_submit" type="submit">
							<span class='button_text'>{if $element->type == 'userdata'}{translations name='registration.form_save'}{else}{translations name='registration.form_register'}{/if}</span>
						</button>
					</div>
				</td>
			</tr>
		</table>
		{/if}
		<input type="hidden" value="{$element->id}" name="id" />
		<input type="hidden" value="submit" name="action" />
	</form>
{/capture}

{assign moduleClass 'registration_basket'}

{include file=$theme->template('component.contentmodule.tpl')}
