{if !empty($additionalFieldName)}
    {$fieldName = $additionalFieldName}
{/if}
{if $orderStatus !== 'undefined'}
    <a href="{$element->URL}id:{$element->id}/action:sendStatusNotification/" class="button">
        {translations name="{$structureType}.send_status_notification"}
    </a>
{/if}

