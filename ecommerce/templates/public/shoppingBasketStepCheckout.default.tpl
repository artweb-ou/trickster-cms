<div class="shoppingbasket_checkout_receiver">
	{if $shoppingBasketElement->shoppingBasket->getSelectedDeliveryType() && $shoppingBasketElement->shoppingBasket->getSelectedDeliveryType()->deliveryFormFields}
		<div class='shoppingbasket_form_heading'>{translations name='shoppingbasket.form_receiverdata'}:</div>
		<table class='shoppingbasket_checkout_table'>
			{foreach from=$shoppingBasketElement->shoppingBasket->getSelectedDeliveryType()->deliveryFormFields item=deliveryField}
				<tr>
					<td class='shoppingbasket_checkout_label'>
						{$deliveryField.title}:
					</td>
					<td class='shoppingbasket_checkout_value'>
						{nl2br($deliveryField.value)}
					</td>
				</tr>
			{/foreach}

		</table>
	{/if}
</div>
<div class="shoppingbasket_checkout_payer">
	{if !$shoppingBasketElement->receiverIsPayer}
	<div class='shoppingbasket_form_heading'>{translations name='shoppingbasket.form_payerdata'}:</div>
	<table class='shoppingbasket_checkout_table'>
		{if $shoppingBasketElement->payerCompany}
			<tr>
				<td class='shoppingbasket_checkout_label'>
					{translations name='shoppingbasket.form_company'}:
				</td>
				<td class='shoppingbasket_checkout_value'>
					{$shoppingBasketElement->payerCompany}
				</td>
			</tr>
		{/if}
		{if $shoppingBasketElement->payerFirstName}
			<tr>
				<td class='shoppingbasket_checkout_label'>
					{translations name='shoppingbasket.form_firstname'}:
				</td>
				<td class='shoppingbasket_checkout_value'>
					{$shoppingBasketElement->payerFirstName}
				</td>
			</tr>
		{/if}
		{if $shoppingBasketElement->payerLastName}
			<tr>
				<td class='shoppingbasket_checkout_label'>
					{translations name='shoppingbasket.form_lastname'}:
				</td>
				<td class='shoppingbasket_checkout_value'>
					{$shoppingBasketElement->payerLastName}
				</td>
			</tr>
		{/if}
		{if $shoppingBasketElement->payerEmail}
			<tr>
				<td class='shoppingbasket_checkout_label'>
					{translations name='shoppingbasket.form_email'}:
				</td>
				<td class='shoppingbasket_checkout_value'>
					{$shoppingBasketElement->payerEmail}
				</td>
			</tr>
		{/if}
		{if $shoppingBasketElement->payerPhone}
			<tr>
				<td class='shoppingbasket_checkout_label'>
					{translations name='shoppingbasket.form_phone'}:
				</td>
				<td class='shoppingbasket_checkout_value'>
					{$shoppingBasketElement->payerPhone}
				</td>
			</tr>
		{/if}
		{if $shoppingBasketElement->payerAddress}
			<tr>
				<td class='shoppingbasket_checkout_label'>
					{translations name='shoppingbasket.form_address'}:
				</td>
				<td class='shoppingbasket_checkout_value'>
					{$shoppingBasketElement->payerAddress}
				</td>
			</tr>
		{/if}
		{if $shoppingBasketElement->payerCity}
			<tr>
				<td class='shoppingbasket_checkout_label'>
					{translations name='shoppingbasket.form_city'}:
				</td>
				<td class='shoppingbasket_checkout_value'>
					{$shoppingBasketElement->payerCity}
				</td>
			</tr>
		{/if}
		{if $shoppingBasketElement->payerPostIndex}
			<tr>
				<td class='shoppingbasket_checkout_label'>
					{translations name='shoppingbasket.form_postindex'}:
				</td>
				<td class='shoppingbasket_checkout_value'>
					{$shoppingBasketElement->payerPostIndex}
				</td>
			</tr>
		{/if}
		{if $shoppingBasketElement->payerCountry}
			<tr>
				<td class='shoppingbasket_checkout_label'>
					{translations name='shoppingbasket.form_country'}:
				</td>
				<td class='shoppingbasket_checkout_value'>
					{$shoppingBasketElement->payerCountry}
				</td>
			</tr>
		{/if}
	</table>
	{/if}
</div>