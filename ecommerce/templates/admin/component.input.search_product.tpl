<div class="form_items{if $formErrors.$fieldName} form_error{/if}">
    <span class="form_label">
        {translations name="{$structureType}.{$fieldName}"}
    </span>
    <div class="form_field">
        {include file=$theme->template('component.ajaxitemsearch.tpl')}
    </div>
</div>