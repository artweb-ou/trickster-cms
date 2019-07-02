<div class="invoice_header_block">
	<div class="order_greeting_row">
		{translations name='invoice.dear_payer'} {$data.payerFirstName} {$data.payerLastName},
	</div>
	<div class="order_info_title_row">
			{translations name='invoice.emailsubject_order_status_notification'}
	</div>
	<table class="invoice_content_table">
		<tr>
			<td class="order_cell_label"><div class="order_info_row">{translations name='invoice.order_nr'}</div></td>
			<td class="order_cell_value"><div class="order_info_row">{$data.invoiceNumber}</div></td>
		</tr>
		<tr>
			<td class="order_cell_label"><div class="order_info_row">{translations name='invoice.orderdate'}:</div></td>
			<td class="order_cell_value"><div class="order_info_row">{$data.dateCreated}</div></td>
		</tr>
		<tr>
			<td class="order_cell_label"><div class="order_info_row">{translations name='invoice.order_status'}:</div></td>
			<td class="order_cell_value"><div class="order_info_row">{$data.orderStatusText} {if $data.orderStatus == 'sent'}({$data.deliveryTitle}){/if}</div></td>
		</tr>
	</table>
</div>
<hr class="hr_divider">
<div class="order_bottom order_signature">
	{translations name='invoice.companyaddress'}
</div>
<hr class="hr_divider">
