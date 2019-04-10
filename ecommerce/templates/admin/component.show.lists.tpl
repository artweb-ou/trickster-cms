{if $list = $form->callElementMethod($item.method)}
    {include file=$theme->template('component.show.heading.tpl') fieldName = $fieldName}
    {foreach $list as $listElement}
        {assign var='listFormData' value=$listElement->getFormData()}
        {assign var='listFormErrors' value=$listElement->getFormErrors()}
        {assign var='listFormNames' value=$listElement->getFormNames()}

        <div class="form_items{if $listFormErrors.value} form_error{/if}">
            <span class="form_label">
                {$listElement->title}
            </span>
            <div class="form_field">
                <input class='input_component' type="text" value="{$listFormData.value}" name="{$listFormNames.value}"/>
                {include file=$theme->template('component.form_help.tpl') structureType=$element->structureType name="input"}
            </div>
        </div>
    {/foreach}
{/if}