	<table class='shoppingbasket_checkout_table shoppingbasket_table table_component'>
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
			</tr>
		</thead>
		<tbody class="shoppingbasket_table_rows">
			{foreach from=$shoppingBasketElement->shoppingBasket->getProductsList() item=product}
			<tr class='shoppingbasket_table_item shoppingbasket_table_{$product->basketProductId}'>
				<td class='shoppingbasket_table_image_container'>
					{if $product->image != ""}
						<img class="shoppingbasket_table_image" src='{$product->image}' alt="{$product->title}"/>
					{/if}
				</td>
				<td class='shoppingbasket_table_title'>
					<div class="shoppingbasket_table_title">{$product->title}</div>
					<div class="shoppingbasket_table_code">{translations name='shoppingbasket.productstable_productcode'} {$product->code}</div>
					<div class="shoppingbasket_table_description">
						{if is_array($product->variation)}
							{foreach $product->variation as $variation}
								<p>
									{$variation}
								</p>
							{/foreach}
						{else}
							{$product->variation}
						{/if}
					</div>
				</td>
				<td class='shoppingbasket_table_price'>
					{if !$product->emptyPrice}
						{$product->price|string_format:'%01.2f'} {$selectedCurrencyItem->symbol} {if $product->unit}/ {$product->unit}{/if}
					{/if}
				</td>
				<td class='shoppingbasket_table_amount'>
					{$product->amount}
				</td>
				<td class='shoppingbasket_table_totalprice'>
					{if !$product->emptyPrice}
						{$product->totalPrice|string_format:'%01.2f'} {$selectedCurrencyItem->symbol}
					{/if}
				</td>
			</tr>
			{/foreach}
		</tbody>
		{if $shoppingBasketElement->displayTotals()}
			<tbody class="shoppingbasket_totals">
				<tr class="shoppingbasket_total shoppingbasket_total_productsfullprice">
					<th class="shoppingbasket_total_title" colspan="4">{translations name='shoppingbasket.productstable_productsprice'}:</th>
					<td class="shoppingbasket_total_value">
						{$shoppingBasketElement->shoppingBasket->getProductsPrice()|string_format:'%01.2f'} {$selectedCurrencyItem->symbol}
					</td>
				</tr>
				{foreach from=$shoppingBasketElement->shoppingBasket->getDeliveryTypesList() item=deliveryType}
					{if $deliveryType->id == $shoppingBasketElement->shoppingBasket->getSelectedDeliveryTypeId()}
						{if $deliveryType->getPrice() !== ""}
						<tr class="shoppingbasket_total shoppingbasket_total_delivery">
							<th class="shoppingbasket_total_title" colspan="4">
								{$deliveryType->title}
							</th>
							<td class="shoppingbasket_total_value">
								{$deliveryType->getPrice()|string_format:'%01.2f'} {$selectedCurrencyItem->symbol}
							</td>
						</tr>
						{/if}
					{/if}
				{/foreach}
				{foreach from=$shoppingBasketElement->shoppingBasket->getDiscountsList() item=discount}
					<tr class="shoppingbasket_total shoppingbasket_total_discount">
						<th class="shoppingbasket_total_title" colspan="4">
							{$discount->title}
						</th>
						<td class="shoppingbasket_total_value">
							{$discount->getAllDiscountsAmount()|string_format:'%01.2f'} {$selectedCurrencyItem->symbol}
						</td>
					</tr>
				{/foreach}
				{if $configManager->get('main.displayVat')}
					<tr class="shoppingbasket_total shoppingbasket_total_vatless">
						<th class="shoppingbasket_total_title" colspan="4">
							{translations name='shoppingbasket.vatlesstotalprice'}:
						</th>
						<td class="shoppingbasket_total_value">
							{$shoppingBasketElement->shoppingBasket->getVatLessTotalPrice()|string_format:'%01.2f'} {$selectedCurrencyItem->symbol}
						</td>
					</tr>
					<tr class="shoppingbasket_total shoppingbasket_total_vat">
						<th class="shoppingbasket_total_title" colspan="4">
							{translations name='shoppingbasket.vatamount'}:
						</th>
						<td class="shoppingbasket_total_value">
							{$shoppingBasketElement->shoppingBasket->getVatAmount()|string_format:'%01.2f'} {$selectedCurrencyItem->symbol}
						</td>
					</tr>
				{/if}
				<tr class="shoppingbasket_total shoppingbasket_total_total">
					<th colspan="4" class="shoppingbasket_total_title">
						{translations name='shoppingbasket.totalprice'}:
					</th>
					<td class="shoppingbasket_total_value">
						<span class='shoppingbasket_totalprice_value'>{$shoppingBasketElement->shoppingBasket->getTotalPrice()|string_format:'%01.2f'}</span> {$selectedCurrencyItem->symbol}
					</td>
				</tr>
				{if !$configManager->get('main.displayVat')}
					<tr class="shoppingbasket_total shoppingbasket_prices_include_vat_row">
						<td colspan="6" class="shoppingbasket_total_title shoppingbasket_prices_include_vat_title">{translations name="shoppingbasket.pricesincludevat"}</td>
					</tr>
				{/if}
			</tbody>
		{/if}
	</table>
	<div class="shoppingbasket_details_left">
		{if $shoppingBasketElement->shoppingBasket->getSelectedDeliveryType() && $shoppingBasketElement->shoppingBasket->getSelectedDeliveryType()->deliveryFormFields}
			<h1 class='shoppingbasket_form_heading'>{translations name='shoppingbasket.form_receiverdata'}:</h1>
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
	<div class="shoppingbasket_details_right">
		{if !$shoppingBasketElement->receiverIsPayer}
		<h1 class='shoppingbasket_form_heading'>{translations name='shoppingbasket.form_payerdata'}:</h1>
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
	<div class="clearfix"></div>