<div class="invoice_header_block">
	<table class="invoice_header_table">
		<tr>
			<td class="invoice_header_logo_info">
				<div class="invoice_header_logo">
                    {if empty($logo)}
                        {$logo = $theme->getImageUrl("logo.png")}
                    {/if}
					<img class="waybill_logo_image" src="{$logo}">
				</div>
			</td>
			<td class="invoice_header_invoice_info">
				<div class="invoice_header_order_date">
					{translations name='orderslist.date'}: {date("d.m.Y")}
				</div>
			</td>
		</tr>
	</table>
</div>
<h1 class="order_heading">{translations name='invoice.waybill'} {$data.orderConfirmationNumber}</h1>
<div class="invoice_top_block">
	<table class="invoice_top_table">
		<tr>
			<td class="invoice_top_receiver_info">
                {if $data.receiverFields}
					<div class="invoice_top_to">{translations name='invoice.receiver'}:</div>
					<div class="invoice_top_payer_row">
                        {if !empty($data.receiverFields.company.value)}
                            {$data.receiverFields.company.value} ({$data.receiverFields.firstName.value} {$data.receiverFields.lastName.value})
                        {else}
                            {$data.receiverFields.firstName.value} {$data.receiverFields.lastName.value}
                        {/if}
					</div>
                    {if !empty($data.receiverFields.address.value)}
						<div class="invoice_top_payer_row">
                            {$data.receiverFields.address.value}{if $data.receiverFields.city.value}, {$data.receiverFields.city.value}{/if}{if $data.receiverFields.postIndex.value}, {$data.receiverFields.postIndex.value}{/if}
						</div>
                    {/if}
                    {if !empty($data.receiverFields.country.value)}
						<div class="invoice_top_payer_row">
                            {$data.receiverFields.country.value}
						</div>
                    {/if}
                    {if !empty($data.receiverFields.email.value)}
						<div class="invoice_top_payer_row">
                            {$data.receiverFields.email.value}
						</div>
                    {/if}
                    {if !empty($data.receiverFields.phone.value)}
						<div class="invoice_top_payer_row">
                            {$data.receiverFields.phone.value}
						</div>
                    {/if}
                    {if !empty($data.receiverFields.other)}
                        {foreach from=$data.receiverFields.other item=field}
							<div class="invoice_top_payer_row">{nl2br($field.value)}</div>
                        {/foreach}
                    {/if}
                {/if}
			</td>
			<td class="invoice_top_delimiter"></td>
		</tr>
	</table>
</div>
<table class="order_productstable">
	<tbody class="order_productstable_head">
	<tr>
		<td>
            {translations name='invoice.code'}
		</td>
		<td>
            {translations name='invoice.product'}
		</td>
		<td>
            {translations name='invoice.quantity'}
		</td>
	</tr>
	</tbody>
	<tbody class="order_productstable_data">
    {foreach from=$data.addedProducts item=productInfo}
		<tr>
			<td>
                {$productInfo.code}
			</td>
			<td>
                {$productInfo.title} {if $productInfo.variation}({$productInfo.variation}){/if}
			</td>
			<td>
                {$productInfo.amount}
			</td>
		</tr>
    {/foreach}
	</tbody>
</table>

<table class="waybill_bottom_table" style="width: 100%;margin-top: 100px;border: 1px solid #e0e0e0;border-collapse: collapse">
	<tr>
		<td style="vertical-align: top; padding: .5em 1.5em; text-align: left; border-collapse: collapse;border-right: 1px solid #e0e0e0;width: 50%;">
			<div>{translations name='waybill.from'}</div>

		</td>
		<td style="vertical-align: top; padding: .5em 1.5em; text-align: left; border-collapse: collapse;width: 50%; ">
			<span style="text-align: left">{translations name='waybill.payer'}</span>
		</td>
	</tr>
	<tr>
		<td style="vertical-align: top; padding: .5em 1.5em; text-align: left; border-collapse: collapse; border-bottom: 1px solid #e0e0e0;border-right: 1px solid #e0e0e0;">
			<div>Andrese Klaasistuudio OÜ</div>
		</td>
		<td style="vertical-align: top; padding: .5em 1.5em; text-align: left; border-collapse: collapse; border-bottom: 1px solid #e0e0e0;"></td>
	</tr>
	<tr>
		<td style="vertical-align: top; padding: .5em 1.5em; text-align: left; border-collapse: collapse; border-bottom: 1px solid #e0e0e0;border-right: 1px solid #e0e0e0;">
			<span style="text-align: left">{translations name='waybill.signature'}</span></td>
		<td style="vertical-align: top; padding: .5em 1.5em; text-align: left; border-collapse: collapse; border-bottom: 1px solid #e0e0e0;">
			<span style="text-align: left">{translations name='waybill.signature_date'}</span>
		</td>
	</tr>
	<tr class="waybill_reciever_side">
		<td style="padding: .5em 1.5em; border-collapse: collapse;border-right: 1px solid #e0e0e0;">&nbsp;</td>
		<td style="padding: .5em 1.5em; border-collapse: collapse;">&nbsp;</td>
	</tr>
</table>





