{if $shoppingBasketElement->hasPromoDiscounts()}
<div class="shoppingbasket_promocode shoppingbasket_aside">
	<h1 class='shoppingbasket_form_heading'>{translations name='shoppingbasket.promocode'}:</h1>
	<table class='shoppingbasket_promocode_form form_table shoppingbasket_delivery_form'>
		<tr class=''>
			<td class='form_label'>
				{translations name='shoppingbasket.form_promocode'}:
			</td>
			<td class='form_star'></td>
			<td class='form_field'>
				<input class="input_component shoppingbasket_promocode_input" type="text" value="" />
			</td>
			<td class='form_extra'></td>
		</tr>
		<tr class=''>
			<td class='form_label'></td>
			<td class='form_star'></td>
			<td class='form_field'>
				<input class="button shoppingbasket_promocode_button" type="button" value="{translations name='shoppingbasket.form_promocode_submit'}"/>
			</td>
			<td class='form_extra'></td>
		</tr>
	</table>
	<div class="shoppingbasket_promocode_status">
		<h3 class="shoppingbasket_promocode_status_title"></h3>
		<input class="button shoppingbasket_promocode_status_reset" type="button" value="{translations name='shoppingbasket.form_promocode_reset'}"/>
	</div>
</div>
{/if}