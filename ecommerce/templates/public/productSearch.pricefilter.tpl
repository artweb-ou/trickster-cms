{if $range = $filter->getRange()}
	{$selectedRange = $filter->getSelectedRange()}
	<div class="productsearch_field productsearch_pricefilter products_filter_price">{stripdomspaces}
			<div class="productsearch_pricefilter_field">				
				<input class="input_component" name="min" value="{$selectedRange.0}" />
				<span class="productsearch_pricefilter_field_currency">{$selectedCurrencyItem->symbol}</span>
			</div>
			<div class="productsearch_pricefilter_field">
				<input class="input_component" name="max" value="{$selectedRange.1}" />
				<span class="productsearch_pricefilter_field_currency">{$selectedCurrencyItem->symbol}</span>
			</div>
		{/stripdomspaces}
		<script>
			window.priceRange = {$range|json_encode};
			window.selectedPriceRange = {$selectedRange|json_encode};
		</script>
	</div>
{/if}