<table class='order_form_table'>
    {assign var="orderElement" value=$element->getOrderElement()}
	{if $orderElement->invoiceFile}
		<tr>
			<td>
				<b>PDF:</b>
			</td>
			<td>
				<a href="{$orderElement->getPdfDownLoadUrl()}">{$orderElement->invoiceNumber}.pdf</a>
			</td>
		</tr>
	{/if}
	<tr>
		<td>
			<b>{translations name='label.paymentnumber'}:</b>
		</td>
		<td>
			<b>{$element->id}</b>
		</td>
	</tr>
	<tr>
		<td>
			<b>{translations name='label.order_number'}:</b>
		</td>
		<td>
			{$orderElement->orderNumber}
		</td>
	</tr>
	<tr>
		<td>
			<b>{translations name='label.user'}:</b>
		</td>
		<td>
			{$element->userElement->userName}
		</td>
	</tr>
	<tr>
		<td>
			<b>{translations name='label.payeraccount'}:</b>
		</td>
		<td>
			{$element->account}
		</td>
	</tr>
	<tr>
		<td>
			<b>{translations name='label.accountowner'}:</b>
		</td>
		<td>
			{$element->payer}
		</td>
	</tr>
	<tr>
		<td>
			<b>{translations name='label.bank'}:</b>
		</td>
		<td>
			{$element->bank}
		</td>
	</tr>
	<tr>
		<td>
			<b>{translations name='label.paymentamount'}:</b>
		</td>
		<td>
			{$element->amount} {$element->currency}
		</td>
	</tr>
	<tr>
		<td>
			<b>{translations name='label.date'}:</b>
		</td>
		<td>
			{$element->date}
		</td>
	</tr>
	<tr>
		<td>
			<b>{translations name='label.status'}:</b>
		</td>
		<td>
			{$element->statusText}
		</td>
	</tr>
</table>