{if isset($shoppingBasket)}
	<a href='{$shoppingBasket->URL}' class='shoppingbasket_status shoppingbasket_status_header{if $element->floating} shoppingbasket_status_floating{/if}'>
		<span class="shoppingbasket_status_main">
			<span class="shoppingbasket_status_icon"></span>
			<span class="shoppingbasket_status_main_text">
				<span class='shoppingbasket_status_empty'>{translations name='shoppingbasket.status_empty'}</span>
				<span class='shoppingbasket_status_amount'>{translations name='shoppingbasket.status_amount'}</span>
				<span class='shoppingbasket_status_price'>
					{translations name='shoppingbasket.status_totalsum'}: <span class='shoppingbasket_status_price_value'>{$shoppingBasket->totalPrice}</span>&#xa0;{$selectedCurrencyItem->symbol}
				</span>
			</span>
		</span>
	</a>
	{if $element->popup}
		{include $theme->template('component.shoppingBasketPopup.tpl')}
	{/if}
{/if}