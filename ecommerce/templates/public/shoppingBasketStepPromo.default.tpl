{if $shoppingBasketElement->hasPromoDiscounts()}
<div class="shoppingbasket_promocode shoppingbasket_aside">
	<div class='shoppingbasket_form_heading'>{translations name='shoppingbasket.promocode'}</div>
	<table class='shoppingbasket_promocode_form form_table'>
		<tr>
			<td class='form_label'>
				{translations name='shoppingbasket.form_promocode'}:
			</td>
			<td class='form_star'></td>
			<td class='form_field'>
				<input class="input_component shoppingbasket_promocode_input" type="text" value="" />
			</td>
		</tr>
		<tr>
			<td class='form_label'></td>
			<td class='form_star'></td>
			<td class='form_field'>
				<input class="button shoppingbasket_promocode_button" type="button" value="{translations name='shoppingbasket.form_promocode_submit'}"/>
			</td>
		</tr>
	</table>
	<div class="shoppingbasket_promocode_status">
		<div class="shoppingbasket_promocode_status_title"></div>
		<input class="button shoppingbasket_promocode_status_reset" type="button" value="{translations name='shoppingbasket.form_promocode_reset'}"/>
	</div>
</div>
{/if}