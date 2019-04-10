<div class="product_details_prices">
	{if $element->getOldPrice()}
		<div class="product_details_oldprice">
			<span class="product_details_oldprice_label">{translations name='product.oldprice'}:&#160;</span><span class="product_details_oldprice_amount">{$element->getOldPrice()} {$selectedCurrencyItem->symbol}</span>{if $element->getUnit()}&#160;/&#160;{$element->getUnit()}{/if}
		</div>
	{/if}
	{if $element->getPrice(false) && !$element->isEmptyPrice()}
		<div class="product_details_price">
			<span class="product_details_price_label">{translations name='product.price'}:&#160;</span><span class="product_details_price_value"><span class="product_details_price_digits">{$element->getPrice()}</span>&#160;<span class="product_details_price_currency">{$selectedCurrencyItem->symbol}</span>{if $element->getUnit()}&#160;/&#160;{$element->getUnit()}{/if}</span>
		</div>
	{/if}
</div>