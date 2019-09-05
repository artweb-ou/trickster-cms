<div class="product_details_amount_block display_flex{if !empty($componentClass)} {$componentClass}{/if}" data-block-type="amount_control_block">
	<span data-amount-type="minus" class="button {if !empty($inputsClass)} {$inputsClass}_amount_minus {$inputsClass}_details_amount{/if}"><span class="button_icon"></span></span>
	<!--suppress HtmlFormInputWithoutLabel -->
	<input data-amount-type="input" class='input_component {if !empty($inputsClass)} {$inputsClass}_amount_input{/if}' type="text" value="{$inputAmount}" />
	<span data-amount-type="plus" class="button {if !empty($inputsClass)} {$inputsClass}_amount_plus {$inputsClass}_details_amount{/if}"><span class="button_icon"></span></span>
</div>
