{if $element->getOrderFields()}
    {include file=$theme->template('component.show.heading.tpl') fieldName = $fieldName}
    {foreach from=$element->getOrderFields() item=fieldElement}
        {assign var='fieldFormData' value=$fieldElement->getFormData()}
        {assign var='fieldFormErrors' value=$fieldElement->getFormErrors()}
        {assign var='fieldFormNames' value=$fieldElement->getFormNames()}

        <div class="form_items{if $fieldFormErrors.value} form_error{/if}">
            <span class="form_label">
                {$fieldElement->title}
            </span>
            <div class="form_field">
                {if $element->getFieldType($fieldElement->fieldId) == 'textarea'}
                    <textarea name="{$fieldFormNames.value}"
                              class='textarea_component'>{$fieldFormData.value}</textarea>
                {else}
                    <input class='input_component' type="text" value="{$fieldFormData.value}"
                           name="{$fieldFormNames.value}"/>
                {/if}
                {include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="input"}
            </div>
        </div>
    {/foreach}
{/if}