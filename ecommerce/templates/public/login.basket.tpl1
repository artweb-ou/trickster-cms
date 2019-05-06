{assign var='formData' value=$element->getFormData()}
{assign var='formErrors' value=$element->getFormErrors()}
{assign var='formNames' value=$element->getFormNames()}
{capture assign="moduleContent"}
	{if $element->title}
		{capture assign='moduleTitle'}
			{$element->title}
		{/capture}
	{/if}

	{if !$element->displayForm()}
		<div class='login_basket_msg'>
			{if $element->getUserDataForm()}
				{translations name='login.welcome'}, <a href="{$element->getUserDataFormUrl()}">{$currentUser->getName()}</a>
				{else}
				{translations name='login.welcome'}, <span>{$currentUser->getName()}</span>
			{/if}
		</div>

		<a class='button login_basket_logout' href='{$element->URL}id:{$element->id}/action:logout'>{translations name='login.logout'}</a>
	{else}
		<form class="login_basket_form" action="{$controller->fullURL}" class='login_form' method="post" enctype="multipart/form-data" role="form">
			<table class="form_table">
				<tr{if $formErrors.userName} class="form_error"{/if}>
					<td class="form_label">
						{translations name='login.email'}:
					</td>
					<td class="form_star">*</td>
					<td class="form_field">
						<input class="input_component" type="text" value="" name="{$formNames.userName}" placeholder=""/>
					</td>
					<td class="form_extra"></td>
				</tr>
				<tr{if $formErrors.password} class="form_error"{/if}>
					<td class="form_label">
						{translations name='login.password'}:
					</td>
					<td class="form_star">*</td>
					<td class="form_field">
						<input class="input_component{if $formErrors.userName} form_error{/if}" type="password" value="" name="{$formNames.password}" placeholder="{translations name='login.password'}"/>
					</td>
					<td class="form_extra"></td>
				</tr>
			</table>

			<button class="login_basket_button button">{translations name='login.submit'}</button>
			<input type="hidden" value="{$element->id}" name="id"/>
			<input type="hidden" value="login" name="action"/>
		</form>
	{/if}
{/capture}

{assign moduleClass "login_basket"}
{assign moduleContentClass "login_contents login_basket_content"}

{include file=$theme->template("component.contentmodule.tpl")}