<div class="shoppingbasket_paymentmethods">
	<h2 class="shoppingbasket_paymentmethods_title">
		{translations name='shoppingbasket.checkout_choosepayment'}
	</h2>
	{if $formErrors.paymentMethodId}
		<div class="shoppingbasket_paymentmethods_error">
			{translations name='shoppingbasket.payment_method_not_selected'}
		</div>
	{/if}
	<div class="shopping_basket_selection_paymentmethods_options">
		{if $formErrors.paymentMethodId}{/if}
		{$paymentMethods = $shoppingBasketElement->getAvailablePaymentMethods()}
		{foreach $paymentMethods as $method}{$fieldId = "shoppingbasket_paymentmethod_{$method->id}"}<label class="shoppingbasket_paymentmethod" for="{$fieldId}" title="{$method->title}">
			<input type="radio" {if $method@first}checked="checked"{/if} class="shoppingbasket_paymentmethod_radio radio_holder" name="{$formNames.paymentMethodId}" value="{$method->id}"{if $formData.paymentMethodId == $method->id} checked="checked"{/if} id="{$fieldId}">{if $method->image}<img class="shoppingbasket_paymentmethod_image" src='{$controller->baseURL}image/type:basketPaymentMethod/id:{$method->image}/filename:{$method->originalName}' alt="{$method->title}"/>
				{else}<span class="shoppingbasket_paymentmethod_text">{$method->title}</span>
				{/if}
			</label>{/foreach}
	</div>
</div>