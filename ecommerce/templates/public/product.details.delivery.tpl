{assign "deliveriesInfo" $element->getDeliveryTypesInfo()}
{if $deliveriesInfo}
	<div class="product_details_deliveryoptions subcontentmodule_component">
		<div class="product_details_deliveryoptions_image"></div>
		<div class="product_details_deliveryoptions_title">
			{translations name='product.delivery_options'}:
		</div>

		{foreach $deliveriesInfo as $deliveryOption}
			<div class="product_details_deliveryoptions_item">
				<div class="product_details_deliveryoptions_item_details">
					<div class="product_details_deliveryoptions_item_title">
						{$deliveryOption.element->title}
					</div>
					{if $deliveryOption.minPrice != $deliveryOption.maxPrice}
						{translations name='product.price'}: {$deliveryOption.minPrice} {$selectedCurrencyItem->symbol} - {$deliveryOption.maxPrice} {$selectedCurrencyItem->symbol}
					{else}
						{translations name='product.price'}: {$deliveryOption.minPrice} {$selectedCurrencyItem->symbol}
					{/if}
				</div>
			</div>
		{/foreach}
	</div>
{/if}
