<div class='shoppingbasket_form_controls'>
	<div>
		<input type="hidden" value="{$shoppingBasketElement->id}" name="id" />
		<input type="hidden" value="submit" name="action" />
	</div>
	<div class="shoppingbasket_form_controls_container">
		<span class="button shoppingbasket_delivery_submit shoppingbasket_form_submit">
			{if !$shoppingBasketElement->isLastStep()}
				<span class='button_text'>{translations name='shoppingbasket.button_proceed'}</span>
			{else}
				<span class='button_text'>{translations name='shoppingbasket.button_checkout'}</span>
			{/if}
		</span>
	</div>
</div>