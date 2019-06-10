<div class='shoppingbasket_form_controls'>
    {if $element->isLastStep()}
        {include file=$theme->template('shoppingBasket.conditions.tpl')}
    {/if}
	<div>
		<input type="hidden" value="{$element->id}" name="id" />
		<input type="hidden" value="submit" name="action" />
	</div>
	<div class="shoppingbasket_form_controls_container">
		<span class="button shoppingbasket_delivery_submit shoppingbasket_form_submit">
			{if !$element->isLastStep()}
				<span class='button_text'>{translations name='shoppingbasket.button_proceed'}</span>
			{else}
				<span class='button_text'>{translations name='shoppingbasket.button_checkout'}</span>
			{/if}
		</span>
	</div>
</div>