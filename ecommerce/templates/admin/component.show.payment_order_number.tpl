{if $element->hasActualStructureInfo()}
    <div class="form_items">
        <span class="form_label">
            {translations name="label.paymentnumber"}:
        </span>
        <div class="form_field invoice_num">
            {$element->id}
        </div>
    </div>
{/if}
{if $orderElement}
    <div class="form_items">
        <span class="form_label">
            <b>{translations name='field.order_number'}:</b>
        </span>
        <div class="form_field invoice_num">
            <a href="{$orderElement->URL}">
                {$orderElement->getInvoiceNumber()}
            </a>
        </div>
    </div>
{/if}