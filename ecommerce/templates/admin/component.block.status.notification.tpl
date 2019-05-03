{if !empty($additionalFieldName)}
    {$fieldName = $additionalFieldName}
{/if}
{if $orderStatus !== 'undefined'}
    <a href="{$element->URL}id:{$element->id}/action:sendInvoice/invoiceType:{$fieldName}/statusType:{$orderStatus}/" class="button">
        {translations name="{$structureType}.invoice_send"}
    </a>
{/if}

