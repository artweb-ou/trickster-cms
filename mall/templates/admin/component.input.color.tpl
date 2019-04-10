<div class="form_items">
    <span class="form_label">
        {translations name="{$translationGroup}.{strtolower($fieldName)}"}
    </span>
    <div class="form_field">
        <input class="color input_component" type="text" value="{$formData.$fieldName}" name="{$formNames.$fieldName}" />
        {include file=$theme->template('component.form_help.tpl')}
    </div>
</div>