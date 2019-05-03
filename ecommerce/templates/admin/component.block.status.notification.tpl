{if !empty($additionalFieldName)}
    {$fieldName = $fieldName|cat:"."|cat:$additionalFieldName}
{/if}
<a href="{$element->URL}id:{$element->id}/action:sendInvoice/invoiceType:{$fieldName}/" class="button">
    {translations name="{$structureType}.invoice_send"}
</a>
