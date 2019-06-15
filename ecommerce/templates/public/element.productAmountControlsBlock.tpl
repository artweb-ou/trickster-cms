<div class="product_details_amount_block display_flex{if $addItemAmountClass} {$addItemAmountClass}{/if}" data-block-type="amount_control_block">
	<span data-amount-type="minus" class="button {if $additionalClass} {$additionalClass}_amount_minus {$additionalClass}_details_amount{/if}"><span class="button_icon"></span></span>
	<!--suppress HtmlFormInputWithoutLabel -->
	<input data-amount-type="input" class='input_component {if $additionalClass} {$additionalClass}_amount_input{/if}' type="text" value="{$inputAmount}" />
	<span data-amount-type="plus" class="button {if $additionalClass} {$additionalClass}_amount_plus {$additionalClass}_details_amount{/if}"><span class="button_icon"></span></span>
</div>
