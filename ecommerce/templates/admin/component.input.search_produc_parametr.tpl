<div class="productimporttemplatecolumn_form_parametersearch_container form_items{if $formErrors.productParameterId} form_error{/if}">
    <span class="form_label">
        {translations name="{$structureType}.{$fieldName}"}
    </span>
    <div class="form_field">
        <input class='input_component productimporttemplatecolumn_form_parametersearch' type="text" value="" name=""
               placeholder="{translations name='field.search'}..."/>
        <input class="ajaxitemsearch_resultid" type="hidden" value="{$formData.productParameterId}"
               name="{$formNames.productParameterId}"/>
        <div class="ajaxitemsearch_result">
            <span class="ajaxitemsearch_result_text">{$parameterTitle}</span>
            <div class="ajaxitemsearch_result_remover"></div>
        </div>
    </div>
</div>