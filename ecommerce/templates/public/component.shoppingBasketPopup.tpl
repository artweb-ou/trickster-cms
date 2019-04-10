<div class="shoppingbasket_popup popup_component">
	<div class="shoppingbasket_popup_products"></div>
	<div class="shoppingbasket_popup_total">
		<span class="shoppingbasket_popup_total_label">{translations name='shoppingbasketpopup.total'}:</span>
		<span class="shoppingbasket_popup_total_value"></span>
	</div>
	<div class="popup_component_controls">
		<a class="button popup_component_button" href="{$shoppingBasket->URL}">
			<span class="button_text">{translations name='shoppingbasketpopup.checkout'}</span>
		</a>
	</div>
	{$notice = {translations name='shoppingbasketpopup.notice' loggable=false required=false}}
	{if $notice}
		<div class="popup_component_notice">
			{$notice}
		</div>
	{/if}
	<div class="shoppingbasket_popup_closer popup_component_close"></div>
</div>