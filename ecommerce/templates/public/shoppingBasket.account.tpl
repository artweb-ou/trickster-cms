{capture assign="moduleContent"}
	{if $element->title}
		{capture assign="moduleTitle"}
			{$element->title}
		{/capture}
	{/if}
	<div class='shoppingbasket_selection'>
		<div class="shoppingbasket_selection_message">
			{$element->shoppingBasket->getMessage()}
		</div>
		<div class="shoppingbasket_contents">
            {if $element->errorMessage != ""}
                <p class="shoppingbasket_selection_error form_error" role="alert">{$element->errorMessage}</p>
            {/if}
            {assign var='formData' value=$element->getFormData()}
            {assign var='formErrors' value=$element->getFormErrors()}
            {assign var='formNames' value=$element->getFormNames()}
			{include file=$theme->template('shoppingBasket.products.tpl') element=$element}

			<div class="shoppingbasket_account">
				<div class="shoppingbasket_account_login">
					{if $loginForm = $element->getLoginForm()}
						{include file=$theme->template('login.basket.tpl') element=$loginForm}
					{/if}
				</div>
				<div class="shoppingbasket_account_registration">
					{if $currentUser->userName === 'anonymous'}
						{if $registrationForm = $element->getRegistrationForm()}
							{include file=$theme->template('registration.basket.tpl') element=$registrationForm}
						{/if}
					{/if}
				</div>
				<div class="clearfix"></div>
			</div>

			{include file=$theme->template('shoppingBasket.totals.tpl')}
			{if $element->isAccountStepSkippable()}
				<div class='shoppingbasket_form_controls'>
					<div class="shoppingbasket_form_controls_container">
						<a class="button" href="{$element->URL}?step=delivery">
							{translations name='shoppingbasket.skip_account'}
						</a>
					</div>
				</div>
			{/if}
		</div>
	</div>
{/capture}
{assign moduleClass "shopping_basket_selection_block"}
{assign moduleTitleClass "shopping_basket_heading"}
{include file=$theme->template("component.contentmodule.tpl")}