<div class='shoppingbasket_form_conditions'>
    <div class="shoppingbasket_form_conditions_content">
        <div class="shoppingbasket_form_heading">{translations name='shoppingbasket.conditions'}</div>
        <div class='shoppingbasket_form_conditions_text'>{$shoppingBasketElement->conditionsText}</div>
    </div>
    <div class='shoppingbasket_form_conditions_controls{if $formErrors.conditions} form_error{/if}'>
        <input type='checkbox' class='checkbox_placeholder' name='{$formNames.conditions}' id="shoppingbasket_form_conditions_checkbox" value='1'
               {if $formData.conditions == '1'}checked='checked'{/if}/><label
                for="shoppingbasket_form_conditions_checkbox" class="shoppingbasket_form_conditions_label checkbox_label">
            {$shoppingBasketElement->getConditionsLabel()}
        </label>
    </div>
</div>