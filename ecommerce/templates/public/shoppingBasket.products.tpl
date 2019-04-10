<table class='shoppingbasket_table table_component'>
	<thead class="shoppingbasket_table_header">
	<tr>
		<th class='shoppingbasket_table_header_title' colspan='2'>
			{translations name='shoppingbasket.productstable_product'}
		</th>
		<th class='shoppingbasket_table_header_price'>
			{translations name='shoppingbasket.productstable_price'}
		</th>
		<th class='shoppingbasket_table_header_amount'>
			{translations name='shoppingbasket.productstable_amount'}
		</th>
		<th class='shoppingbasket_table_header_totalprice'>
			{translations name='shoppingbasket.productstable_total'}
		</th>
		<th class='shoppingbasket_table_header_remove'>
			{translations name='shoppingbasket.productstable_remove'}
		</th>
	</tr>
	</thead>
	<tbody class="shoppingbasket_table_rows">
	</tbody>
	<tbody class="shoppingbasket_total shoppingbasket_total_products">
	<tr class="shoppingbasket_total">
		<th class="shoppingbasket_total_title" colspan="4">
			{translations name='shoppingbasket.productstable_productsprice'}:
		</th>
		<td class="shoppingbasket_total_value">
			{$element->shoppingBasket->getProductsPrice()} {$selectedCurrencyItem->symbol}
		</td>
		<td class="shoppingbasket_total_value_spacer"></td>
	</tr>
	</tbody>
	<tbody class="shoppingbasket_services_component">
	<tr>
		<th class="shoppingbasket_services_label">{translations name='shoppingbasket.services'}:</th>
		<td colspan="5" class="shoppingbasket_services_list"></td>
	</tr>
	</tbody>
</table>