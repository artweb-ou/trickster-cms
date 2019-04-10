<div class="form_items{if $formErrors.rules} form_error{/if}">
    <span class="form_label">
        {translations name="{$structureType}.{$fieldName}"}
    </span>
    <div class="importcalculations_rules_item form_field">
        <input type="hidden" name="{$formNames.rules}"/>
    </div>
</div>