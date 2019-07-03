<div class="form_items">
    <span class="form_label">
        {translations name="{$translationGroup}.{strtolower($fieldName)}"}:
    </span>
	<div class="form_field invoice_num">
		<a href="{$element->URL}id:{$element->id}/action:generateWaybill/">
            {$element->orderNumber}.pdf
		</a>
	</div>
</div>
