<div class="shoppingbasket_form_block">
	<div class='shoppingbasket_receiver_data'>
		<div class='shoppingbasket_form_heading'>{translations name='shoppingbasket.receiverdata'}:</div>
		<table class='form_table shoppingbasket_delivery_form'>
			<tbody class='form_table shoppingbasket_delivery_form_controls'>
			<tr class="shoppingbasket_delivery_form_country">
				<td class='form_label'>
					{translations name='shoppingbasket.country'}:
				</td>
				<td class='form_star'></td>
				<td class='form_field'></td>
				<td class='form_extra'></td>
			</tr>
			<tr class="shoppingbasket_delivery_form_cities">
				<td class='form_label'>
					{translations name='shoppingbasket.city'}:
				</td>
				<td class='form_star'></td>
				<td class='form_field '></td>
				<td class='form_extra'></td>
			</tr>
			<tr class="shoppingbasket_delivery_form_deliverytype">
				<td class='form_label'>
					{translations name='shoppingbasket.delivery'}:
				</td>
				<td class='form_star'></td>
				<td class='form_field'></td>
				<td class='form_extra'></td>
			</tr>
			<tr>
				<td class="form_spacer"></td>
			</tr>
			</tbody>
			<tbody class='form_table shoppingbasket_delivery_form_data'>
			</tbody>
			<tbody>
			<tr>
				<td></td>
				<td></td>
				<td class="shoppingbasket_payer_data_controls form_field">
					<input type='checkbox' class='checkbox_placeholder' name='{$formNames.receiverIsPayer}' id="shoppingbasket_payer_data_checkbox" value='1' {if $formData.receiverIsPayer}checked='checked'{/if}/>
					<label for="shoppingbasket_payer_data_checkbox">{translations name='shoppingbasket.receiverispayer'}</label>
				</td>
			</tr>
			</tbody>
		</table>
		<input type="hidden" class="shoppingbasket_delivery_form_fieldsname" value="{$shoppingBasketElement->getFormNamesBase()}" />
	</div>
	<div class='shoppingbasket_payer_data'>
		<div class='shoppingbasket_form_heading'>{translations name='shoppingbasket.payerdata'}:</div>
		<table class='form_table shoppingbasket_delivery_form'>
			<tr class='{if $formErrors.payerCompany} form_error{/if}'>
				<td class='form_label'>
					{translations name='shoppingbasket.form_company'}:
				</td>
				<td class='form_star'></td>
				<td class='form_field'>
					<input class="input_component" type="text" value="{$formData.payerCompany}" name="{$formNames.payerCompany}" />
				</td>
				<td class='form_extra'></td>
			</tr>
			<tr class='{if $formErrors.payerFirstName} form_error{/if}'>
				<td class='form_label'>
					{translations name='shoppingbasket.form_firstname'}:
				</td>
				<td class='form_star'>*</td>
				<td class='form_field'>
					<input class="input_component" type="text" value="{$formData.payerFirstName}" name="{$formNames.payerFirstName}" />
				</td>
				<td class='form_extra'></td>
			</tr>
			<tr class='{if $formErrors.payerLastName} form_error{/if}'>
				<td class='form_label'>
					{translations name='shoppingbasket.form_lastname'}:
				</td>
				<td class='form_star'>*</td>
				<td class='form_field'>
					<input class="input_component" type="text" value="{$formData.payerLastName}" name="{$formNames.payerLastName}" />
				</td>
				<td class='form_extra'></td>
			</tr>
			<tr class='{if $formErrors.payerEmail} form_error{/if}'>
				<td class='form_label'>
					{translations name='shoppingbasket.form_email'}:
				</td>
				<td class='form_star'>*</td>
				<td class='form_field'>
					<input class="input_component" type="text" value="{$formData.payerEmail}" name="{$formNames.payerEmail}" />
				</td>
				<td class='form_extra'></td>
			</tr>
			<tr class='{if $formErrors.payerPhone} form_error{/if}'>
				<td class='form_label'>
					{translations name='shoppingbasket.form_phone'}:
				</td>
				<td class='form_star'>*</td>
				<td class='form_field'>
					<input class="input_component" type="text" value="{$formData.payerPhone}" name="{$formNames.payerPhone}" />
				</td>
				<td class='form_extra'></td>
			</tr>
			<tr class='{if $formErrors.payerAddress} form_error{/if}'>
				<td class='form_label'>
					{translations name='shoppingbasket.form_address'}:
				</td>
				<td class='form_star'></td>
				<td class='form_field'>
					<input class="input_component" type="text" value="{$formData.payerAddress}" name="{$formNames.payerAddress}" />
				</td>
				<td class='form_extra'></td>
			</tr>
			<tr class='{if $formErrors.payerCity} form_error{/if}'>
				<td class='form_label'>
					{translations name='shoppingbasket.form_city'}:
				</td>
				<td class='form_star'></td>
				<td class='form_field'>
					<input class="input_component" type="text" value="{$formData.payerCity}" name="{$formNames.payerCity}" />
				</td>
				<td class='form_extra'></td>
			</tr>
			<tr class='{if $formErrors.payerPostIndex} form_error{/if}'>
				<td class='form_label'>
					{translations name='shoppingbasket.form_postindex'}:
				</td>
				<td class='form_star'></td>
				<td class='form_field'>
					<input class="input_component" type="text" value="{$formData.payerPostIndex}" name="{$formNames.payerPostIndex}" />
				</td>
				<td class='form_extra'></td>
			</tr>
			<tr class='{if $formErrors.payerCountry} form_error{/if}'>
				<td class='form_label'>
					{translations name='shoppingbasket.form_country'}:
				</td>
				<td class='form_star'></td>
				<td class='form_field'>
					<input class="input_component" type="text" value="{$formData.payerCountry}" name="{$formNames.payerCountry}" />
				</td>
				<td class='form_extra'></td>
			</tr>
		</table>
	</div>
</div>