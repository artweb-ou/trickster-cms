<div class="shoppingbasket_account">
	<div class="shoppingbasket_account_login">
		{if $loginForm = $element->getLoginForm()}
			{include file=$theme->template('login.form.tpl') element=$loginForm}
		{/if}
	</div>
	<div class="shoppingbasket_account_registration">
		{if $currentUser->userName === 'anonymous'}
			{if $registrationForm = $element->getRegistrationForm()}
				{include file=$theme->template('registration.form.tpl') element=$registrationForm}
			{/if}
		{/if}
	</div>
	<div class="clearfix"></div>
</div>

{if $shoppingBasketElement->isAccountStepSkippable()}
	{if $nextStep = $shoppingBasketElement->getNextStep()}
		<div class='shoppingbasket_form_controls'>
			<div class="shoppingbasket_form_controls_container">
				<a class="button" href="{$shoppingBasketElement->URL}step:{$nextStep->structureName}/">
					{translations name='shoppingbasket.skip_account'}
				</a>
			</div>
		</div>
	{/if}
{/if}

