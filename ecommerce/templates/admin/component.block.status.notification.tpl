{if !empty($additionalFieldName)}
    {$fieldName = $fieldName|cat:"."|cat:$additionalFieldName}
{/if}
{if $orderStatus !== 'undefined'}
    <a href="{$element->URL}id:{$element->id}/action:sendInvoice/invoiceType:{$fieldName}.{$orderStatus}/" class="button">
        {translations name="{$structureType}.invoice_send"}
    </a>
{/if}

