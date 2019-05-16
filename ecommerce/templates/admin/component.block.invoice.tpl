<div class="form_items">
    <span class="form_label">
        {translations name="{$translationGroup}.{strtolower($fieldName)}"}:
    </span>
    <div class="form_field invoice_num">
        {if $form->getElementProperty("{$fieldName}File")}
            <a href="{$element->getPdfDownLoadUrl($fieldName)}">
                {$form->getElementProperty("{$fieldName}Number")}.pdf
            </a>
        {/if}
    </div>
    <div class="form_field">
        <a href="{$element->URL}id:{$element->id}/action:generateInvoice/invoiceType:{$fieldName}/" class="button">
            {translations name="{$structureType}.invoice_generate"}
        </a>
        <a href="{$element->URL}id:{$element->id}/action:sendInvoice/invoiceType:{$fieldName}/" class="button">
            {translations name="{$structureType}.invoice_send"}
        </a>
    </div>
</div>
