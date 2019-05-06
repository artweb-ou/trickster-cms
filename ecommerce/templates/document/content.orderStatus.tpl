<div class="invoice_header_block">
	<table class="invoice_header_table">
		<tr>
			<td class="invoice_header_logo_info">
				<div class="invoice_header_logo">
					{if empty($logo)}
						{$logo = $theme->getImageUrl("logo.png")}
					{/if}
					<img class="logo_image" src="{$logo}">
				</div>
			</td>
			<td class="invoice_header_invoice_info">
				<div class="order_greeting_row">
					{translations name='labels.dear_payer'} {$data.payerFirstName} {$data.payerLastName},
				</div>
				<div class="order_info_title_row">
						{translations name='invoice.emailsubject_order_status_notification'}
				</div>
			</td>
		</tr>
	</table>
	<table class="invoice_content_table">
		<tr>
			<td class="order_cell_label"><div class="order_info_row">{translations name='labels.order_nr'}</div></td>
			<td class="order_cell_value"><div class="order_info_row">{$data.orderNumber}</div></td>
		</tr>
		<tr>
			<td class="order_cell_label"><div class="order_info_row">{translations name='invoice.orderdate'}:</div></td>
			<td class="order_cell_value"><div class="order_info_row">{$data.dateCreated}</div></td>
		</tr>
		<tr>
			<td class="order_cell_label"><div class="order_info_row">{translations name='labels.order_status'}:</div></td>
			<td class="order_cell_value"><div class="order_info_row">{$data.orderStatusText} {if $data.orderStatus == 'sent'}({$data.deliveryTitle}){/if}</div></td>
		</tr>
	</table>
</div>
<hr class="hr_divider">
<div class="order_bottom order_signature">
	{translations name='invoice.companyaddress'}
</div>
<hr class="hr_divider">
