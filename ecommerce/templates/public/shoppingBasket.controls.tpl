<div class='shoppingbasket_form_controls'>
	<div class='shoppingbasket_form_conditions'>
		<div class="shoppingbasket_form_conditions_content">
			<div class="shoppingbasket_form_heading">{translations name='shoppingbasket.conditions'}</div>
			<div class='shoppingbasket_form_conditions_text'>{$element->conditionsText}</div>
		</div>
		<div class='shoppingbasket_form_conditions_controls{if $formErrors.conditions} form_error{/if}'>
			<input type='checkbox' class='checkbox_placeholder' name='{$formNames.conditions}' id="shoppingbasket_form_conditions_checkbox" value='1' {if $formData.conditions == '1'}checked='checked'{/if}/>
			<label for="shoppingbasket_form_conditions_checkbox" class="shoppingbasket_form_conditions_label">
				{$element->getConditionsLabel()}
			</label>
		</div>
	</div>
	<div>
		<input type="hidden" value="{$element->id}" name="id" />
		<input type="hidden" value="submit" name="action" />
	</div>
	<div class="shoppingbasket_form_controls_container">
		<a href="#" class="button shoppingbasket_delivery_submit shoppingbasket_form_submit">
			<span class='button_left'></span>
			<span class='button_right'></span>
			<span class='button_center'></span>
			{if $element->isCheckoutStepEnabled()}
				<span class='button_text'>{translations name='shoppingbasket.button_proceed'}</span>
			{else}
				<span class='button_text'>{translations name='shoppingbasket.button_checkout'}</span>
			{/if}
		</a>
	</div>
</div>