{if $shoppingBasketElement->hasPromoDiscounts()}
<div class="shoppingbasket_promocode shoppingbasket_aside">
	<div class='shoppingbasket_form_heading'>{translations name='shoppingbasket.promocode'}:</div>
	<table class='shoppingbasket_promocode_form form_table shoppingbasket_delivery_form'>
		<tr class=''>
			<td class='form_star'></td>
			<td class='form_field'>
				<input placeholder="{translations name='shoppingbasket.form_promocode'}" class="input_component shoppingbasket_promocode_input" type="text" value="" />
			</td>
			<td class='form_extra'></td>
		</tr>
		<tr class=''>
			<td class='form_star'></td>
			<td class='form_field text_center'>
				<input class="button shoppingbasket_promocode_button" type="button" value="{translations name='shoppingbasket.form_promocode_submit'}"/>
			</td>
			<td class='form_extra'></td>
		</tr>
	</table>
	<div class="shoppingbasket_promocode_status">
		<div class="shoppingbasket_promocode_status_title"></div>
		<input class="button shoppingbasket_promocode_status_reset" type="button" value="{translations name='shoppingbasket.form_promocode_reset'}"/>
	</div>
</div>
{/if}