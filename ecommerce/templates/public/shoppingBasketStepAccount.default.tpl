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